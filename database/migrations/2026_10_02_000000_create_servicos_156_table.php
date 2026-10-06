<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicos_156', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_servico_id')
                ->constrained('categorias_servico')
                ->restrictOnDelete();
            $table->string('nome', 150);
            $table->string('slug', 170)->unique();
            $table->text('descricao');
            $table->timestamps();

            $table->unique(['categoria_servico_id', 'nome']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicos_156');
    }
};
