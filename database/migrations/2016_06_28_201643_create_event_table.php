<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('person_id')->index('person')->comment = 'ID персоны';
            $table->enum('type', array('bth', 'dth', 'mrg'))->comment = 'Тип события: brh - рождение, brh - смерть, mrg - свадьба';
            $table->dateTime('datetime')->comment = 'Дата/время события';
            $table->text('description')->comment = 'Описание события';
            $table->timestamps();
        });

        DB::statement("alter table `event` comment 'События для персоны'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event');
    }
}
