<?php

use App\Http\Controllers\AutocadastroController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('cadastro.index');
});

Route::get('/cadastro', [AutocadastroController::class, 'index'])->name('cadastro.index');
Route::post('/cadastro', [AutocadastroController::class, 'store'])->name('cadastro.store');
