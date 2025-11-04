<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // 🔹 Cada empresa pertence a um usuário
            $table->foreignId("user_id")->nullable()->constrained()->onDelete("cascade");

            $table->string('name', 255);
            $table->string('cnpj', 14)->unique();   // 🔹 CNPJ único
            $table->string('email', 255)->unique(); // 🔹 Email único
            $table->string('password');             // 🔹 Senha de acesso da empresa

            // Extras opcionais
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
