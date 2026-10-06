<?php

use App\Exceptions\SolicitacaoNaoPodeSerRegistrada;
use App\Http\Middleware\AdicionarContextoDaRequisicao;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Primeiro da fila, para que até as falhas anteriores ao roteamento
        // (como envio acima do limite do PHP) saiam com o identificador.
        $middleware->prepend(AdicionarContextoDaRequisicao::class);

        $middleware->redirectGuestsTo(fn () => route('acesso.index'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (SolicitacaoNaoPodeSerRegistrada $erro, Request $requisicao) {
            $identificadorDeCorrelacao = Context::get('identificador_correlacao');

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'erro_geral',
                    SolicitacaoNaoPodeSerRegistrada::MENSAGEM_PARA_O_CIDADAO." Se o problema continuar, informe o código {$identificadorDeCorrelacao}.",
                );
        });
    })->create();
