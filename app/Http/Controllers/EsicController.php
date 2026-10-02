<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarSolicitacaoEsicRequest;
use App\Models\SolicitacaoEsic;
use App\Services\EsicService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EsicController extends Controller
{
    public function __construct(private readonly EsicService $esicService)
    {
    }

    public function index(): View
    {
        return view('servicos.esic.esic-pagina-inicial');
    }

    public function criar(): View
    {
        return view('servicos.esic.solicitacao.criar');
    }

    public function store(RegistrarSolicitacaoEsicRequest $request): RedirectResponse
    {
        $solicitacao = $this->esicService->registrarSolicitacao(
            $request->validated(),
            $request->file('anexo'),
        );

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
