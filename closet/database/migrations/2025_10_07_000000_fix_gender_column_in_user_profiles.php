<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Atualizar valores existentes para o novo formato
        DB::table("user_profiles")->where("gender", "masculino")->update(["gender" => "male"]);
        DB::table("user_profiles")->where("gender", "feminino")->update(["gender" => "female"]);
        DB::table("user_profiles")->where("gender", "outro")->update(["gender" => "other"]);
        DB::table("user_profiles")->where("gender", "prefiro_nao_dizer")->update(["gender" => "prefer_not_to_say"]);

        // Modificar a coluna para aceitar os novos valores
        // DB::statement("ALTER TABLE user_profiles MODIFY COLUMN gender ENUM(\'male\', \'female\', \'other\', \'prefer_not_to_say\') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter valores para o formato antigo
        DB::table("user_profiles")->where("gender", "male")->update(["gender" => "masculino"]);
        DB::table("user_profiles")->where("gender", "female")->update(["gender" => "feminino"]);
        DB::table("user_profiles")->where("gender", "other")->update(["gender" => "outro"]);
        DB::table("user_profiles")->where("gender", "prefer_not_to_say")->update(["gender" => "prefiro_nao_dizer"]);

        // Reverter a coluna para os valores antigos
        // DB::statement("ALTER TABLE user_profiles MODIFY COLUMN gender ENUM(\'masculino\', \'feminino\', \'outro\', \'prefiro_nao_dizer\') NULL");
    }
};
