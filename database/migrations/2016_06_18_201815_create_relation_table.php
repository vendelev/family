<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('main_person_id')->index('main')->comment = 'Главная персона, для mr - мужчина, для ch - родитель';
            $table->integer('slave_person_id')->index('slave')->comment = 'Зависимая персона, для mr - женщина, для ch - ребенок';
            $table->enum('type', array('mrg', 'prt'))->comment = 'Тип отношений: mr - супружество, prt - родительство';
            $table->timestamps();
        });

        DB::statement("alter table `relation` comment 'Взаимоотношения персон'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('relation');
    }
}
