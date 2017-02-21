<?php

// use Illuminate\Foundation\Testing\WithoutMiddleware;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\DatabaseTransactions;

use Family\Http\Controllers\FamilyTree;

class FamilyTreeTest extends TestCase
{
    public function testGetEmptyHumanIds()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $humans = array(
            '123' => array(true),
        );
        $relations = array(
            array(
                'main_person_id' => 123,
                'slave_person_id'=> 124
            )
        );

        $result = $this->callPrivateMethod('getEmptyHumanIds', array($relations, $humans));
        $this->assertEquals($result, array(124));
    }

    public function testGetNameIds()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $humans = array(array(
            'sname_id' => 1,
            'bname_id' => 2,
            'fname_id' => 3,
            'mname_id' => 4,
        ));

        $result = $this->callPrivateMethod('getNameIds', array($humans));
        $this->assertEquals($result, array('sname' => array(1,2), 'name' => array(3,4)));
    }

    public function testGetHumanName()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $data = array(
            'id' => array(
                'name' => 123,
            )
        );
        $result = $this->callPrivateMethod('getHumanName', array($data, 'id', 'name'));
        $this->assertEquals($result, 123);
    }

    protected function setUp()
    {
        $this->className= '\Family\Http\Controllers\FamilyTree';
        $this->object   = new $this->className();
    }
}
