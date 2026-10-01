<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarSolicitacaoEsicRequest;
use App\Models\SolicitacaoEsic;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SolicitacaoEsicController extends Controller
{
    public function criar(): View
    {
        return view('servicos.esic.solicitacao.criar');
    }

    /**
     * Sem integração com a API do e-SIC ainda — o anexo é guardado em
     * disco local (storage/app/public) só para existir algo de verdade
     * por trás do formulário. O protocolo é gerado localmente (ver
     * SolicitacaoEsic::gerarProtocolo) e não representa um número oficial.
     */
    public function store(RegistrarSolicitacaoEsicRequest $request): RedirectResponse
    {
        $dados = $request->validated();

        $anexoNomeOriginal = null;
        $anexoCaminho = null;

        if ($request->hasFile('anexo')) {
            $anexo = $request->file('anexo');
            $anexoNomeOriginal = $anexo->getClientOriginalName();
            $anexoCaminho = $anexo->store('solicitacoes-esic', 'public');
        }

        $solicitacao = SolicitacaoEsic::create([
            'protocolo' => SolicitacaoEsic::gerarProtocolo(),
            'assunto' => $dados['assunto'],
            'descricao' => $dados['descricao'],
            'anexo_nome_original' => $anexoNomeOriginal,
            'anexo_caminho' => $anexoCaminho,
        ]);

        return redirect()
            ->route('esic.solicitacao.confirmacao', $solicitacao)
            ->with('protocoloGerado', $solicitacao->protocolo);
    }

    public function confirmacao(SolicitacaoEsic $solicitacao): View
    {
        return view('servicos.esic.solicitacao.confirmacao', [
            'solicitacao' => $solicitacao,
        ]);
    }
}
