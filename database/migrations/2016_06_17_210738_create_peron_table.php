<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeronTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fname_id')->comment = 'ID имени';
            $table->integer('lname_id')->comment = 'ID имени/отчества';
            $table->integer('sname_id')->comment = 'ID текущей фамилии';
            $table->integer('bname_id')->comment = 'ID фамилии по рождению';
            $table->timestamps();
        });

        DB::statement("alter table `person` comment 'Список персон'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('person');
    }
}
