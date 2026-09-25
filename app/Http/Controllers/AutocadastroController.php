<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller de cadastro do Sistema 651.
 *
 * Este projeto é responsável apenas pelo frontend. Os campos e regras de
 * validação abaixo refletem o contrato esperado da API que será
 * disponibilizada pela equipe de backend.
 *
 * Formulário único: os campos de Pessoa Física (nome, cpf) e Pessoa
 * Jurídica (razao_social, cnpj) coexistem no mesmo formulário — a
 * alternância de exibição é só de interface (ver alternador-tipo-pessoa.js);
 * a validação aqui exige um ou outro conforme o tipo_pessoa informado.
 */
class AutocadastroController extends Controller
{
    public function index(): View
    {
        return view('portal.cadastro');
    }

    /**
     * TODO(integração): substituir pela chamada real à API de cadastro
     * assim que o contrato for definido pela equipe de backend.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tipo_pessoa' => ['required', 'string', 'in:fisica,juridica'],

            'nome_completo' => ['required_if:tipo_pessoa,fisica', 'nullable', 'string', 'max:255'],
            'cpf' => ['required_if:tipo_pessoa,fisica', 'nullable', 'string', 'max:14'],
            'razao_social' => ['required_if:tipo_pessoa,juridica', 'nullable', 'string', 'max:255'],
            'cnpj' => ['required_if:tipo_pessoa,juridica', 'nullable', 'string', 'max:18'],

            'nome_social' => ['nullable', 'string', 'max:255'],
            'nome_mae' => ['nullable', 'string', 'max:255'],
            'data_nascimento' => ['required', 'date'],
            'sexo' => ['required', 'string', 'in:feminino,masculino,outro,nao_informar'],
            'faixa_etaria' => ['required', 'string', 'in:ate_17,18_24,25_34,35_44,45_59,60_mais'],
            'escolaridade' => ['required', 'string', 'in:fundamental,medio,superior,pos_graduacao,nao_informar'],
            'profissao' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'email_confirmacao' => ['required', 'email', 'same:email'],

            'telefone_tipo' => ['required', 'string', 'in:celular,residencial,comercial,recado'],
            'telefone_ddd' => ['required', 'string', 'max:3'],
            'telefone_numero' => ['required', 'string', 'max:20'],
            'telefone_observacao' => ['nullable', 'string', 'max:255'],

            'endereco_cep' => ['required', 'string', 'max:9'],
            'endereco_logradouro' => ['required', 'string', 'max:255'],
            'endereco_bairro' => ['required', 'string', 'max:255'],
            'endereco_cidade' => ['required', 'string', 'max:255'],
            'endereco_uf' => ['required', 'string', 'size:2'],
            'endereco_numero' => ['required', 'string', 'max:20'],
            'endereco_complemento' => ['nullable', 'string', 'max:255'],

            'senha' => ['required', 'string', 'min:8'],
            'senha_confirmacao' => ['required', 'string', 'same:senha'],

            'termo_uso' => ['accepted'],
        ]);

        return redirect()
            ->route('cadastro.index')
            ->with('situacao', 'sucesso')
            ->with('mensagem', 'Cadastro enviado com sucesso! (simulação — integração com a API pendente)');
    }
}
