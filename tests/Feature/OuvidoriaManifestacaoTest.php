<?php

namespace Tests\Feature;

use App\Exceptions\FalhaAoArmazenarAnexo;
use App\Models\ManifestacaoOuvidoria;
use App\Models\User;
use App\Services\ArmazenadorDeAnexos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Mockery\MockInterface;
use Tests\TestCase;

class OuvidoriaManifestacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('local');
    }

    private function dadosValidos(array $sobrescritos = []): array
    {
        return [
            'descricao' => 'O atendimento na unidade foi muito demorado e ninguém soube explicar o motivo.',
            ...$sobrescritos,
        ];
    }

    private function registrarComo(User $usuario, string $tipo, array $dados): TestResponse
    {
        return $this->actingAs($usuario)->post(route('ouvidoria.manifestacao.registrar', $tipo), $dados);
    }

    public function test_visitante_nao_autenticado_e_redirecionado_para_entrar(): void
    {
        $this->get(route('ouvidoria.manifestacao.criar', 'reclamacao'))->assertRedirect(route('acesso.index'));
        $this->post(route('ouvidoria.manifestacao.registrar', 'reclamacao'), $this->dadosValidos())
            ->assertRedirect(route('acesso.index'));

        $this->assertSame(0, ManifestacaoOuvidoria::count());
    }

    public function test_tipo_inexistente_devolve_404(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->get('/ouvidoria/manifestar/inexistente')->assertNotFound();
        $this->actingAs($usuario)->post('/ouvidoria/manifestar/inexistente', $this->dadosValidos())->assertNotFound();

        $this->assertSame(0, ManifestacaoOuvidoria::count());
    }

    public function test_formulario_e_exibido_para_cada_tipo_de_manifestacao(): void
    {
        $usuario = User::factory()->create();

        foreach (ManifestacaoOuvidoria::TIPOS as $tipo => $configuracao) {
            $this->actingAs($usuario)
                ->get(route('ouvidoria.manifestacao.criar', $tipo))
                ->assertOk()
                ->assertSee($configuracao['rotulo']);
        }
    }

    public function test_registra_reclamacao_para_o_usuario_logado_com_anexo_no_disco_privado(): void
    {
        $usuario = User::factory()->create();

        $resposta = $this->registrarComo($usuario, 'reclamacao', $this->dadosValidos([
            'orgao_ou_setor' => 'Secretaria de Saúde',
            'local_do_fato' => 'UBS do Centro',
            'anexos' => [UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf')],
        ]));

        $manifestacao = ManifestacaoOuvidoria::with('anexos')->firstOrFail();

        $resposta->assertRedirect(route('ouvidoria.manifestacao.confirmacao', $manifestacao->protocolo));
        $this->assertSame($usuario->id, $manifestacao->user_id);
        $this->assertSame('reclamacao', $manifestacao->tipo);
        $this->assertMatchesRegularExpression('/^OUV-\d{4}-\d{6}$/', $manifestacao->protocolo);
        $this->assertSame('Secretaria de Saúde', $manifestacao->orgao_ou_setor);
        $this->assertSame('UBS do Centro', $manifestacao->local_do_fato);
        $this->assertCount(1, $manifestacao->anexos);
        $this->assertSame('documento.pdf', $manifestacao->anexos->first()->nome_original);
        Storage::disk('local')->assertExists($manifestacao->anexos->first()->caminho);
    }

    public function test_registra_denuncia_com_campos_especificos(): void
    {
        $dataDoFato = now()->subDay()->format('Y-m-d');

        $this->registrarComo(User::factory()->create(), 'denuncia', $this->dadosValidos([
            'data_do_fato' => $dataDoFato,
            'local_do_fato' => 'Prédio da Prefeitura',
            'servidor_ou_setor' => 'Setor de Protocolo',
        ]))->assertSessionHasNoErrors();

        $manifestacao = ManifestacaoOuvidoria::firstOrFail();

        $this->assertSame('denuncia', $manifestacao->tipo);
        $this->assertSame($dataDoFato, $manifestacao->data_do_fato->format('Y-m-d'));
        $this->assertSame('Setor de Protocolo', $manifestacao->servidor_ou_setor);
    }

    public function test_formulario_da_denuncia_nao_oferece_pedido_de_sigilo(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('ouvidoria.manifestacao.criar', 'denuncia'))
            ->assertOk()
            ->assertDontSee('sigilo');
    }

    public function test_campos_que_nao_pertencem_ao_tipo_sao_descartados(): void
    {
        $this->registrarComo(User::factory()->create(), 'sugestao', $this->dadosValidos([
            'local_do_fato' => 'Qualquer lugar',
            'servidor_ou_setor' => 'Qualquer setor',
        ]))->assertSessionHasNoErrors();

        $manifestacao = ManifestacaoOuvidoria::firstOrFail();

        $this->assertNull($manifestacao->local_do_fato);
        $this->assertNull($manifestacao->servidor_ou_setor);
    }

    public function test_aceita_cada_tipo_apenas_com_a_descricao(): void
    {
        $usuario = User::factory()->create();

        foreach (array_keys(ManifestacaoOuvidoria::TIPOS) as $tipo) {
            $this->registrarComo($usuario, $tipo, $this->dadosValidos())->assertSessionHasNoErrors();
        }

        $this->assertSame(count(ManifestacaoOuvidoria::TIPOS), ManifestacaoOuvidoria::count());
    }

    public function test_rejeita_descricao_curta(): void
    {
        $this->registrarComo(User::factory()->create(), 'reclamacao', $this->dadosValidos(['descricao' => 'Muito curta']))
            ->assertSessionHasErrors('descricao');

        $this->assertSame(0, ManifestacaoOuvidoria::count());
    }

    public function test_rejeita_data_do_fato_no_futuro(): void
    {
        $this->registrarComo(User::factory()->create(), 'reclamacao', $this->dadosValidos([
            'data_do_fato' => now()->addDay()->format('Y-m-d'),
        ]))->assertSessionHasErrors('data_do_fato');

        $this->assertSame(0, ManifestacaoOuvidoria::count());
    }

    public function test_rejeita_mais_de_cinco_anexos(): void
    {
        $anexos = array_map(
            fn (int $indice) => UploadedFile::fake()->create("foto{$indice}.jpg", 10, 'image/jpeg'),
            range(1, 6),
        );

        $this->registrarComo(User::factory()->create(), 'reclamacao', $this->dadosValidos(['anexos' => $anexos]))
            ->assertSessionHasErrors('anexos');

        $this->assertSame(0, ManifestacaoOuvidoria::count());
    }

    public function test_rejeita_extensao_de_anexo_nao_aceita(): void
    {
        $this->registrarComo(User::factory()->create(), 'reclamacao', $this->dadosValidos([
            'anexos' => [UploadedFile::fake()->create('programa.exe', 10, 'application/x-msdownload')],
        ]))->assertSessionHasErrors('anexos.0');

        $this->assertSame(0, ManifestacaoOuvidoria::count());
    }

    public function test_aceita_arquivo_ate_o_limite_de_10_mb(): void
    {
        $this->registrarComo(User::factory()->create(), 'reclamacao', $this->dadosValidos([
            'anexos' => [UploadedFile::fake()->create('video.mp4', 10240, 'video/mp4')],
        ]))->assertSessionHasNoErrors();

        $this->assertSame(1, ManifestacaoOuvidoria::count());
    }

    public function test_rejeita_arquivo_acima_de_10_mb(): void
    {
        $this->registrarComo(User::factory()->create(), 'reclamacao', $this->dadosValidos([
            'anexos' => [UploadedFile::fake()->create('video.mp4', 10241, 'video/mp4')],
        ]))->assertSessionHasErrors('anexos.0');

        $this->assertSame(0, ManifestacaoOuvidoria::count());
    }

    public function test_aceita_anexos_que_somam_exatamente_o_limite_total_de_25_mb(): void
    {
        $fotos = array_map(
            fn (int $indice) => UploadedFile::fake()->create("foto{$indice}.jpg", 5120, 'image/jpeg'),
            range(1, 5),
        );

        $this->registrarComo(User::factory()->create(), 'reclamacao', $this->dadosValidos(['anexos' => $fotos]))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, ManifestacaoOuvidoria::count());
    }

    public function test_rejeita_anexos_que_somam_mais_que_o_limite_total_de_25_mb(): void
    {
        $videos = array_map(
            fn (int $indice) => UploadedFile::fake()->create("video{$indice}.mp4", 9000, 'video/mp4'),
            range(1, 3),
        );

        $this->registrarComo(User::factory()->create(), 'reclamacao', $this->dadosValidos(['anexos' => $videos]))
            ->assertSessionHasErrors('anexos');

        $this->assertSame(0, ManifestacaoOuvidoria::count());
    }

    public function test_falha_ao_gravar_anexo_desfaz_a_manifestacao_e_avisa_o_cidadao(): void
    {
        $this->mock(ArmazenadorDeAnexos::class, function (MockInterface $armazenador) {
            $armazenador->shouldReceive('armazenar')
                ->once()
                ->andThrow(FalhaAoArmazenarAnexo::noDiretorio('manifestacoes-ouvidoria/teste'));
            $armazenador->shouldReceive('apagar')->once()->with([]);
        });

        $this->registrarComo(User::factory()->create(), 'denuncia', $this->dadosValidos([
            'anexos' => [UploadedFile::fake()->create('prova.pdf', 100, 'application/pdf')],
        ]))
            ->assertRedirect()
            ->assertSessionHas('erro_geral');

        $this->assertSame(0, ManifestacaoOuvidoria::count());
    }

    public function test_apenas_o_dono_acessa_a_confirmacao(): void
    {
        $dono = User::factory()->create();
        $outroUsuario = User::factory()->create();

        $manifestacao = ManifestacaoOuvidoria::create([
            ...$this->dadosValidos(),
            'user_id' => $dono->id,
            'protocolo' => 'OUV-2026-000001',
            'tipo' => 'elogio',
        ]);

        $rota = route('ouvidoria.manifestacao.confirmacao', $manifestacao->protocolo);

        $this->actingAs($outroUsuario)->get($rota)->assertNotFound();
        $this->actingAs($dono)->get($rota)->assertOk()->assertSee($manifestacao->protocolo)->assertSee('Elogio');
    }
}
