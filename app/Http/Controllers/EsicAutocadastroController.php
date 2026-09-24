<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class EsicAutocadastroController extends Controller
{
    public function index(): View
    {
        return view('esic.cadastro');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome_completo' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        return redirect()
            ->route('esic.cadastro.index')
            ->with('situacao', 'sucesso')
            ->with('mensagem', 'Cadastro no ESIC enviado com sucesso! (simulação — integração com a API pendente)');
    }
}
