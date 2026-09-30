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

    /**
     * Ícone Font Awesome exibido no card da categoria (ver
     * resources/views/servicos/156/categoria-servicos.blade.php).
     *
     * Mapeado pelo nome porque a tabela não tem uma coluna própria para
     * isso — os nomes vêm do seeder provisório (CategoriaServicoSeeder).
     * Categorias fora deste mapa (novas, cadastradas depois) recebem o
     * ícone de reserva, para a tela nunca ficar sem ícone por esquecimento.
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
