<?php

namespace Tests\Feature;

use App\Models\CategoriaServico;
use App\Models\ManifestacaoOuvidoria;
use App\Models\Servico156;
use App\Models\Solicitacao156;
use App\Models\SolicitacaoEsic;
use App\Models\User;
use App\Services\StatusDaSolicitacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class MinhasSolicitacoesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('local');
    }

    private function criarServico(): Servico156
    {
        $categoria = CategoriaServico::firstOrCreate(['slug' => 'arvore'], [
            'nome' => 'Árvore',
            'descricao' => 'Descrição da categoria.',
            'em_destaque' => true,
        ]);

        return Servico156::firstOrCreate(['slug' => 'corte-de-arvore'], [
            'categoria_servico_id' => $categoria->id,
            'nome' => 'Avaliação para corte de árvore',
            'descricao' => 'Solicite a avaliação técnica.',
        ]);
    }

    private function criarSolicitacao156(User $usuario, string $protocolo = '156-2026-000001'): Solicitacao156
    {
        $servico = $this->criarServico();

        $solicitacao = Solicitacao156::create([
            'user_id' => $usuario->id,
            'servico156_id' => $servico->id,
            'categoria_servico_id' => $servico->categoria_servico_id,
            'protocolo' => $protocolo,
            'descricao' => 'Árvore com risco de queda.',
            'telefone_contato' => '(12) 98765-4321',
            'cep' => '11660-000',
            'logradouro' => 'Rua das Flores',
            'numero' => '100',
            'bairro' => 'Centro',
        ]);

        app(StatusDaSolicitacaoService::class)->registrarRecebimento($solicitacao);

        return $solicitacao;
    }

    private function criarPedidoEsic(User $usuario, string $protocolo = 'ESIC-2026-000001', array $extras = []): SolicitacaoEsic
    {
        $pedido = SolicitacaoEsic::create([
            'user_id' => $usuario->id,
            'protocolo' => $protocolo,
            'assunto' => 'contrato',
            'descricao' => 'Solicito cópia do contrato vigente.',
            ...$extras,
        ]);

        app(StatusDaSolicitacaoService::class)->registrarRecebimento($pedido);

        return $pedido;
    }

    private function criarManifestacao(User $usuario, string $protocolo = 'OUV-2026-000001'): ManifestacaoOuvidoria
    {
        $manifestacao = ManifestacaoOuvidoria::create([
            'user_id' => $usuario->id,
            'protocolo' => $protocolo,
            'tipo' => 'reclamacao',
            'descricao' => 'Atendimento demorado.',
        ]);

        app(StatusDaSolicitacaoService::class)->registrarRecebimento($manifestacao);

        return $manifestacao;
    }

    public function test_visitante_nao_autenticado_e_redirecionado_para_entrar(): void
    {
        $this->get(route('minhas-solicitacoes.index'))->assertRedirect(route('acesso.index'));
        $this->get(route('minhas-solicitacoes.show', 'OUV-2026-000001'))->assertRedirect(route('acesso.index'));
        $this->get(route('minhas-solicitacoes.anexos.baixar', ['OUV-2026-000001', 1]))->assertRedirect(route('acesso.index'));
    }

    public function test_sem_solicitacoes_mostra_estado_vazio(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('minhas-solicitacoes.index'))
            ->assertOk()
            ->assertSee('Você ainda não registrou nenhuma solicitação.');
    }

    public function test_lista_os_tres_servicos_juntos(): void
    {
        $usuario = User::factory()->create();
        $this->criarSolicitacao156($usuario);
        $this->criarPedidoEsic($usuario);
        $this->criarManifestacao($usuario);

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.index'))
            ->assertOk()
            ->assertSee('156-2026-000001')
            ->assertSee('ESIC-2026-000001')
            ->assertSee('OUV-2026-000001')
            ->assertSee('Recebida');
    }

    public function test_nao_lista_solicitacoes_de_outro_usuario(): void
    {
        $dono = User::factory()->create();
        $this->criarPedidoEsic($dono);

        $this->actingAs(User::factory()->create())
            ->get(route('minhas-solicitacoes.index'))
            ->assertOk()
            ->assertDontSee('ESIC-2026-000001');
    }

    public function test_aba_filtra_por_servico(): void
    {
        $usuario = User::factory()->create();
        $this->criarPedidoEsic($usuario);
        $this->criarManifestacao($usuario);

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.index', ['servico' => 'esic']))
            ->assertOk()
            ->assertSee('ESIC-2026-000001')
            ->assertDontSee('OUV-2026-000001');
    }

    public function test_busca_por_protocolo(): void
    {
        $usuario = User::factory()->create();
        $this->criarPedidoEsic($usuario);
        $this->criarManifestacao($usuario);

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.index', ['busca' => 'ouv-2026']))
            ->assertOk()
            ->assertSee('OUV-2026-000001')
            ->assertDontSee('ESIC-2026-000001');
    }

    public function test_busca_sem_resultado_mostra_mensagem_e_caminho_de_volta(): void
    {
        $usuario = User::factory()->create();
        $this->criarPedidoEsic($usuario);

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.index', ['busca' => 'OUV-9999']))
            ->assertOk()
            ->assertSee('Não encontramos solicitações com esses critérios.')
            ->assertSee('Ver todas as solicitações');
    }

    public function test_filtro_invalido_volta_para_a_lista_com_erro(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('minhas-solicitacoes.index', ['servico' => 'inexistente']))
            ->assertRedirect(route('minhas-solicitacoes.index'))
            ->assertSessionHasErrors('servico');

        $this->actingAs(User::factory()->create())
            ->get(route('minhas-solicitacoes.index', ['busca' => '%; drop']))
            ->assertSessionHasErrors('busca');
    }

    public function test_lista_e_paginada_de_dez_em_dez(): void
    {
        $usuario = User::factory()->create();
        foreach (range(1, 11) as $numero) {
            $this->criarPedidoEsic($usuario, sprintf('ESIC-2026-%06d', $numero));
        }

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.index'))
            ->assertOk()
            ->assertSee('11 solicitações encontradas')
            ->assertSee('Página 1 de 2');

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Página 2 de 2');
    }

    public function test_detalhe_mostra_dados_e_linha_do_tempo(): void
    {
        $usuario = User::factory()->create();
        $manifestacao = $this->criarManifestacao($usuario);

        app(StatusDaSolicitacaoService::class)->alterarStatus(
            $manifestacao,
            'em_analise',
            'Sua manifestação está sendo analisada.',
            'teste',
        );

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.show', $manifestacao->protocolo))
            ->assertOk()
            ->assertSee('Atendimento demorado.')
            ->assertSee('Recebemos sua solicitação.')
            ->assertSee('Sua manifestação está sendo analisada.')
            ->assertSee('Em análise');
    }

    public function test_detalhe_sem_movimentacoes_mostra_aviso(): void
    {
        $usuario = User::factory()->create();
        $pedido = SolicitacaoEsic::create([
            'user_id' => $usuario->id,
            'protocolo' => 'ESIC-2026-000001',
            'assunto' => 'contrato',
            'descricao' => 'Pedido antigo.',
        ]);

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.show', $pedido->protocolo))
            ->assertOk()
            ->assertSee('Ainda não há movimentações registradas.');
    }

    public function test_detalhe_de_outro_usuario_devolve_404(): void
    {
        $dono = User::factory()->create();
        $manifestacao = $this->criarManifestacao($dono);

        $this->actingAs(User::factory()->create())
            ->get(route('minhas-solicitacoes.show', $manifestacao->protocolo))
            ->assertNotFound();
    }

    public function test_protocolo_inexistente_ou_malformado_devolve_404(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->get(route('minhas-solicitacoes.show', 'OUV-2026-999999'))->assertNotFound();
        $this->actingAs($usuario)->get('/minhas-solicitacoes/qualquer-coisa')->assertNotFound();
    }

    public function test_dono_baixa_anexo_do_esic(): void
    {
        $usuario = User::factory()->create();
        Storage::disk('local')->put('solicitacoes-esic/arquivo.pdf', 'conteudo');
        $pedido = $this->criarPedidoEsic($usuario, extras: [
            'anexo_nome_original' => 'pedido.pdf',
            'anexo_caminho' => 'solicitacoes-esic/arquivo.pdf',
        ]);

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.anexos.baixar', [$pedido->protocolo, 1]))
            ->assertOk()
            ->assertDownload('pedido.pdf')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_outro_usuario_nao_baixa_anexo(): void
    {
        $dono = User::factory()->create();
        Storage::disk('local')->put('solicitacoes-esic/arquivo.pdf', 'conteudo');
        $pedido = $this->criarPedidoEsic($dono, extras: [
            'anexo_nome_original' => 'pedido.pdf',
            'anexo_caminho' => 'solicitacoes-esic/arquivo.pdf',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('minhas-solicitacoes.anexos.baixar', [$pedido->protocolo, 1]))
            ->assertNotFound();
    }

    public function test_posicao_de_anexo_inexistente_devolve_404(): void
    {
        $usuario = User::factory()->create();
        $pedido = $this->criarPedidoEsic($usuario);

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.anexos.baixar', [$pedido->protocolo, 1]))
            ->assertNotFound();
    }

    public function test_anexo_ausente_no_disco_avisa_o_cidadao_sem_detalhes_tecnicos(): void
    {
        $usuario = User::factory()->create();
        $pedido = $this->criarPedidoEsic($usuario, extras: [
            'anexo_nome_original' => 'pedido.pdf',
            'anexo_caminho' => 'solicitacoes-esic/que-sumiu.pdf',
        ]);

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.anexos.baixar', [$pedido->protocolo, 1]))
            ->assertRedirect(route('minhas-solicitacoes.show', $pedido->protocolo))
            ->assertSessionHas('erro_geral');
    }

    public function test_dono_baixa_anexo_da_ouvidoria_na_ordem_de_envio(): void
    {
        $usuario = User::factory()->create();
        $manifestacao = $this->criarManifestacao($usuario);
        Storage::disk('local')->put('ouvidoria/a.pdf', 'a');
        Storage::disk('local')->put('ouvidoria/b.pdf', 'b');
        $manifestacao->anexos()->create(['nome_original' => 'primeiro.pdf', 'caminho' => 'ouvidoria/a.pdf']);
        $manifestacao->anexos()->create(['nome_original' => 'segundo.pdf', 'caminho' => 'ouvidoria/b.pdf']);

        $this->actingAs($usuario)
            ->get(route('minhas-solicitacoes.anexos.baixar', [$manifestacao->protocolo, 2]))
            ->assertDownload('segundo.pdf');
    }
}
