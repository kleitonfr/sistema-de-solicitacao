<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class SolicitacaoNaoPodeSerRegistrada extends RuntimeException
{
    public const MENSAGEM_PARA_O_CIDADAO = 'Não foi possível concluir sua solicitação. Tente novamente em alguns instantes.';

    public static function por(Throwable $causa): self
    {
        return new self(self::MENSAGEM_PARA_O_CIDADAO, 0, $causa);
    }

    /**
     * O service já registrou a falha com contexto sem dados pessoais. Sem
     * este método, o Laravel reportaria a exceção de novo e gravaria a
     * causa original, cuja mensagem pode conter valores do formulário.
     */
    public function report(): bool
    {
        return true;
    }
}
