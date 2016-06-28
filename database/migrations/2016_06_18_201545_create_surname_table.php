<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurnameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surname', function (Blueprint $table) {
            $table->increments('id');
            $table->string('male', 45)->comment = 'Фамилия в мужском роде';
            $table->string('female', 45)->comment = 'Фамилия в женском роде';
            $table->timestamps();
        });

        DB::statement("alter table `surname` comment 'Список фамилий'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('surname');
    }
}
