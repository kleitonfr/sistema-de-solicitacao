<?php

namespace Tests\Feature;

use App\Exceptions\TransicaoDeStatusInvalida;
use App\Models\ManifestacaoOuvidoria;
use App\Models\MovimentacaoDaSolicitacao;
use App\Models\SolicitacaoEsic;
use App\Models\User;
use App\Services\StatusDaSolicitacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StatusDaSolicitacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('local');
    }

    private function criarPedidoRecebido(): SolicitacaoEsic
    {
        $pedido = SolicitacaoEsic::create([
            'user_id' => User::factory()->create()->id,
            'protocolo' => 'ESIC-2026-000001',
            'assunto' => 'contrato',
            'descricao' => 'Solicito cópia do contrato vigente.',
        ]);

        app(StatusDaSolicitacaoService::class)->registrarRecebimento($pedido);

        return $pedido;
    }

    private function alterar(SolicitacaoEsic $pedido, string $novoStatus, ?string $mensagem = null)
    {
        return app(StatusDaSolicitacaoService::class)
            ->alterarStatus($pedido, $novoStatus, $mensagem, 'teste');
    }

    public function test_transicoes_permitidas_seguem_a_tabela(): void
    {
        $this->assertTrue(MovimentacaoDaSolicitacao::transicaoEhPermitida('recebida', 'em_analise'));
        $this->assertTrue(MovimentacaoDaSolicitacao::transicaoEhPermitida('em_analise', 'concluida'));
        $this->assertFalse(MovimentacaoDaSolicitacao::transicaoEhPermitida('recebida', 'concluida'));
        $this->assertFalse(MovimentacaoDaSolicitacao::transicaoEhPermitida('concluida', 'em_analise'));
        $this->assertFalse(MovimentacaoDaSolicitacao::transicaoEhPermitida('em_atendimento', 'encaminhada'));
    }

    public function test_altera_status_e_registra_movimentacao(): void
    {
        $pedido = $this->criarPedidoRecebido();

        $movimentacao = $this->alterar($pedido, 'em_analise', 'Em análise pela equipe.');

        $this->assertSame('em_analise', $pedido->fresh()->status);
        $this->assertSame('recebida', $movimentacao->status_anterior);
        $this->assertSame('em_analise', $movimentacao->status_novo);
        $this->assertSame('teste', $movimentacao->registrado_por);
        $this->assertCount(2, $pedido->movimentacoes()->get());
    }

    public function test_transicao_invalida_e_recusada_sem_alterar_nada(): void
    {
        $pedido = $this->criarPedidoRecebido();

        try {
            $this->alterar($pedido, 'concluida');
            $this->fail('A transição inválida deveria ser recusada.');
        } catch (TransicaoDeStatusInvalida) {
            // esperado
        }

        $this->assertSame('recebida', $pedido->fresh()->status);
        $this->assertCount(1, $pedido->movimentacoes()->get());
    }

    public function test_instancia_desatualizada_nao_repete_transicao_ja_feita(): void
    {
        $pedido = $this->criarPedidoRecebido();
        $copiaDesatualizada = SolicitacaoEsic::findOrFail($pedido->id);

        $this->alterar($pedido, 'em_analise');

        $this->expectException(TransicaoDeStatusInvalida::class);
        $this->alterar($copiaDesatualizada, 'em_analise');
    }

    public function test_registro_de_solicitacao_cria_movimentacao_de_recebimento(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->post(route('esic.solicitacao.store'), [
            'assunto' => 'contrato',
            'descricao' => 'Solicito cópia do contrato de coleta de lixo.',
            'anexo' => UploadedFile::fake()->create('pedido.pdf', 100, 'application/pdf'),
        ]);

        $pedido = SolicitacaoEsic::firstOrFail();
        $movimentacoes = $pedido->movimentacoes()->get();

        $this->assertCount(1, $movimentacoes);
        $this->assertSame('recebida', $movimentacoes->first()->status_novo);
        $this->assertSame('sistema', $movimentacoes->first()->registrado_por);
    }

    public function test_comando_avanca_status_de_uma_solicitacao(): void
    {
        $pedido = $this->criarPedidoRecebido();

        $this->artisan('solicitacao:avancar-status', [
            'protocolo' => $pedido->protocolo,
            'status' => 'em_analise',
            'mensagem' => 'Estamos analisando.',
        ])->assertExitCode(0);

        $this->assertSame('em_analise', $pedido->fresh()->status);
    }

    public function test_comando_recusa_status_inexistente(): void
    {
        $pedido = $this->criarPedidoRecebido();

        $this->artisan('solicitacao:avancar-status', ['protocolo' => $pedido->protocolo, 'status' => 'inventado'])
            ->assertExitCode(1);

        $this->assertSame('recebida', $pedido->fresh()->status);
    }

    public function test_comando_recusa_transicao_invalida(): void
    {
        $pedido = $this->criarPedidoRecebido();

        $this->artisan('solicitacao:avancar-status', ['protocolo' => $pedido->protocolo, 'status' => 'concluida'])
            ->assertExitCode(1);

        $this->assertSame('recebida', $pedido->fresh()->status);
    }

    public function test_comando_recusa_protocolo_inexistente(): void
    {
        $this->artisan('solicitacao:avancar-status', ['protocolo' => 'OUV-2026-999999', 'status' => 'em_analise'])
            ->assertExitCode(1);

        $this->assertSame(0, ManifestacaoOuvidoria::count());
    }

    public function test_comando_nao_funciona_em_producao(): void
    {
        $pedido = $this->criarPedidoRecebido();
        $this->app['env'] = 'production';

        $this->artisan('solicitacao:avancar-status', ['protocolo' => $pedido->protocolo, 'status' => 'em_analise'])
            ->assertExitCode(1);

        $this->assertSame('recebida', $pedido->fresh()->status);
    }
}
