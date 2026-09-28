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
