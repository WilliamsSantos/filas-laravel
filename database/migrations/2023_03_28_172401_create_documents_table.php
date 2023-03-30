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
        Schema::create('documents', function (Blueprint $table) {
            # Ajustes:
            // 1- utilizei o método foreignId que: 
            //   - já cria uma coluna UNSIGNED BIGINT usando a conversão do laravel
            //     e identifica pela conversão a tabela(category) e coluna (id)
            // 2- onUpdate pois posteriormente pode ser feito algum tipo de update nos dados
            // 3- adicionei o softDeletes pois é uma técnica mais seguro

            $table->id();
            $table->string('title', 60)
                ->nullable(false);

            $table->text('content');

            $table->integer('exercice_year')
                ->nullable(false);

            $table->foreignId('category_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
