<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('person', function (Blueprint $table) {
            
            $table->enum('status', array('pub', 'del'))->after('bname_id')->comment = 'Статус доступности: pub - опубликовано, del - удалено';
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
        Schema::table('person', function (Blueprint $table) {
            
            $table->dropColumn('photo');
            $table->dropColumn('status');
        });
    }
}
