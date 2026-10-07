<?php

namespace App\Http\Requests;

use App\Models\ManifestacaoOuvidoria;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

class RegistrarManifestacaoRequest extends FormRequest
{
    public const MAXIMO_DE_ANEXOS = 5;

    public const TAMANHO_MAXIMO_POR_ARQUIVO_EM_MB = 10;

    public const TAMANHO_MAXIMO_TOTAL_EM_MB = 25;

    public const EXTENSOES_ACEITAS = 'jpg,jpeg,png,pdf,mp4,mov,webm';

    private const BYTES_POR_MB = 1024 * 1024;

    private const REGRAS_DOS_CAMPOS_ESPECIFICOS = [
        'data_do_fato' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
        'local_do_fato' => ['nullable', 'string', 'max:255'],
        'servidor_ou_setor' => ['nullable', 'string', 'max:255'],
    ];

    public function rules(): array
    {
        return [
            'orgao_ou_setor' => ['nullable', 'string', 'max:255'],
            'descricao' => ['required', 'string', 'min:20', 'max:2000'],
            ...$this->regrasDosCamposEspecificosDoTipo(),
            'anexos' => ['nullable', 'array', 'max:'.self::MAXIMO_DE_ANEXOS],
            'anexos.*' => [
                'file',
                'mimes:'.self::EXTENSOES_ACEITAS,
                'max:'.(self::TAMANHO_MAXIMO_POR_ARQUIVO_EM_MB * 1024),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'descricao.min' => 'Descreva a manifestação com mais detalhes (mínimo de :min caracteres).',
            'data_do_fato.date_format' => 'Informe uma data válida.',
            'data_do_fato.before_or_equal' => 'A data do fato não pode ser uma data futura.',
            'anexos.max' => 'Você pode anexar no máximo :max arquivos.',
            'anexos.*.file' => 'O anexo enviado não é um arquivo válido.',
            'anexos.*.mimes' => 'O anexo deve ser uma foto (JPG ou PNG), um PDF ou um vídeo (MP4, MOV ou WEBM).',
            'anexos.*.max' => 'Cada anexo deve ter no máximo '.self::TAMANHO_MAXIMO_POR_ARQUIVO_EM_MB.' MB.',
        ];
    }

    /**
     * @return list<callable>
     */
    public function after(): array
    {
        return [
            fn (Validator $validador) => $this->validarTamanhoTotalDosAnexos($validador),
        ];
    }

    /**
     * Só os campos que pertencem ao tipo recebem regra, e por isso só eles
     * chegam em validated(): um campo enviado fora do tipo é descartado. O tipo
     * vem da rota, que já barra (404) qualquer valor fora de
     * ManifestacaoOuvidoria::TIPOS.
     *
     * @return array<string, list<string>>
     */
    private function regrasDosCamposEspecificosDoTipo(): array
    {
        $camposDoTipo = ManifestacaoOuvidoria::TIPOS[$this->route('tipo')]['campos'];

        return Arr::only(self::REGRAS_DOS_CAMPOS_ESPECIFICOS, $camposDoTipo);
    }

    private function validarTamanhoTotalDosAnexos(Validator $validador): void
    {
        $totalEmBytes = $this->somaDosAnexosValidosEmBytes();

        if ($totalEmBytes <= self::TAMANHO_MAXIMO_TOTAL_EM_MB * self::BYTES_POR_MB) {
            return;
        }

        $totalEmMb = number_format($totalEmBytes / self::BYTES_POR_MB, 1, ',', '.');

        $validador->errors()->add(
            'anexos',
            "Os anexos somam {$totalEmMb} MB, mas o total permitido é de ".self::TAMANHO_MAXIMO_TOTAL_EM_MB.' MB.',
        );
    }

    /**
     * Arquivos com erro de envio ficam de fora: já recebem a mensagem da
     * regra 'file', e ler o tamanho de um upload quebrado lança exceção.
     */
    private function somaDosAnexosValidosEmBytes(): int
    {
        return (int) collect(Arr::wrap($this->file('anexos')))
            ->filter(fn (UploadedFile $anexo) => $anexo->isValid())
            ->sum(fn (UploadedFile $anexo) => $anexo->getSize());
    }
}
