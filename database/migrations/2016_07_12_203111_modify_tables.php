<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('person',    'humans');
        Schema::rename('event',     'events');
        Schema::rename('name',      'names');
        Schema::rename('surname',   'surnames');
        Schema::rename('relation',  'relations');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('humans',   'person');
        Schema::rename('events',    'event');
        Schema::rename('names',     'name');
        Schema::rename('surnames',  'surname');
        Schema::rename('relations', 'relation');
    }
}
