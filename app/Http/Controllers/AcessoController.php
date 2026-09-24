<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
