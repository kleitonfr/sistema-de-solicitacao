<?php

namespace App\Http\Controllers;

use Illuminate\View\View;


class ServicoController extends Controller
{
    public function index(): View
    {
        return view('servicos.index');
    }
}
