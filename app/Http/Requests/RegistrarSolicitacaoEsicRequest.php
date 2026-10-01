<?php

namespace App\Http\Requests;

use App\Models\SolicitacaoEsic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarSolicitacaoEsicRequest extends FormRequest
{
    /**
     * Em caso de erro, volta para o próprio formulário — não para
     * url()->previous(), que aqui já é o formulário mesmo, mas fixar a
     * rota evita qualquer ambiguidade se o fluxo de navegação mudar.
     */
    protected $redirectRoute = 'esic.solicitacao.criar';

    public function rules(): array
    {
        return [
            'assunto' => ['required', Rule::in(array_keys(SolicitacaoEsic::ASSUNTOS))],
            'descricao' => ['required', 'string', 'min:20', 'max:4000'],
            'anexo' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ];
    }

    public function messages(): array
    {
        return [
            'assunto.required' => 'Selecione o assunto do seu pedido.',
            'assunto.in' => 'Selecione um assunto válido.',
            'descricao.required' => 'Descreva a informação que você deseja obter.',
            'descricao.min' => 'Descreva o pedido com mais detalhes (mínimo de 20 caracteres).',
            'descricao.max' => 'A descrição deve ter no máximo 4000 caracteres.',
            'anexo.file' => 'O anexo enviado não é um arquivo válido.',
            'anexo.max' => 'O anexo deve ter no máximo 10 MB.',
            'anexo.mimes' => 'O anexo deve ser PDF, imagem (JPG/PNG) ou documento (DOC/DOCX).',
        ];
    }
}
