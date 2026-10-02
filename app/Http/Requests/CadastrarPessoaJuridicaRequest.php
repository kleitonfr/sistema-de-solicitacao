<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CadastrarPessoaJuridicaRequest extends FormRequest
{
    protected $redirectRoute = 'cadastro.index';

    public function rules(): array
    {
        return [
            'razao_social' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'max:18'],
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
        ];
    }
}
