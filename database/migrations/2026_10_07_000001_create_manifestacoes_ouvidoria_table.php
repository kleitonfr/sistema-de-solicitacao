<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manifestacoes_ouvidoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('protocolo', 20)->unique();
            $table->string('tipo', 20);
            $table->string('orgao_ou_setor')->nullable();
            $table->text('descricao');
            $table->date('data_do_fato')->nullable();
            $table->string('local_do_fato')->nullable();
            $table->string('servidor_ou_setor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manifestacoes_ouvidoria');
    }
};
