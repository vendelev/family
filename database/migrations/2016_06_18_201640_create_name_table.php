<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('name', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('sex', array('m', 'f'))->index('sex')->comment = 'Пол имени: m - муж, f - жен';
            $table->string('fname', 45)->comment = 'Имя';
            $table->string('male_lname', 50)->comment = 'Муж отчество';
            $table->string('female_lname', 50)->comment = 'Жен отчество';
            $table->timestamps();
        });

        DB::statement("alter table `name` comment 'Список имен и отчеств'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('name');
    }
}
