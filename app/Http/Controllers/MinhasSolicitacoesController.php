<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListarMinhasSolicitacoesRequest;
use App\Services\MinhasSolicitacoesService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MinhasSolicitacoesController extends Controller
{
    public function __construct(private readonly MinhasSolicitacoesService $minhasSolicitacoesService)
    {
    }

    public function index(ListarMinhasSolicitacoesRequest $request): View
    {
        $chaveDoTipo = $request->validated('servico');
        $busca = $request->validated('busca');

        $solicitacoes = $this->minhasSolicitacoesService
            ->listar($request->user(), $chaveDoTipo, $busca === null ? null : mb_strtoupper($busca))
            ->withQueryString();

        return view('portal.minhas-solicitacoes', [
            'solicitacoes' => $solicitacoes,
            'chaveDoTipo' => $chaveDoTipo,
            'busca' => $busca,
        ]);
    }

    public function show(Request $request, string $protocolo): View
    {
        return view('portal.minhas-solicitacoes-detalhe', [
            'detalhe' => $this->minhasSolicitacoesService->buscarDetalheDoUsuario($request->user(), $protocolo),
        ]);
    }

    public function baixarAnexo(Request $request, string $protocolo, int $posicao): StreamedResponse
    {
        return $this->minhasSolicitacoesService->baixarAnexoDoUsuario($request->user(), $protocolo, $posicao);
    }
}
