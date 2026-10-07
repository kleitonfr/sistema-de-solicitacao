<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnexoManifestacaoOuvidoria extends Model
{
    protected $table = 'anexos_manifestacao_ouvidoria';

    protected $fillable = [
        'manifestacao_id',
        'nome_original',
        'caminho',
    ];

    public function manifestacao(): BelongsTo
    {
        return $this->belongsTo(ManifestacaoOuvidoria::class, 'manifestacao_id');
    }
}
