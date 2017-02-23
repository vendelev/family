<?php
/**
 * Created by PhpStorm.
 * User: artiom
 * Date: 23.02.17
 * Time: 23:24
 */

use Family\Models\Human;


class HumanTest extends TestCase
{
    public function testGetEmptyIds()
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

        $result = $this->callPrivateMethod('getEmptyIds', array($relations, $humans));
        $this->assertEquals($result, array(124));
    }

    protected function setUp()
    {
        $this->className= '\Family\Models\Human';
        $this->object   = new $this->className();
    }
}
