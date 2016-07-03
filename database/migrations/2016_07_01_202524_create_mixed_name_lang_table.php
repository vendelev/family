<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMixedNameLangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mixed_name_lang', function (Blueprint $table) {
            $table->integer('NAME_ID');
            $table->integer('NAME_LANG_ID');
        });

        DB::statement("INSERT INTO `mixed_name_lang` VALUES (1,1),(1,14),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(8,8),(9,9),(10,10),(11,11),(11,70),(12,12),(13,13),(14,15),(14,16),(15,17),(15,18),(16,19),(17,20),(18,21),(19,22),(20,23),(21,24),(22,25),(23,26),(24,27),(25,28),(26,29),(26,30),(27,31),(27,32),(28,33),(29,34),(30,35),(31,36),(32,37),(32,38),(33,39),(34,40),(35,41),(36,42),(37,43),(38,44),(39,45),(40,46),(41,47),(42,48),(43,49),(44,50),(45,51),(46,52),(47,53),(48,54),(49,55),(50,56),(51,57),(52,58),(53,59),(54,60),(55,61),(56,62),(57,63),(58,64),(59,65),(60,66),(61,67),(62,68),(63,69),(64,71),(65,72),(66,73),(67,74),(68,75),(69,76),(70,77),(71,78),(72,79),(73,80),(74,81),(75,82),(76,83),(77,84),(78,85),(79,86),(80,87),(81,88)");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mixed_name_lang');
    }
}
