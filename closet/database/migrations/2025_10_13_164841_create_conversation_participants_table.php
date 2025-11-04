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
        // Garante que a tabela exista apenas se não existir
        if (!Schema::hasTable('conversation_participants')) {
            Schema::create('conversation_participants', function (Blueprint $table) {
                $table->id();

                // Foreign key obrigatória
                $table->foreignId('conversation_id')
                    ->constrained('conversations')
                    ->onDelete('cascade');

                // Foreign keys opcionais
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId('company_id')
                    ->nullable()
                    ->constrained('companies')
                    ->nullOnDelete();

                $table->timestamps();

                // Evita duplicidade
                $table->unique(['conversation_id', 'user_id', 'company_id'], 'conv_part_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_participants');
    }
};

