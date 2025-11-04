<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->id();

            // Usuário que está seguindo
            $table->unsignedBigInteger('follower_id');

            // Usuário que está sendo seguido
            $table->unsignedBigInteger('followed_id');

            $table->timestamps();

            // Garantir que não haja duplicação
            $table->unique(['follower_id', 'followed_id']);

            // Chaves estrangeiras
            $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('followed_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
