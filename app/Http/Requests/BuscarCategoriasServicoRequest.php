<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuscarCategoriasServicoRequest extends FormRequest
{
    public const FILTRO_TODAS = 'todas';

    public const FILTRO_PRINCIPAIS = 'principais';

    /**
     * Em caso de erro, volta para a listagem sem parâmetros. Sem isso, o
     * redirecionamento padrão ("voltar") pode apontar para a própria URL
     * inválida quando ela for aberta diretamente.
     */
    protected $redirectRoute = 'servicos.156.categorias';

    public function rules(): array
    {
        return [
            'busca' => ['nullable', 'string', 'max:100'],
            'filtro' => ['nullable', Rule::in([self::FILTRO_TODAS, self::FILTRO_PRINCIPAIS])],
        ];
    }

    public function messages(): array
    {
        return [
            'busca.string' => 'Digite um termo de busca válido.',
            'busca.max' => 'O termo de busca deve ter no máximo 100 caracteres.',
            'filtro.in' => 'Filtro de categorias inválido.',
        ];
    }

    public function termoDeBusca(): ?string
    {
        $termo = trim((string) $this->validated('busca', ''));

        return $termo === '' ? null : $termo;
    }

    public function filtroSelecionado(): string
    {
        return $this->validated('filtro') ?? self::FILTRO_TODAS;
    }
}
