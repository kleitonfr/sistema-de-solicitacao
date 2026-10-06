<?php

namespace Tests\Feature;

use App\Exceptions\FalhaAoArmazenarAnexo;
use App\Models\SolicitacaoEsic;
use App\Models\User;
use App\Services\ArmazenadorDeAnexos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class EsicSolicitacaoTest extends TestCase
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
            'assunto' => 'contrato',
            'descricao' => 'Solicito cópia do contrato de coleta de lixo vigente.',
            ...$sobrescritos,
        ];
    }

    public function test_visitante_nao_autenticado_e_redirecionado_para_entrar(): void
    {
        $this->get(route('esic.solicitacao.criar'))->assertRedirect(route('acesso.index'));
    }

    public function test_registra_pedido_para_o_usuario_logado_com_anexo_no_disco_privado(): void
    {
        $usuario = User::factory()->create();

        $resposta = $this->actingAs($usuario)->post(route('esic.solicitacao.store'), $this->dadosValidos([
            'anexo' => UploadedFile::fake()->create('pedido.pdf', 100, 'application/pdf'),
        ]));

        $solicitacao = SolicitacaoEsic::firstOrFail();

        $resposta->assertRedirect(route('esic.solicitacao.confirmacao', $solicitacao->protocolo));
        $this->assertSame($usuario->id, $solicitacao->user_id);
        $this->assertMatchesRegularExpression('/^ESIC-\d{4}-\d{6}$/', $solicitacao->protocolo);
        $this->assertSame('pedido.pdf', $solicitacao->anexo_nome_original);
        Storage::disk('local')->assertExists($solicitacao->anexo_caminho);
    }

    public function test_falha_ao_gravar_anexo_nao_cria_o_pedido_e_avisa_o_cidadao(): void
    {
        $this->mock(ArmazenadorDeAnexos::class, function (MockInterface $armazenador) {
            $armazenador->shouldReceive('armazenar')
                ->once()
                ->andThrow(FalhaAoArmazenarAnexo::noDiretorio('solicitacoes-esic'));
            $armazenador->shouldReceive('apagar')->once()->with([]);
        });

        $this->actingAs(User::factory()->create())
            ->post(route('esic.solicitacao.store'), $this->dadosValidos([
                'anexo' => UploadedFile::fake()->create('pedido.pdf', 100, 'application/pdf'),
            ]))
            ->assertRedirect()
            ->assertSessionHas('erro_geral');

        $this->assertSame(0, SolicitacaoEsic::count());
    }

    public function test_rejeita_assunto_fora_da_lista(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('esic.solicitacao.store'), $this->dadosValidos(['assunto' => 'inexistente']))
            ->assertSessionHasErrors('assunto');

        $this->assertSame(0, SolicitacaoEsic::count());
    }

    public function test_apenas_o_dono_acessa_a_confirmacao(): void
    {
        $dono = User::factory()->create();
        $outroUsuario = User::factory()->create();

        $solicitacao = SolicitacaoEsic::create([
            ...$this->dadosValidos(),
            'user_id' => $dono->id,
            'protocolo' => 'ESIC-2026-000001',
        ]);

        $rota = route('esic.solicitacao.confirmacao', $solicitacao->protocolo);

        $this->actingAs($outroUsuario)->get($rota)->assertNotFound();
        $this->actingAs($dono)->get($rota)->assertOk()->assertSee($solicitacao->protocolo);
    }
}
