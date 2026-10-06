<?php

namespace Tests\Feature;

use App\Logging\ContextoDaFalha;
use App\Logging\RegistroDeSeguranca;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use PDOException;
use RuntimeException;
use Tests\TestCase;

class RegistrosDeLogTest extends TestCase
{
    public function test_aviso_de_seguranca_vai_para_o_canal_proprio_com_o_ip_do_cliente(): void
    {
        Context::addHidden('ip_do_cliente', '203.0.113.9');

        Log::shouldReceive('channel')->once()->with('seguranca')->andReturnSelf();
        Log::shouldReceive('warning')->once()->with('login_falhou', [
            'usuario_id' => 7,
            'ip' => '203.0.113.9',
        ]);

        (new RegistroDeSeguranca())->aviso('login_falhou', ['usuario_id' => 7]);
    }

    public function test_contexto_da_falha_nao_expoe_os_valores_do_sql_de_uma_query_exception(): void
    {
        $erro = new QueryException(
            'sqlite',
            'insert into solicitacoes_156 (descricao) values (?)',
            ['Meu telefone é (12) 98765-4321'],
            new PDOException('SQLSTATE[23000]: Integrity constraint violation'),
        );

        $contexto = ContextoDaFalha::deExcecao($erro);

        $this->assertStringNotContainsString('98765-4321', $contexto['mensagem_da_excecao']);
        $this->assertSame('SQLSTATE[23000]: Integrity constraint violation', $contexto['mensagem_da_excecao']);
    }

    public function test_contexto_da_falha_mantem_a_mensagem_de_excecoes_comuns(): void
    {
        $contexto = ContextoDaFalha::deExcecao(new RuntimeException('Disco cheio'));

        $this->assertSame('Disco cheio', $contexto['mensagem_da_excecao']);
        $this->assertSame(RuntimeException::class, $contexto['tipo_da_excecao']);
    }

    public function test_toda_resposta_devolve_o_identificador_de_correlacao(): void
    {
        $resposta = $this->get('/up');

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{12}$/',
            (string) $resposta->headers->get('X-Identificador-Correlacao'),
        );
    }
}
