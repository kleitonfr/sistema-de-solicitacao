<?php

namespace App\Logging;

use Throwable;

final class ContextoDaFalha
{
    /**
     * @return array<string, mixed>
     */
    public static function deExcecao(Throwable $erro): array
    {
        return [
            'tipo_da_excecao' => $erro::class,
            'codigo_da_excecao' => $erro->getCode(),
            'mensagem_da_excecao' => $erro,
            'arquivo' => basename($erro->getFile()),
            'linha' => $erro->getLine(),
            'stack_trace' => $erro->getTraceAsString(),
        ];
    }

}
