<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();

            // 🔹 Relacionamento com empresas
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade');

            $table->string('title', 255);
            $table->text('description');

            $table->enum('type', [
                'desconto_percentual',
                'desconto_valor',
                'frete_gratis',
                'brinde',
                'outro'
            ]);

            // 🔹 Campos de desconto
            $table->decimal('discount_percentage', 5, 2)->nullable(); // Ex: 10.50%
            $table->decimal('discount_amount', 10, 2)->nullable();    // Ex: R$ 100,00
            $table->decimal('minimum_purchase', 10, 2)->nullable();   // Valor mínimo para aplicar promoção

            // 🔹 Outras informações
            $table->string('coupon_code', 255)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);

            // 🔹 Mídia e termos
            $table->string('image', 255)->nullable();
            $table->text('terms_conditions')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
