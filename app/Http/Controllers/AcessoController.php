<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntrarRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AcessoController extends Controller
{
    public function index(): View
    {
        return view('portal.entrar');
    }

    public function store(EntrarRequest $request): RedirectResponse
    {
        $credenciais = $request->validated();

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
