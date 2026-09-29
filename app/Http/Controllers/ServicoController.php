<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuscarCategoriasServicoRequest;
use App\Models\CategoriaServico;
use Illuminate\Support\Str;
use Illuminate\View\View;


class ServicoController extends Controller
{
    public function index(): View
    {
        return view('servicos.index');
    }

    public function listarCategoriasPortal156(BuscarCategoriasServicoRequest $request): View
    {
        $termoDeBusca = $request->termoDeBusca();
        $filtroSelecionado = $request->filtroSelecionado();

        $categorias = CategoriaServico::query()
            ->when(
                $filtroSelecionado === BuscarCategoriasServicoRequest::FILTRO_PRINCIPAIS,
                fn ($consulta) => $consulta->somenteEmDestaque(),
            )
            ->when(
                $termoDeBusca !== null,
                fn ($consulta) => $consulta->contendoTermo($termoDeBusca),
            )
            ->get()
            ->sortBy(fn (CategoriaServico $categoria) => $this->chaveDeOrdenacaoAlfabetica($categoria->nome))
            ->values();

        return view('servicos.156.categoria-servicos', [
            'categorias' => $categorias,
            'termoDeBusca' => $termoDeBusca,
            'filtroSelecionado' => $filtroSelecionado,
        ]);
    }

    /**
     * A ordenação é feita aqui, e não no banco, porque o SQLite (usado em
     * desenvolvimento) compara bytes: "Árvore" iria para o fim e "IPTU"
     * ficaria antes de "Iluminação".
     */
    private function chaveDeOrdenacaoAlfabetica(string $nome): string
    {
        return mb_strtolower(Str::ascii($nome));
    }
}
