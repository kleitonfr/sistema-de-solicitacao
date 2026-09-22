<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Serve fragmentos HTML dos formulários de acesso (Entrar/Cadastrar) para
 * troca via requisição AJAX no alternador da tela de acesso, sem recarregar
 * a página inteira.
 *
 * Responsabilidade única: devolver a mesma parcial Blade usada pelas views
 * completas (acesso.parciais.formulario-entrar e
 * cadastro.parciais.formulario-cadastro), sem o layout ao redor. Não lida
 * com submissão de formulário nem validação — isso continua em
 * AcessoController e AutocadastroController.
 *
 * Só responde a requisições AJAX (header X-Requested-With, enviado pelo
 * fetch() do alternador em alternador-acesso.js). Uma visita direta a estas
 * rotas devolve 404, para não expor um endpoint público que serve HTML
 * incompleto (sem layout) fora do fluxo do alternador.
 */
class FragmentoAcessoController extends Controller
{
    public function entrar(Request $request): View
    {
        $this->exigirRequisicaoAjax($request);

        return view('acesso.parciais.formulario-entrar');
    }

    public function cadastro(Request $request): View
    {
        $this->exigirRequisicaoAjax($request);

        return view('cadastro.parciais.formulario-cadastro');
    }

    private function exigirRequisicaoAjax(Request $request): void
    {
        if (! $request->ajax()) {
            throw new NotFoundHttpException();
        }
    }
}
