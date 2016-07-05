<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::statement("ALTER TABLE `name` CHANGE `male_lname` `male_sname` VARCHAR(45);");
        DB::statement("ALTER TABLE `name` CHANGE `female_lname` `female_sname` VARCHAR(45);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        DB::statement("ALTER TABLE `name` CHANGE `male_sname` `male_lname` VARCHAR(45);");
        DB::statement("ALTER TABLE `name` CHANGE `female_sname` `female_lname` VARCHAR(45);");
    }
}
