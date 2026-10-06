<?php

namespace Tests\Feature;

use App\Models\SolicitacaoEsic;
use App\Models\User;
use App\Services\GeradorDeProtocolo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeradorDeProtocoloTest extends TestCase
{
    use RefreshDatabase;

    public function test_gera_o_primeiro_protocolo_do_ano(): void
    {
        $ano = now()->year;

        $protocolo = app(GeradorDeProtocolo::class)->gerar('ESIC', SolicitacaoEsic::class);

        $this->assertSame("ESIC-{$ano}-000001", $protocolo);
    }

    public function test_sequencial_considera_as_solicitacoes_ja_registradas_no_ano(): void
    {
        $ano = now()->year;

        SolicitacaoEsic::create([
            'user_id' => User::factory()->create()->id,
            'protocolo' => "ESIC-{$ano}-000001",
            'assunto' => 'despesas',
            'descricao' => 'Descrição qualquer de uma solicitação existente.',
        ]);

        $protocolo = app(GeradorDeProtocolo::class)->gerar('ESIC', SolicitacaoEsic::class);

        $this->assertSame("ESIC-{$ano}-000002", $protocolo);
    }
}
