<?php

namespace App\Logging;

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;

/**
 * Único ponto onde o IP do cidadão é gravado. Os services não conhecem HTTP:
 * o IP vem do contexto oculto preenchido pelo middleware da requisição e
 * nunca entra nos logs gerais.
 */
class RegistroDeSeguranca
{
    private const CANAL = 'seguranca';

    /**
     * @param  array<string, mixed>  $contexto
     */
    public function aviso(string $evento, array $contexto = []): void
    {
        Log::channel(self::CANAL)->warning($evento, [
            ...$contexto,
            'ip' => Context::getHidden('ip_do_cliente'),
        ]);
    }
}
