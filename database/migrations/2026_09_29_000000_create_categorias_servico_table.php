<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_servico', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 120)->unique();
            $table->text('descricao');
            $table->boolean('em_destaque')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_servico');
    }
};
