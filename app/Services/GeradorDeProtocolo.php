<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class GeradorDeProtocolo
{
    /**
     * Lógica provisória, à espera da definição oficial de protocolo: o
     * sequencial é a contagem do ano + 1, então repete número se algum
     * registro for excluído.
     *
     * @param  class-string<Model>  $modeloDaSolicitacao
     */
    public function gerar(string $prefixo, string $modeloDaSolicitacao): string
    {
        $ano = now()->year;

        $quantidadeNoAno = $modeloDaSolicitacao::query()
            ->whereYear('created_at', $ano)
            ->lockForUpdate()
            ->count();

        $sequencial = str_pad((string) ($quantidadeNoAno + 1), 6, '0', STR_PAD_LEFT);

        return "{$prefixo}-{$ano}-{$sequencial}";
    }
}
