<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes_156', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('servico156_id')->constrained('servicos_156')->restrictOnDelete();
            $table->foreignId('categoria_servico_id')->constrained('categorias_servico')->restrictOnDelete();
            $table->string('protocolo', 20)->unique();
            $table->text('descricao');
            $table->string('telefone_contato', 15);
            $table->string('cep', 9);
            $table->string('logradouro');
            $table->string('numero', 20);
            $table->string('bairro');
            $table->string('ponto_referencia')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_156');
    }
};
