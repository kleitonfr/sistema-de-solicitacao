<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class AdicionarContextoDaRequisicao
{
    public function handle(Request $request, Closure $next): Response
    {
        $identificadorDeCorrelacao = bin2hex(random_bytes(6));

        Context::add('identificador_correlacao', $identificadorDeCorrelacao);
        Context::addHidden('ip_do_cliente', $request->ip());

        $resposta = $next($request);
        $resposta->headers->set('X-Identificador-Correlacao', $identificadorDeCorrelacao);
 
        return $resposta;
    }
}
