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
        
        DB::statement("ALTER TABLE `name` CHANGE `male_lname` `male_mname` VARCHAR(45);");
        DB::statement("ALTER TABLE `name` CHANGE `female_lname` `female_mname` VARCHAR(45);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        DB::statement("ALTER TABLE `name` CHANGE `male_mname` `male_lname` VARCHAR(45);");
        DB::statement("ALTER TABLE `name` CHANGE `female_mname` `female_lname` VARCHAR(45);");
    }
}
