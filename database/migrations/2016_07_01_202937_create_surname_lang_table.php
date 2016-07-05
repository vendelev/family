<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurnameLangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surname_lang', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('MALE_SURNAME');
            $table->string('FEMALE_SURNAME');
            $table->string('LANG');
        });

        DB::statement("INSERT INTO `surname_lang` VALUES (1,'Венделев','Венделева','ru'),(2,'Гонеев','Гонеева','ru'),(3,'Пигорев','Пигорева','ru'),(4,'Vendelev','Vendeleva','en'),(5,'Ляпин','Ляпина','ru'),(6,'Ховяков','Ховякова','ru'),(7,'Доронин','Доронина','ru'),(8,'Лысенко','Лысенко','ru'),(9,'Иванов','Иванова','ru'),(10,'Ivanov','Ivanova','en'),(11,'Голушкин','Голушкина','ru'),(12,'Белоусов','Белоусова','ru'),(13,'Арцыбашев','Арцыбашева','ru'),(14,'Лихолетов','Лихолетова','ru'),(15,'Тулупов','Тулупова','ru'),(16,'Ефремов','Ефремова','ru'),(17,'Efremov','Efremova','en'),(18,'Doronin','Doronina','en'),(19,'Belousov','Belousova','en'),(20,'Лашин','Лашина','ru'),(21,'Lashin','Lashina','en'),(22,'Тисленков','Тисленкова','ru'),(23,'Коренев','Коренева','ru'),(24,'Сергеев','Сергеева','ru'),(25,'Абдулрахимов','Абдулрахимова','ru'),(26,'Деревягин','Деревягина','ru'),(27,'Никулин','Никулина','ru'),(28,'Golushkin','Golushkina','en'),(29,'Goneev','Goneeva','en'),(30,'Derevyagin','Derevyagina','en'),(31,'Korenev','Koreneva','en'),(32,'Liholetov','Liholetova','en'),(33,'Lisenko','Lisenko','en'),(34,'Lyapin','Lyapina','en'),(35,'Pigorev','Pigoreva','en'),(36,'Tislenkov','Tislenkova','en'),(37,'Tulupov','Tulupova','en'),(38,'Hovyakov','Hovyakova','en'),(40,'Arcibashev','Arcibasheva','en'),(41,'Abdulrahimov','Abdulrahimova','en'),(42,'Беляев','Беляева','ru'),(43,'Колесников','Колесникова','ru'),(44,'Гребеник','Гребеник','ru'),(45,'Шеромов','Шеромова','ru'),(46,'Гусар','Гусар','ru'),(47,'Синько','Синько','ru'),(48,'Стекачев','Стекачева','ru'),(49,'Щетинин','Щетинина','ru'),(50,'Лобанов','Лобанова','ru'),(51,'Татарков','Татаркова','ru')");

        $mixed_name_lang = DB::table('mixed_surname_lang')->get();
        $name_lang = DB::table('surname_lang')->where('LANG', 'ru')->get();
        
        $inserts = array();
        $mixed   = array();
        foreach ($mixed_name_lang as $name) {
            $mixed[$name->SURNAME_LANG_ID] = $name->SURNAME_ID;
        };


        foreach ($name_lang as $name) {
            $id  = $mixed[$name->ID];

            $inserts[$id] = array(
                'id'        => $id,
                'male'      => $name->MALE_SURNAME,
                'female'    => $name->FEMALE_SURNAME,
                'created_at'=> date("Y-m-d H:i:s"),
            );
        };

        DB::table('surname')->insert($inserts);

        Schema::drop('surname_lang');
        Schema::drop('mixed_surname_lang');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('surname')->truncate();
    }
}
