<?php

namespace App\Http\Requests;

use App\Services\MinhasSolicitacoesService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListarMinhasSolicitacoesRequest extends FormRequest
{
    /**
     * Em um filtro inválido, o cidadão volta à lista sem filtro, com a
     * mensagem junto ao campo, em vez de ser mandado para a página anterior.
     */
    protected $redirectRoute = 'minhas-solicitacoes.index';

    public function rules(): array
    {
        return [
            'servico' => ['nullable', Rule::in(array_keys(MinhasSolicitacoesService::TIPOS))],
            'busca' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9-]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'servico.in' => 'Escolha um dos serviços da lista.',
            'busca.max' => 'O protocolo deve ter no máximo :max caracteres.',
            'busca.regex' => 'Digite o protocolo usando apenas letras, números e hífen.',
        ];
    }
}
