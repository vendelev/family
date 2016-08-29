<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifySurnames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surnames', function (Blueprint $table) {
            $table->index('male', 'male');
            $table->index('female', 'female');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surnames', function (Blueprint $table) {
            $table->dropIndex('male');
            $table->dropIndex('female');
        });
    }
}
