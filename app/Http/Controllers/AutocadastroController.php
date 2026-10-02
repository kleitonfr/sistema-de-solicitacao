<?php

namespace App\Http\Controllers;

use App\Http\Requests\CadastrarPessoaFisicaRequest;
use App\Http\Requests\CadastrarPessoaJuridicaRequest;
use App\Services\CadastroService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AutocadastroController extends Controller
{
    public function __construct(private readonly CadastroService $cadastroService)
    {
    }

    public function index(): View
    {
        return view('portal.cadastro');
    }

    public function storeFisica(CadastrarPessoaFisicaRequest $request): RedirectResponse
    {
        $this->cadastroService->cadastrarPessoaFisica($request->validated());

        return $this->redirecionarComSucesso();
    }

    public function storeJuridica(CadastrarPessoaJuridicaRequest $request): RedirectResponse
    {
        $this->cadastroService->cadastrarPessoaJuridica($request->validated());

        return $this->redirecionarComSucesso();
    }

    private function redirecionarComSucesso(): RedirectResponse
    {
        return redirect()
            ->route('acesso.index')
            ->with('situacao', 'sucesso')
            ->with('mensagem', 'Cadastro enviado com sucesso! (simulação — integração com a API pendente)');
    }
}
