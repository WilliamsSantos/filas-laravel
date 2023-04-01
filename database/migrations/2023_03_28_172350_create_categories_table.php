<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            // #ajustes
            // 1- no name da categoria eu adicionaei uma restrição de chave exclusiva 
            // para evitar a criação de categorias duplicadas.
            // 2- o unique já me gera uma index que nomeei categories_name_unique
            //      para uma melhor consulta dos dados futuramente
            // 3- adicionei um softDelete pois é uma técnica mais segura

            $table->id();
            $table->string('name', 20)
                ->nullable(false)
                ->unique('categories_name_unique');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
