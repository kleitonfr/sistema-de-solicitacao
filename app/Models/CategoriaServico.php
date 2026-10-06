<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaServico extends Model
{
    protected $table = 'categorias_servico';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'em_destaque',
    ];

    protected function casts(): array
    {
        return [
            'em_destaque' => 'boolean',
        ];
    }

    public function servicos(): HasMany
    {
        return $this->hasMany(Servico156::class, 'categoria_servico_id');
    }

    #[Scope]
    protected function somenteEmDestaque(Builder $consulta): void
    {
        $consulta->where('em_destaque', true);
    }

    /**
     * Ícone Font Awesome exibido no card da categoria. Mapeado pelo nome
     * enquanto a equipe da API não define o ícone no próprio cadastro;
     * categorias fora do mapa recebem o ícone de reserva.
     */
    public function icone(): string
    {
        return self::ICONES[$this->nome] ?? self::ICONE_RESERVA;
    }

    private const ICONE_RESERVA = 'fa-solid fa-circle-info';

    private const ICONES = [
        'Abordagem Social - Situação de Rua' => 'fa-solid fa-hand-holding-heart',
        'Acessibilidade' => 'fa-solid fa-wheelchair',
        'Alvará Comercial' => 'fa-solid fa-file-signature',
        'Animais' => 'fa-solid fa-paw',
        'Armazém da Família' => 'fa-solid fa-basket-shopping',
        'Árvore' => 'fa-solid fa-tree',
        'Calçadas' => 'fa-solid fa-road',
        'Coleta' => 'fa-solid fa-dumpster',
        'Disque Solidariedade' => 'fa-solid fa-hands-holding-circle',
        'Fiscalização do comércio estabelecido' => 'fa-solid fa-shop',
        'Iluminação Pública' => 'fa-solid fa-lightbulb',
        'IPTU' => 'fa-solid fa-house-chimney',
        'Pavimentação' => 'fa-solid fa-road-barrier',
        'Trânsito' => 'fa-solid fa-car-side',
    ];
}
