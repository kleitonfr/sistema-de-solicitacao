<?php

namespace App\Exceptions;

use App\Models\MovimentacaoDaSolicitacao;
use DomainException;

class TransicaoDeStatusInvalida extends DomainException
{
    public static function entre(string $statusAtual, string $novoStatus): self
    {
        return new self(sprintf(
            'A solicitação não pode passar de "%s" para "%s".',
            MovimentacaoDaSolicitacao::rotuloDoStatus($statusAtual),
            MovimentacaoDaSolicitacao::rotuloDoStatus($novoStatus),
        ));
    }
}
