<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitacao156 extends Model
{
    public const CIDADE_E_UF = 'Caraguatatuba/SP';

    protected $table = 'solicitacoes_156';

    protected $fillable = [
        'user_id',
        'servico156_id',
        'categoria_servico_id',
        'protocolo',
        'descricao',
        'telefone_contato',
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'ponto_referencia',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servico156::class, 'servico156_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaServico::class, 'categoria_servico_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(AnexoSolicitacao156::class, 'solicitacao156_id');
    }

    public function enderecoCompleto(): string
    {
        return "{$this->logradouro}, {$this->numero} — {$this->bairro}, ".self::CIDADE_E_UF." — CEP {$this->cep}";
    }
}
