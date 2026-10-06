<?php

namespace App\Exceptions;

use RuntimeException;

class FalhaAoArmazenarAnexo extends RuntimeException
{
    public static function noDiretorio(string $diretorio): self
    {
        return new self("Não foi possível gravar o anexo no diretório '{$diretorio}'.");
    }
}
