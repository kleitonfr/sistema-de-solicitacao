<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Servico156 extends Model
{
    protected $table = 'servicos_156';

    protected $fillable = [
        'categoria_servico_id',
        'nome',
        'slug',
        'descricao',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaServico::class, 'categoria_servico_id');
    }
}
