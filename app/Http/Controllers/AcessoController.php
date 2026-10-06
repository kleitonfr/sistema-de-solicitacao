<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntrarRequest;
use App\Logging\RegistroDeSeguranca;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AcessoController extends Controller
{
    public function __construct(private readonly RegistroDeSeguranca $registroDeSeguranca)
    {
    }

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
            $this->registroDeSeguranca->aviso('login_falhou');

            return redirect()
                ->route('acesso.index')
                ->withErrors(['email' => 'E-mail ou senha inválidos.']);
        }

        $request->session()->regenerate();

        Log::info('login_realizado', ['usuario_id' => Auth::id()]);

        return redirect()->route('servicos.index');
    }
}
