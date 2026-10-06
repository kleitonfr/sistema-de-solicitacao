<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitacaoEsic extends Model
{
    protected $table = 'solicitacoes_esic';

    protected $fillable = [
        'user_id',
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

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rotuloDoAssunto(): string
    {
        return self::ASSUNTOS[$this->assunto] ?? $this->assunto;
    }
}
