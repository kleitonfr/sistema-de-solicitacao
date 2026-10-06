<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anexos_solicitacao_156', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitacao156_id')->constrained('solicitacoes_156')->cascadeOnDelete();
            $table->string('nome_original');
            $table->string('caminho');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anexos_solicitacao_156');
    }
};
