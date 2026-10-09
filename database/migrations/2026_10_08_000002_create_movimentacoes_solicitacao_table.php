<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Apelidos gravados em solicitacao_type (ver Relation::morphMap em
     * AppServiceProvider). Ficam escritos aqui porque uma migration não deve
     * depender do código da aplicação, que muda com o tempo.
     */
    private const APELIDO_POR_TABELA = [
        'solicitacoes_156' => 'solicitacao_156',
        'solicitacoes_esic' => 'solicitacao_esic',
        'manifestacoes_ouvidoria' => 'manifestacao_ouvidoria',
    ];

    public function up(): void
    {
        Schema::create('movimentacoes_solicitacao', function (Blueprint $table) {
            $table->id();
            $table->morphs('solicitacao', 'movimentacoes_solicitacao_origem_index');
            $table->string('status_anterior', 20)->nullable();
            $table->string('status_novo', 20);
            $table->string('mensagem', 500)->nullable();
            $table->string('registrado_por', 60);
            $table->timestamp('created_at')->useCurrent();
        });

        $this->registrarRecebimentoDasSolicitacoesExistentes();
    }

    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_solicitacao');
    }

    /**
     * As solicitações feitas antes do acompanhamento ficam com status
     * "recebida" (padrão da coluna) e precisam da movimentação
     * correspondente para aparecerem na linha do tempo.
     */
    private function registrarRecebimentoDasSolicitacoesExistentes(): void
    {
        foreach (self::APELIDO_POR_TABELA as $tabela => $apelido) {
            DB::table($tabela)->orderBy('id')->chunkById(500, function ($solicitacoes) use ($apelido) {
                $movimentacoes = $solicitacoes->map(fn ($solicitacao) => [
                    'solicitacao_type' => $apelido,
                    'solicitacao_id' => $solicitacao->id,
                    'status_anterior' => null,
                    'status_novo' => 'recebida',
                    'mensagem' => 'Recebemos sua solicitação.',
                    'registrado_por' => 'sistema',
                    'created_at' => $solicitacao->created_at,
                ])->all();

                DB::table('movimentacoes_solicitacao')->insert($movimentacoes);
            });
        }
    }
};
