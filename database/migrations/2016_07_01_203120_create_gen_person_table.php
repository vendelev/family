<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenPersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gen_person', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('FNAME_ID');
            $table->string('SNAME_ID');
            $table->string('SURNAME_ID');
            $table->string('BSURNAME_ID');
            $table->dateTime('BDATE');
            $table->dateTime('DDATE');
            $table->integer('USER_ID');
        });

        $this->insertOldData();

        $persons = DB::table('gen_person')->get();
        $inserts = array();
        $p_events= array();

        foreach ($persons as $person) {

            $inserts[] = array(
                'id'         => $person->ID,
                'fname_id'   => $person->FNAME_ID,
                'lname_id'   => $person->SNAME_ID,
                'sname_id'   => $person->SURNAME_ID,
                'bname_id'   => $person->BSURNAME_ID,
                'status'     => 'pub',
                'created_at' => date("Y-m-d H:i:s"),
            );
            
            $this->insetEvent($person->BDATE, 'bth', $person->ID, $p_events);
            $this->insetEvent($person->DDATE, 'dth', $person->ID, $p_events);
        };

        DB::table('person')->insert($inserts);
        DB::table('event_person')->insert($p_events);

        Schema::drop('gen_person');
    }

    private function insertOldData() {

        DB::statement("INSERT INTO `gen_person` VALUES (1,1,3,1,1,'1980-09-14 22:49:00','0000-00-00 00:00:00',1),(2,2,4,1,2,'1982-04-18 10:00:00','0000-00-00 00:00:00',1),(3,6,10,1,3,'1946-03-10 00:00:00','0000-00-00 00:00:00',1),(4,5,4,2,2,'1994-08-06 00:00:00','0000-00-00 00:00:00',1),(5,3,9,1,1,'1951-03-09 00:00:00','0000-00-00 00:00:00',1)");
    }

    private function insetEvent($date, $type, $pid, &$p_events) {

        if ($date != '0000-00-00 00:00:00') {

            $id = DB::table('event')->insertGetId([
                'datetime'   => $date,
                'description'=> '',
                'status'     => 'pub',
                'created_at' => date("Y-m-d H:i:s"),
            ]);

            $p_events[] = array(
                'person_id' => $pid,
                'event_id'  => $id,
                'type'      => $type,
                'created_at' => date("Y-m-d H:i:s"),
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('person')->truncate();
        DB::table('event_person')->truncate();
    }
}
