<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuPrincipalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_visitante_nao_autenticado_e_redirecionado_ao_abrir_minhas_solicitacoes(): void
    {
        $this->get(route('minhas-solicitacoes.index'))->assertRedirect(route('acesso.index'));
    }

    public function test_usuario_autenticado_abre_minhas_solicitacoes(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('minhas-solicitacoes.index'))
            ->assertOk()
            ->assertSee('Minhas solicitações');
    }

    public function test_pagina_sobre_156_e_publica(): void
    {
        $this->get(route('sobre156.index'))
            ->assertOk()
            ->assertSee('Sobre o 156');
    }

    public function test_menu_do_cabecalho_aponta_para_as_novas_paginas(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('sobre156.index'))
            ->assertSee(route('minhas-solicitacoes.index'), false)
            ->assertSee(route('sobre156.index'), false);
    }
}
