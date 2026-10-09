<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABELAS_DAS_SOLICITACOES = [
        'solicitacoes_156',
        'solicitacoes_esic',
        'manifestacoes_ouvidoria',
    ];

    public function up(): void
    {
        foreach (self::TABELAS_DAS_SOLICITACOES as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->string('status', 20)->default('recebida')->index();
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABELAS_DAS_SOLICITACOES as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            });
        }
    }
};
