<?php

namespace Tests\Feature;

use App\Exceptions\FalhaAoArmazenarAnexo;
use App\Services\ArmazenadorDeAnexos;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ArmazenadorDeAnexosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_grava_o_arquivo_no_disco_privado_e_devolve_o_caminho(): void
    {
        $caminho = (new ArmazenadorDeAnexos())->armazenar(
            UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg'),
            'solicitacoes-156/156-2026-000001',
        );

        Storage::disk('local')->assertExists($caminho);
    }

    public function test_lanca_excecao_quando_a_gravacao_devolve_false(): void
    {
        $arquivo = Mockery::mock(UploadedFile::class);
        $arquivo->shouldReceive('store')->once()->andReturn(false);

        $this->expectException(FalhaAoArmazenarAnexo::class);

        (new ArmazenadorDeAnexos())->armazenar($arquivo, 'solicitacoes-156/156-2026-000001');
    }

    public function test_apaga_os_arquivos_informados(): void
    {
        Storage::disk('local')->put('solicitacoes-156/a.jpg', 'conteudo');
        Storage::disk('local')->put('solicitacoes-156/b.jpg', 'conteudo');

        (new ArmazenadorDeAnexos())->apagar(['solicitacoes-156/a.jpg', 'solicitacoes-156/b.jpg']);

        Storage::disk('local')->assertMissing('solicitacoes-156/a.jpg');
        Storage::disk('local')->assertMissing('solicitacoes-156/b.jpg');
    }
}
