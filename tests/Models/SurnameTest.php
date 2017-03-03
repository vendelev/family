<?php
/**
 * Created by PhpStorm.
 * User: artiom
 * Date: 24.02.17
 * Time: 0:10
 */


use Family\Models\Surname;


class SurnameTest extends TestCase
{
    public function testSetGetIds()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $humans = array(array(
            'sname_id' => 1,
            'bname_id' => 2,
            'fname_id' => 3,
            'mname_id' => 4,
        ));

        $name  = new Surname();
        $result= $name->setIds($humans)->getIds();
        $this->assertEquals($result, array(1,2));
    }
}
