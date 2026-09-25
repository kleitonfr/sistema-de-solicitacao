<?php

use App\Http\Controllers\AcessoController;
use App\Http\Controllers\AutocadastroController;
use App\Http\Controllers\FragmentoAcessoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('cadastro.index');
});

Route::get('/entrar', [AcessoController::class, 'index'])->name('acesso.index');
Route::post('/entrar', [AcessoController::class, 'store'])->name('acesso.store');

Route::get('/cadastro', [AutocadastroController::class, 'index'])->name('cadastro.index');
Route::post('/cadastro', [AutocadastroController::class, 'store'])->name('cadastro.store');

Route::get('/fragmentos/entrar', [FragmentoAcessoController::class, 'entrar'])->name('fragmentos.entrar');
Route::get('/fragmentos/cadastro', [FragmentoAcessoController::class, 'cadastro'])->name('fragmentos.cadastro');
