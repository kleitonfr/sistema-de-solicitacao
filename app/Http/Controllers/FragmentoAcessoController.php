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
 * completas, sem o layout ao redor. Não lida com submissão de formulário
 * nem validação — isso continua em AcessoController e AutocadastroController.
 *
 * Só responde a requisições AJAX (header X-Requested-With, enviado pelo
 * fetch() dos alternadores em alternador-acesso.js e
 * alternador-tipo-pessoa.js). Uma visita direta a estas rotas devolve 404,
 * para não expor um endpoint público que serve HTML incompleto (sem
 * layout) fora do fluxo dos alternadores.
 */
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
