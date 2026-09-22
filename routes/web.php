<?php

use App\Http\Controllers\AcessoController;
use App\Http\Controllers\AutocadastroController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('cadastro.index');
});

Route::get('/cadastro', [AutocadastroController::class, 'index'])->name('cadastro.index');
Route::post('/cadastro', [AutocadastroController::class, 'store'])->name('cadastro.store');

Route::get('/entrar', [AcessoController::class, 'index'])->name('acesso.index');
Route::post('/entrar', [AcessoController::class, 'store'])->name('acesso.store');
