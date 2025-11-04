<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // Relações
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();

            // Informações principais
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('brand', 255)->nullable();
            $table->string('size', 255)->nullable();
            $table->json('colors')->nullable(); 

            // Estado e detalhes de uso
            $table->enum('condition', [
                'novo', 
                'usado_excelente', 
                'usado_bom', 
                'usado_regular', 
                'danificado'
            ])->default('novo');

            // Valores e compra
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->boolean('for_sale')->default(false);
            $table->decimal('price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();

            // Mídias e tags
            $table->json('images')->nullable(); 
            $table->json('tags')->nullable(); 

            // Uso e favoritos
            $table->integer('usage_count')->default(0);
            $table->date('last_used')->nullable();
            $table->boolean('is_favorite')->default(false);

            // Estilo e clima
            $table->enum('season', ['primavera', 'verao', 'outono', 'inverno', 'todas'])->default('todas');
            $table->enum('occasion', ['casual', 'trabalho', 'festa', 'esporte', 'formal', 'todas'])->default('todas');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('items');
    }
};
