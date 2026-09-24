<?php

use App\Http\Controllers\AcessoController;
use App\Http\Controllers\AutocadastroController;
use App\Http\Controllers\EsicAcessoController;
use App\Http\Controllers\EsicAutocadastroController;
use App\Http\Controllers\FragmentoAcessoController;
use App\Http\Controllers\FragmentoEsicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('cadastro.index');
});

/* ============================= 156 MUNICÍPIO + OUVIDORIA ============================= */
Route::get('/entrar', [AcessoController::class, 'index'])->name('acesso.index');
Route::post('/entrar', [AcessoController::class, 'store'])->name('acesso.store');

Route::get('/cadastro', [AutocadastroController::class, 'index'])->name('cadastro.index');
Route::post('/cadastro', [AutocadastroController::class, 'store'])->name('cadastro.store');

Route::get('/fragmentos/entrar', [FragmentoAcessoController::class, 'entrar'])->name('fragmentos.entrar');
Route::get('/fragmentos/cadastro', [FragmentoAcessoController::class, 'cadastro'])->name('fragmentos.cadastro');


/* ============================= ESIC ============================= */
Route::get('/esic/entrar', [EsicAcessoController::class, 'index'])->name('esic.acesso.index');
Route::post('/esic/entrar', [EsicAcessoController::class, 'store'])->name('esic.acesso.store');

Route::get('/esic/cadastro', [EsicAutocadastroController::class, 'index'])->name('esic.cadastro.index');
Route::post('/esic/cadastro', [EsicAutocadastroController::class, 'store'])->name('esic.cadastro.store');

Route::get('/esic/fragmentos/entrar', [FragmentoEsicController::class, 'entrar'])->name('fragmentos.esic.entrar');
Route::get('/esic/fragmentos/cadastro', [FragmentoEsicController::class, 'cadastro'])->name('fragmentos.esic.cadastro');
