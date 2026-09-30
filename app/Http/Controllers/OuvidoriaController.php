<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class OuvidoriaController extends Controller
{
    public function index(): View
    {
        return view('servicos.ouvidoria.ouvidoria-pagina-inicial');
    }
}
