<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMixedSurnameLangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mixed_surname_lang', function (Blueprint $table) {
            $table->integer('SURNAME_ID');
            $table->integer('SURNAME_LANG_ID');
        });

        DB::statement("INSERT INTO `mixed_surname_lang` VALUES (1,1),(2,2),(3,3),(1,4),(4,5),(5,6),(6,7),(7,8),(8,9),(8,10),(10,11),(11,12),(12,13),(13,14),(14,15),(15,16),(15,17),(6,18),(11,19),(16,20),(16,21),(17,22),(18,23),(19,24),(20,25),(21,26),(22,27),(10,28),(2,29),(21,30),(18,31),(13,32),(7,33),(4,34),(3,35),(17,36),(14,37),(5,38),(12,40),(20,41),(23,42),(24,43),(25,44),(26,45),(27,46),(28,47),(29,48),(30,49),(31,50),(32,51)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
