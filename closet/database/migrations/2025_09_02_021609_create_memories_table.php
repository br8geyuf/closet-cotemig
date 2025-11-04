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
        Schema::create('memories', function (Blueprint $table) {
            $table->id();

            // 🔗 Relacionamentos
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();

            // 📌 Campos principais
            $table->string('title', 255);
            $table->text('content');
            $table->date('memory_date');
            $table->string('location', 255)->nullable();
            $table->enum('occasion', [
                'casual',
                'trabalho',
                'festa',
                'viagem',
                'especial',
                'outro'
            ])->nullable();

            // 📷 Campos adicionais
            $table->json('photos')->nullable(); // Fotos da ocasião
            $table->json('tags')->nullable();   // Tags da memória

            // ⭐ Avaliação e favorito
            $table->unsignedTinyInteger('rating')->nullable(); // 1-5
            $table->boolean('is_favorite')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memories');
    }
};
