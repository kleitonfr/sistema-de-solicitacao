<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Tela de escolha de serviço, exibida logo após o login (ver
 * AcessoController::store). Usa o layout institucional completo
 * (header/menu/footer — layouts.app), diferente da tela de acesso
 * (split-screen, sem menu).
 *
 * Apresenta 3 destinos — Ouvidoria, Portal 156 e e-SIC — como cards de
 * escolha. Nenhum dos 3 tem destino real ainda (ver comentário na view);
 * a rota/controller de cada serviço será definida quando esses sistemas
 * existirem.
 */
class ServicoController extends Controller
{
    public function index(): View
    {
        return view('servicos.index');
    }
}
