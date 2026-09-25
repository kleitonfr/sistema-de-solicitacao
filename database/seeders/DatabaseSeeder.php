<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Usuário de teste para permitir validar o fluxo de login (tela de
     * acesso -> tela de escolha de serviço) antes da integração com a API
     * real de autenticação. Ver AcessoController::store.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Usuário de Teste',
            'email' => 'teste@teste.com',
            'password' => bcrypt('12345678'),
        ]);
    }
}
