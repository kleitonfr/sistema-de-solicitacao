<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class FragmentoAcessoController extends Controller
{
    public function entrar(Request $request): View
    {
        $this->exigirRequisicaoAjax($request);

        return view('portal.parciais.formulario-entrar');
    }

    public function cadastro(Request $request): View
    {
        $this->exigirRequisicaoAjax($request);

        return view('portal.parciais.formulario-cadastro');
    }


    private function exigirRequisicaoAjax(Request $request): void
    {
        if (! $request->ajax()) {
            throw new NotFoundHttpException();
        }
    }
}
