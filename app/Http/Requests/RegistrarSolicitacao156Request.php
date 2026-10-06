<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

class RegistrarSolicitacao156Request extends FormRequest
{
    public const MAXIMO_DE_ANEXOS = 5;

    public const TAMANHO_MAXIMO_DA_FOTO_EM_MB = 5;

    public const TAMANHO_MAXIMO_DO_VIDEO_EM_MB = 30;

    public const EXTENSOES_ACEITAS = 'jpg,jpeg,png,mp4,mov,webm';

    public function rules(): array
    {
        return [
            'descricao' => ['required', 'string', 'min:20', 'max:2000'],
            'telefone_contato' => ['required', 'regex:/^\(\d{2}\) \d{4,5}-\d{4}$/'],
            'cep' => ['required', 'regex:/^\d{5}-\d{3}$/'],
            'logradouro' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'bairro' => ['required', 'string', 'max:255'],
            'ponto_referencia' => ['nullable', 'string', 'max:255'],
            'anexos' => ['nullable', 'array', 'max:'.self::MAXIMO_DE_ANEXOS],
            'anexos.*' => [
                'file',
                'mimes:'.self::EXTENSOES_ACEITAS,
                'max:'.(self::TAMANHO_MAXIMO_DO_VIDEO_EM_MB * 1024),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'descricao.min' => 'Descreva a solicitação com mais detalhes (mínimo de :min caracteres).',
            'telefone_contato.regex' => 'Informe o telefone de contato no formato (00) 00000-0000.',
            'cep.regex' => 'Informe o CEP no formato 00000-000.',
            'anexos.max' => 'Você pode anexar no máximo :max arquivos.',
            'anexos.*.file' => 'O anexo enviado não é um arquivo válido.',
            'anexos.*.mimes' => 'O anexo deve ser uma foto (JPG ou PNG) ou um vídeo (MP4, MOV ou WEBM).',
            'anexos.*.max' => 'O anexo deve ter no máximo '.self::TAMANHO_MAXIMO_DO_VIDEO_EM_MB.' MB.',
        ];
    }

    /**
     * @return list<callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validador) {
                foreach (Arr::wrap($this->file('anexos')) as $indice => $anexo) {
                    if ($this->ehFotoMaiorQueOPermitido($anexo)) {
                        $validador->errors()->add(
                            "anexos.{$indice}",
                            'A foto deve ter no máximo '.self::TAMANHO_MAXIMO_DA_FOTO_EM_MB.' MB.',
                        );
                    }
                }
            },
        ];
    }

    private function ehFotoMaiorQueOPermitido(UploadedFile $anexo): bool
    {
        return $anexo->isValid()
            && str_starts_with((string) $anexo->getMimeType(), 'image/')
            && $anexo->getSize() > self::TAMANHO_MAXIMO_DA_FOTO_EM_MB * 1024 * 1024;
    }
}
