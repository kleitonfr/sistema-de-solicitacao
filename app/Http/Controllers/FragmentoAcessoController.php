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

    public function cadastroFisica(Request $request): View
    {
        $this->exigirRequisicaoAjax($request);

        return view('portal.parciais.formulario-cadastro-fisica');
    }

    public function cadastroJuridica(Request $request): View
    {
        $this->exigirRequisicaoAjax($request);

        return view('portal.parciais.formulario-cadastro-juridica');
    }

    private function exigirRequisicaoAjax(Request $request): void
    {
        if (! $request->ajax()) {
            throw new NotFoundHttpException();
        }
    }
}
