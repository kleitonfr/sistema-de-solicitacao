<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CategoriaServico extends Model
{
    protected $table = 'categorias_servico';

    protected $fillable = [
        'nome',
        'descricao',
        'em_destaque',
    ];

    protected function casts(): array
    {
        return [
            'em_destaque' => 'boolean',
        ];
    }

    #[Scope]
    protected function somenteEmDestaque(Builder $consulta): void
    {
        $consulta->where('em_destaque', true);
    }

    /**
     * "%" e "_" digitados pelo cidadão são tratados como texto, não como
     * curingas do LIKE. O caractere de escape é "!" porque a barra
     * invertida não tem o mesmo significado no MySQL e no SQLite.
     */
    #[Scope]
    protected function contendoTermo(Builder $consulta, string $termo): void
    {
        $termoEscapado = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $termo);
        $padrao = "%{$termoEscapado}%";

        $consulta->where(function (Builder $condicao) use ($padrao) {
            $condicao
                ->whereRaw("nome LIKE ? ESCAPE '!'", [$padrao])
                ->orWhereRaw("descricao LIKE ? ESCAPE '!'", [$padrao]);
        });
    }
}
