<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarSolicitacaoEsicRequest;
use App\Services\EsicService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            $request->user(),
            $request->validated(),
            $request->file('anexo'),
        );

        return redirect()->route('esic.solicitacao.confirmacao', $solicitacao->protocolo);
    }

    public function confirmacao(Request $request, string $protocolo): View
    {
        return view('servicos.esic.solicitacao.confirmacao', [
            'solicitacao' => $this->esicService->buscarSolicitacaoDoUsuario($request->user(), $protocolo),
        ]);
    }
}
