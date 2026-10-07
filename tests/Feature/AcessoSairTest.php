<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcessoSairTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_logado_sai_e_e_redirecionado_para_entrar(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->post(route('acesso.sair'))
            ->assertRedirect(route('acesso.index'));

        $this->assertGuest();
    }

    public function test_depois_de_sair_as_paginas_protegidas_exigem_novo_login(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->post(route('acesso.sair'));

        $this->get(route('servicos.index'))->assertRedirect(route('acesso.index'));
    }

    public function test_visitante_nao_autenticado_e_redirecionado_para_entrar(): void
    {
        $this->post(route('acesso.sair'))->assertRedirect(route('acesso.index'));
    }

    public function test_saida_nao_aceita_requisicao_get(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/sair')
            ->assertStatus(405);

        $this->assertAuthenticated();
    }
}
