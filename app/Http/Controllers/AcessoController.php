<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AcessoController extends Controller
{
    public function index(): View
    {
        return view('portal.entrar');
    }

    /**
     * TODO(integração): substituir pela chamada real à API de autenticação
     * assim que o contrato for definido pela equipe de backend.
     *
     * Por ora, autentica via sessão do próprio Laravel (Auth::attempt),
     * usando o usuário de teste criado em database/seeders/DatabaseSeeder.php
     * (e-mail teste@teste.com, senha 12345678) — permite validar o fluxo de
     * login -> tela de escolha de serviço antes da API real existir.
     */
    public function store(Request $request): RedirectResponse
    {
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'senha' => ['required', 'string'],
        ]);

        $autenticado = Auth::attempt([
            'email' => $credenciais['email'],
            'password' => $credenciais['senha'],
        ]);

        if (! $autenticado) {
            return redirect()
                ->route('acesso.index')
                ->withErrors(['email' => 'E-mail ou senha inválidos.']);
        }

        $request->session()->regenerate();

        return redirect()->route('servicos.index');
    }
}
