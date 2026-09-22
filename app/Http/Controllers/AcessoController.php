<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller de acesso (entrada) ao Sistema 651.
 *
 * Este projeto é responsável apenas pelo frontend. A autenticação real
 * (verificação de credenciais, sessão, hashing) será implementada pela
 * equipe de backend, consumindo a API que ainda será definida.
 *
 * Por ora, index() apenas renderiza o formulário visual e store() simula
 * uma resposta de erro genérica, para permitir testar o fluxo de UI
 * (estado de erro, mensagens) sem autenticação real.
 */
class AcessoController extends Controller
{
    public function index(): View
    {
        return view('acesso.entrar');
    }

    /**
     * TODO(integração): substituir pela chamada real à API de autenticação
     * assim que o contrato for definido pela equipe de backend.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'senha' => ['required', 'string'],
        ]);

        return redirect()
            ->route('acesso.index')
            ->withErrors(['email' => 'Acesso ainda não disponível — aguardando integração com a API de autenticação.']);
    }
}
