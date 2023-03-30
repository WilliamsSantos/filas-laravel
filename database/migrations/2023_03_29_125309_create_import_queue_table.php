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
        Schema::create('import_queue', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->nullabel(false);
            $table->string('filename')->nullabel(false);
            $table->json('content')->nullable(false);
            $table->enum('status', [
                'pending', 
                'processed', 
                'failed'
            ])->default('pending');

            $table->date('processed_at')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_queue');
    }
};
