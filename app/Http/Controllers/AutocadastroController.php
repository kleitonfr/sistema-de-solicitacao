<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutocadastroController extends Controller
{
    public function index(): View
    {
        return view('portal.cadastro');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome_completo' => ['required', 'string', 'max:255'],
            'nome_social' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'cpf' => ['required', 'string', 'max:14'],
            'nome_mae' => ['nullable', 'string', 'max:255'],
            'data_nascimento' => ['required', 'date'],
            'sexo' => ['required', 'string', 'in:feminino,masculino,outro,nao_informar'],

            'telefone_tipo' => ['required', 'string', 'in:celular,residencial,comercial,recado'],
            'telefone_ddd' => ['required', 'string', 'max:3'],
            'telefone_numero' => ['required', 'string', 'max:20'],
            'telefone_observacao' => ['nullable', 'string', 'max:255'],

            'endereco_cep' => ['required', 'string', 'max:9'],
            'endereco_bairro' => ['required', 'string', 'max:255'],
            'endereco_logradouro' => ['required', 'string', 'max:255'],
            'endereco_cidade' => ['required', 'string', 'max:255'],
            'endereco_numero' => ['required', 'string', 'max:20'],
            'endereco_referencia' => ['nullable', 'string', 'max:255'],

            'termo_uso' => ['accepted'],
        ]);


        return redirect()
            ->route('cadastro.index')
            ->with('situacao', 'sucesso')
            ->with('mensagem', 'Cadastro enviado com sucesso! (simulação — integração com a API pendente)');
    }
}
