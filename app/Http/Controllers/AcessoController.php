<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntrarRequest;
use App\Logging\RegistroDeSeguranca;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function sair(Request $request): RedirectResponse
    {
        $usuarioId = Auth::id();

        Auth::logout();

        // Invalidar a sessão e trocar o token CSRF impede que a sessão antiga
        // seja reaproveitada depois da saída (por exemplo, em computador compartilhado).
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('logout_realizado', ['usuario_id' => $usuarioId]);

        return redirect()->route('acesso.index');
    }
}
