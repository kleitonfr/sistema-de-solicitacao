<?php

namespace Tests\Feature;

use App\Exceptions\FalhaAoArmazenarAnexo;
use App\Models\CategoriaServico;
use App\Models\Servico156;
use App\Models\Solicitacao156;
use App\Models\User;
use App\Services\ArmazenadorDeAnexos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class Servico156SolicitacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('local');
    }

    private function criarCategoria(string $nome = 'Árvore'): CategoriaServico
    {
        return CategoriaServico::create([
            'nome' => $nome,
            'slug' => Str::slug($nome),
            'descricao' => 'Descrição da categoria.',
            'em_destaque' => true,
        ]);
    }

    private function criarServico(string $nome = 'Avaliação para corte de árvore'): Servico156
    {
        return Servico156::create([
            'categoria_servico_id' => $this->criarCategoria()->id,
            'nome' => $nome,
            'slug' => Str::slug($nome),
            'descricao' => 'Solicite a avaliação técnica para o corte de árvore.',
        ]);
    }

    private function dadosValidos(array $sobrescritos = []): array
    {
        return [
            'descricao' => 'Árvore com risco de queda em frente ao número 100.',
            'telefone_contato' => '(12) 98765-4321',
            'cep' => '11660-000',
            'logradouro' => 'Rua das Flores',
            'numero' => '100',
            'bairro' => 'Centro',
            'ponto_referencia' => 'Próximo à praça',
            ...$sobrescritos,
        ];
    }

    private function criarSolicitacaoDe(User $usuario, Servico156 $servico): Solicitacao156
    {
        return Solicitacao156::create([
            ...$this->dadosValidos(),
            'user_id' => $usuario->id,
            'servico156_id' => $servico->id,
            'categoria_servico_id' => $servico->categoria_servico_id,
            'protocolo' => '156-2026-000001',
        ]);
    }

    private function registrarComo(User $usuario, Servico156 $servico, array $dados)
    {
        return $this->actingAs($usuario)->post(
            route('servicos.156.solicitacao.registrar', $servico->slug),
            $dados,
        );
    }

    public function test_visitante_nao_autenticado_e_redirecionado_para_entrar(): void
    {
        $servico = $this->criarServico();

        $this->get(route('servicos.156.solicitacao.criar', $servico->slug))
            ->assertRedirect(route('acesso.index'));
    }

    public function test_lista_os_servicos_da_categoria(): void
    {
        $servico = $this->criarServico();

        $this->actingAs(User::factory()->create())
            ->get(route('servicos.156.servicos', $servico->categoria->slug))
            ->assertOk()
            ->assertSee('Avaliação para corte de árvore')
            ->assertSee('1 serviço');
    }

    public function test_categoria_sem_servicos_exibe_estado_vazio(): void
    {
        $categoria = $this->criarCategoria('IPTU');

        $this->actingAs(User::factory()->create())
            ->get(route('servicos.156.servicos', $categoria->slug))
            ->assertOk()
            ->assertSee('Ainda não há serviços disponíveis nesta categoria.');
    }

    public function test_categoria_inexistente_retorna_404(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('servicos.156.servicos', 'nao-existe'))
            ->assertNotFound();
    }

    public function test_formulario_exibe_o_servico_e_a_cidade_fixa(): void
    {
        $servico = $this->criarServico();

        $this->actingAs(User::factory()->create())
            ->get(route('servicos.156.solicitacao.criar', $servico->slug))
            ->assertOk()
            ->assertSee('Avaliação para corte de árvore')
            ->assertSee(Solicitacao156::CIDADE_E_UF);
    }

    public function test_registra_solicitacao_para_o_usuario_logado_com_anexos_no_disco_privado(): void
    {
        $usuario = User::factory()->create();
        $servico = $this->criarServico();

        $resposta = $this->registrarComo($usuario, $servico, $this->dadosValidos([
            'anexos' => [
                UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('video.mp4', 2048, 'video/mp4'),
            ],
        ]));

        $solicitacao = Solicitacao156::with('anexos')->firstOrFail();

        $resposta->assertRedirect(route('servicos.156.solicitacao.confirmacao', $solicitacao->protocolo));
        $this->assertSame($usuario->id, $solicitacao->user_id);
        $this->assertSame($servico->id, $solicitacao->servico156_id);
        $this->assertSame($servico->categoria_servico_id, $solicitacao->categoria_servico_id);
        $this->assertMatchesRegularExpression('/^156-\d{4}-\d{6}$/', $solicitacao->protocolo);
        $this->assertCount(2, $solicitacao->anexos);

        foreach ($solicitacao->anexos as $anexo) {
            Storage::disk('local')->assertExists($anexo->caminho);
        }
    }

    public function test_registra_solicitacao_sem_anexos_e_sem_ponto_de_referencia(): void
    {
        $servico = $this->criarServico();

        $this->registrarComo(User::factory()->create(), $servico, $this->dadosValidos(['ponto_referencia' => null]))
            ->assertSessionHasNoErrors();

        $solicitacao = Solicitacao156::with('anexos')->firstOrFail();

        $this->assertNull($solicitacao->ponto_referencia);
        $this->assertCount(0, $solicitacao->anexos);
    }

    public function test_rejeita_descricao_curta_e_telefone_fora_do_formato(): void
    {
        $servico = $this->criarServico();

        $this->registrarComo(User::factory()->create(), $servico, $this->dadosValidos([
            'descricao' => 'curta',
            'telefone_contato' => '12987654321',
        ]))->assertSessionHasErrors(['descricao', 'telefone_contato']);

        $this->assertSame(0, Solicitacao156::count());
    }

    public function test_rejeita_foto_acima_de_5_mb(): void
    {
        $servico = $this->criarServico();

        $this->registrarComo(User::factory()->create(), $servico, $this->dadosValidos([
            'anexos' => [UploadedFile::fake()->create('foto.jpg', 6000, 'image/jpeg')],
        ]))->assertSessionHasErrors('anexos.0');

        $this->assertSame(0, Solicitacao156::count());
    }

    public function test_aceita_video_ate_o_limite_de_10_mb(): void
    {
        $servico = $this->criarServico();

        $this->registrarComo(User::factory()->create(), $servico, $this->dadosValidos([
            'anexos' => [UploadedFile::fake()->create('video.mp4', 10240, 'video/mp4')],
        ]))->assertSessionHasNoErrors();

        $this->assertSame(1, Solicitacao156::count());
    }

    public function test_rejeita_video_acima_de_10_mb(): void
    {
        $servico = $this->criarServico();

        $this->registrarComo(User::factory()->create(), $servico, $this->dadosValidos([
            'anexos' => [UploadedFile::fake()->create('video.mp4', 10241, 'video/mp4')],
        ]))->assertSessionHasErrors('anexos.0');

        $this->assertSame(0, Solicitacao156::count());
    }

    public function test_aceita_anexos_que_somam_exatamente_o_limite_total_de_25_mb(): void
    {
        $servico = $this->criarServico();

        $fotos = array_map(
            fn (int $indice) => UploadedFile::fake()->create("foto{$indice}.jpg", 5120, 'image/jpeg'),
            range(1, 5),
        );

        $this->registrarComo(User::factory()->create(), $servico, $this->dadosValidos(['anexos' => $fotos]))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Solicitacao156::count());
    }

    public function test_rejeita_anexos_que_somam_mais_que_o_limite_total_de_25_mb(): void
    {
        $servico = $this->criarServico();

        $videos = array_map(
            fn (int $indice) => UploadedFile::fake()->create("video{$indice}.mp4", 9000, 'video/mp4'),
            range(1, 3),
        );

        $this->registrarComo(User::factory()->create(), $servico, $this->dadosValidos(['anexos' => $videos]))
            ->assertSessionHasErrors('anexos');

        $this->assertSame(0, Solicitacao156::count());
    }

    public function test_falha_ao_gravar_anexo_desfaz_a_solicitacao_e_avisa_o_cidadao(): void
    {
        $usuario = User::factory()->create();
        $servico = $this->criarServico();

        $this->mock(ArmazenadorDeAnexos::class, function (MockInterface $armazenador) {
            $armazenador->shouldReceive('armazenar')
                ->once()
                ->andThrow(FalhaAoArmazenarAnexo::noDiretorio('solicitacoes-156/teste'));
            $armazenador->shouldReceive('apagar')->once()->with([]);
        });

        $this->registrarComo($usuario, $servico, $this->dadosValidos([
            'anexos' => [UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg')],
        ]))
            ->assertRedirect()
            ->assertSessionHas('erro_geral');

        $this->assertSame(0, Solicitacao156::count());
    }

    public function test_rejeita_mais_de_cinco_anexos(): void
    {
        $servico = $this->criarServico();

        $fotos = array_map(
            fn (int $indice) => UploadedFile::fake()->create("foto{$indice}.jpg", 100, 'image/jpeg'),
            range(1, 6),
        );

        $this->registrarComo(User::factory()->create(), $servico, $this->dadosValidos(['anexos' => $fotos]))
            ->assertSessionHasErrors('anexos');
    }

    public function test_rejeita_tipo_de_arquivo_nao_permitido(): void
    {
        $servico = $this->criarServico();

        $this->registrarComo(User::factory()->create(), $servico, $this->dadosValidos([
            'anexos' => [UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf')],
        ]))->assertSessionHasErrors('anexos.0');
    }

    public function test_apenas_o_dono_acessa_a_confirmacao(): void
    {
        $dono = User::factory()->create();
        $outroUsuario = User::factory()->create();
        $solicitacao = $this->criarSolicitacaoDe($dono, $this->criarServico());

        $rota = route('servicos.156.solicitacao.confirmacao', $solicitacao->protocolo);

        $this->actingAs($outroUsuario)->get($rota)->assertNotFound();
        $this->actingAs($dono)->get($rota)->assertOk()->assertSee($solicitacao->protocolo);
    }
}
