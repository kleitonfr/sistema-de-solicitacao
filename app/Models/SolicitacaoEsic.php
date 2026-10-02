<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitacaoEsic extends Model
{
    protected $table = 'solicitacoes_esic';

    protected $fillable = [
        'protocolo',
        'assunto',
        'descricao',
        'anexo_nome_original',
        'anexo_caminho',
    ];

    public const ASSUNTOS = [
        'outro_tema' => 'Outro tema',
        'contrato' => 'Contrato',
        'despesas' => 'Despesas',
        'funcionarios' => 'Funcionários',
        'licitacoes' => 'Licitações',
        'patrimonio' => 'Patrimônio',
        'processo_administrativo' => 'Processo Administrativo',
    ];

    public function rotuloDoAssunto(): string
    {
        return self::ASSUNTOS[$this->assunto] ?? $this->assunto;
    }
}
