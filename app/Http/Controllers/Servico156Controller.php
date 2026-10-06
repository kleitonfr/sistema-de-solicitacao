<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuscarCategoriasServicoRequest;
use App\Http\Requests\RegistrarSolicitacao156Request;
use App\Services\Servico156Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class Servico156Controller extends Controller
{
    public function __construct(private readonly Servico156Service $servico156Service)
    {
    }

    public function index(): View
    {
        return view('servicos.index');
    }

    public function listarCategoriasPortal156(BuscarCategoriasServicoRequest $request): View
    {
        $termoDeBusca = $request->termoDeBusca();
        $filtroSelecionado = $request->filtroSelecionado();

        $categorias = $this->servico156Service->listarCategorias(
            $termoDeBusca,
            $filtroSelecionado === BuscarCategoriasServicoRequest::FILTRO_PRINCIPAIS,
        );

        return view('servicos.156.categoria-servicos', [
            'categorias' => $categorias,
            'termoDeBusca' => $termoDeBusca,
            'filtroSelecionado' => $filtroSelecionado,
        ]);
    }

    public function listarServicosDaCategoria(string $slugDaCategoria): View
    {
        $categoria = $this->servico156Service->buscarCategoriaPorSlug($slugDaCategoria);

        return view('servicos.156.servicos-da-categoria', [
            'categoria' => $categoria,
            'servicos' => $this->servico156Service->listarServicosDaCategoria($categoria),
        ]);
    }

    public function criarSolicitacao(string $slugDoServico): View
    {
        return view('servicos.156.solicitacao.criar', [
            'servico' => $this->servico156Service->buscarServicoPorSlug($slugDoServico),
        ]);
    }

    public function registrarSolicitacao(RegistrarSolicitacao156Request $request, string $slugDoServico): RedirectResponse
    {
        $solicitacao = $this->servico156Service->registrarSolicitacao(
            $request->user(),
            $this->servico156Service->buscarServicoPorSlug($slugDoServico),
            $request->validated(),
            Arr::wrap($request->file('anexos')),
        );

        return redirect()->route('servicos.156.solicitacao.confirmacao', $solicitacao->protocolo);
    }

    public function confirmacaoSolicitacao(Request $request, string $protocolo): View
    {
        return view('servicos.156.solicitacao.confirmacao', [
            'solicitacao' => $this->servico156Service->buscarSolicitacaoDoUsuario($request->user(), $protocolo),
        ]);
    }
}
