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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('spent_amount', 10, 2)->default(0);
            $table->enum('period', ['mensal', 'trimestral', 'semestral', 'anual', 'personalizado']);
            $table->date('start_date');
            $table->date('end_date');
            $table->json('categories')->nullable(); // Categorias incluídas no orçamento
            $table->tinyInteger('is_active')->default(true);
            $table->tinyInteger('notify_on_limit')->default(true);
            $table->decimal('notification_threshold', 5, 2)->default(80.00); // % para notificar
            $table->timestamps(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
