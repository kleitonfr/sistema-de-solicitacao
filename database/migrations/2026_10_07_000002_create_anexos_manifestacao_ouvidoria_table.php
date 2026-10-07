<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anexos_manifestacao_ouvidoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manifestacao_id')->constrained('manifestacoes_ouvidoria')->cascadeOnDelete();
            $table->string('nome_original');
            $table->string('caminho');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anexos_manifestacao_ouvidoria');
    }
};
