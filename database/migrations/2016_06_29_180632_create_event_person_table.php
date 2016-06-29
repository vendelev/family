<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventPersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_person', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('person_id')->comment = 'ID персоны';
            $table->integer('event_id')->comment = 'ID события';
            $table->enum('type', array('bth', 'dth', 'mrg', 'bch'))->comment = 'Тип события: brh - рождение, dth - смерть, mrg - свадьба, bch - рождение ребенка';
            $table->timestamps();

            $table->index(['person_id', 'event_id'], 'person');
        });


        DB::statement("alter table `event_person` comment 'Связь событий и персон'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_person');
    }
}
