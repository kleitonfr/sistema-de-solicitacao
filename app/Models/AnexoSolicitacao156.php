<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnexoSolicitacao156 extends Model
{
    protected $table = 'anexos_solicitacao_156';

    protected $fillable = [
        'solicitacao156_id',
        'nome_original',
        'caminho',
    ];

    public function solicitacao(): BelongsTo
    {
        return $this->belongsTo(Solicitacao156::class, 'solicitacao156_id');
    }
}
