<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToItemsTable extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            // opção moderna: foreignId
            $table->foreignId('company_id')
                  ->nullable()
                  ->constrained('companies') // referencia companies(id)
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
          // $table->dropForeign([\'company_id\"]);
          // $table->dropColumn(\'company_id\');
        });
    }
}
