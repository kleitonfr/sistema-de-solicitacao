<?php

namespace App\Exceptions;

use RuntimeException;

class AnexoIndisponivel extends RuntimeException
{
    public const MENSAGEM_PARA_O_CIDADAO = 'Não foi possível baixar o arquivo. Tente novamente em alguns instantes.';

    public static function ausenteDoArmazenamento(): self
    {
        return new self('O arquivo do anexo não foi encontrado no armazenamento.');
    }

    /**
     * O service já registrou a falha com o contexto da solicitação. Sem este
     * método, o Laravel a reportaria de novo.
     */
    public function report(): bool
    {
        return true;
    }
}
