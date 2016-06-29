<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event', function (Blueprint $table) {

            $table->dropColumn('person_id');
            $table->dropColumn('type');

            $table->enum('status', array('pub', 'del'))->after('description')->comment = 'Статус события: pub - опубликовано, del - удалено';
            $table->string('photo')->after('status')->comment = 'Путь к файлу от public';

            $table->index(['id', 'status'], 'status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event', function (Blueprint $table) {
            
            $table->integer('person_id')->index('person')->after('id')->comment = 'ID персоны';
            $table->enum('type', array('bth', 'dth', 'mrg'))->after('person_id')->comment = 'Тип события: brh - рождение, brh - смерть, mrg - свадьба';
            
            $table->dropColumn('photo');
            $table->dropColumn('status');
        });
    }
}
