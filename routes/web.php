<?php

use App\Http\Controllers\AcessoController;
use App\Http\Controllers\AutocadastroController;
use App\Http\Controllers\EsicController;
use App\Http\Controllers\FragmentoAcessoController;
use App\Http\Controllers\OuvidoriaController;
use App\Http\Controllers\Servico156Controller;
use App\Models\ManifestacaoOuvidoria;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('cadastro.index');
});

Route::view('/sobre-156', 'portal.sobre-156')->name('sobre156.index');

Route::get('/entrar', [AcessoController::class, 'index'])->name('acesso.index');
Route::post('/entrar', [AcessoController::class, 'store'])->name('acesso.store');

Route::get('/cadastro', [AutocadastroController::class, 'index'])->name('cadastro.index');
Route::post('/cadastro/fisica', [AutocadastroController::class, 'storeFisica'])->name('cadastro.store.fisica');
Route::post('/cadastro/juridica', [AutocadastroController::class, 'storeJuridica'])->name('cadastro.store.juridica');

Route::get('/fragmentos/entrar', [FragmentoAcessoController::class, 'entrar'])->name('fragmentos.entrar');
Route::get('/fragmentos/cadastro/fisica', [FragmentoAcessoController::class, 'cadastroFisica'])->name('fragmentos.cadastro.fisica');
Route::get('/fragmentos/cadastro/juridica', [FragmentoAcessoController::class, 'cadastroJuridica'])->name('fragmentos.cadastro.juridica');

Route::middleware('auth')->group(function () {
    Route::post('/sair', [AcessoController::class, 'sair'])->name('acesso.sair');

    Route::view('/minhas-solicitacoes', 'portal.minhas-solicitacoes')->name('minhas-solicitacoes.index');

    Route::get('/servicos', [Servico156Controller::class, 'index'])->name('servicos.index');

    Route::get('/servicos/156/categorias', [Servico156Controller::class, 'listarCategoriasPortal156'])
        ->name('servicos.156.categorias');

    Route::get('/servicos/156/categorias/{slugDaCategoria}', [Servico156Controller::class, 'listarServicosDaCategoria'])
        ->where('slugDaCategoria', '[a-z0-9-]+')
        ->name('servicos.156.servicos');

    Route::get('/servicos/156/solicitar/{slugDoServico}', [Servico156Controller::class, 'criarSolicitacao'])
        ->where('slugDoServico', '[a-z0-9-]+')
        ->name('servicos.156.solicitacao.criar');

    Route::post('/servicos/156/solicitar/{slugDoServico}', [Servico156Controller::class, 'registrarSolicitacao'])
        ->where('slugDoServico', '[a-z0-9-]+')
        ->name('servicos.156.solicitacao.registrar');

    Route::get('/servicos/156/solicitacoes/{protocolo}', [Servico156Controller::class, 'confirmacaoSolicitacao'])
        ->where('protocolo', '156-\d{4}-\d{6}')
        ->name('servicos.156.solicitacao.confirmacao');

    Route::get('/ouvidoria', [OuvidoriaController::class, 'index'])->name('ouvidoria.index');

    Route::get('/ouvidoria/manifestar/{tipo}', [OuvidoriaController::class, 'criarManifestacao'])
        ->where('tipo', implode('|', array_keys(ManifestacaoOuvidoria::TIPOS)))
        ->name('ouvidoria.manifestacao.criar');

    Route::post('/ouvidoria/manifestar/{tipo}', [OuvidoriaController::class, 'registrarManifestacao'])
        ->where('tipo', implode('|', array_keys(ManifestacaoOuvidoria::TIPOS)))
        ->name('ouvidoria.manifestacao.registrar');

    Route::get('/ouvidoria/manifestacoes/{protocolo}', [OuvidoriaController::class, 'confirmacaoManifestacao'])
        ->where('protocolo', 'OUV-\d{4}-\d{6}')
        ->name('ouvidoria.manifestacao.confirmacao');

    Route::get('/e-sic', [EsicController::class, 'index'])->name('esic.index');

    Route::get('/e-sic/pedido', [EsicController::class, 'criar'])->name('esic.solicitacao.criar');
    Route::post('/e-sic/pedido', [EsicController::class, 'store'])->name('esic.solicitacao.store');
    
    Route::get('/e-sic/pedido/{protocolo}/confirmacao', [EsicController::class, 'confirmacao'])
        ->where('protocolo', 'ESIC-\d{4}-\d{6}')
        ->name('esic.solicitacao.confirmacao');
});
