<?php

namespace App\Logging;

use Illuminate\Database\QueryException;
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
            'mensagem_da_excecao' => self::mensagemSemDadosPessoais($erro),
            'arquivo' => basename($erro->getFile()),
            'linha' => $erro->getLine(),
            'stack_trace' => $erro->getTraceAsString(),
        ];
    }

    /**
     * A QueryException anexa à mensagem o SQL com os valores já substituídos
     * (descrição, telefone, endereço do cidadão). A mensagem do driver, que
     * está na exceção anterior, descreve a falha sem esses valores.
     * 
     * Eu recebi um Throwable. Se esse Throwable for especificamente uma
     * QueryException, não confio na getMessage() dela para logar, porque
     * ela pode carregar a query com os valores."
     */
    private static function mensagemSemDadosPessoais(Throwable $erro): string
    {
        if ($erro instanceof QueryException) {
            return $erro->getPrevious()?->getMessage() ?? 'Falha em consulta ao banco de dados.';
        }

        return $erro->getMessage();
    }
}
