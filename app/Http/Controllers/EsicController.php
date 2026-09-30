<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class EsicController extends Controller
{
    public function index(): View
    {
        return view('servicos.esic.esic-pagina-inicial');
    }
}
