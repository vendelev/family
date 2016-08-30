<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHumanTree extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('human_trees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('human_id')->comment = 'ID персоны';
            $table->string('family')->comment = 'ID фамилии';
            $table->timestamps();

            $table->index(['human_id', 'family'], 'family');
        });

        $humans = DB::table('humans')->get();
        $inserts = array();

        foreach ($humans as $human) {

            $inserts[] = array(
                'human_id'   => $human->id,
                'family'     => $human->sname_id,
                'created_at' => date("Y-m-d H:i:s"),
            );

            if ($human->bname_id!=$human->sname_id) {
                $inserts[] = array(
                    'human_id'   => $human->id,
                    'family'     => $human->bname_id,
                    'created_at' => date("Y-m-d H:i:s"),
                );
            }
        };

        DB::table('human_trees')->insert($inserts);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('human_trees');
    }
}
