<?php
/**
 * Created by PhpStorm.
 * User: artiom
 * Date: 23.02.17
 * Time: 23:24
 */

use Family\Http\Controllers\Fio;


class FioTest extends TestCase
{
    public function testGetFio()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $human = array(
            'bname_id' => 1,
            'sname_id' => 2,
            'fname_id' => 1,
            'mname_id' => 2,
        );

        $surnames = array(
            1 => ['male' => 'Test1'],
            2 => ['male' => 'Test2'],
        );
        $names = array(
            1 => ['fname' => 'Test3', 'sex' => 'm'],
            2 => ['male_mname' => 'Test4'],
        );

        $fio  = new Fio();
        $result = $fio->getFio($human, $surnames, $names);

        $this->assertEquals($result, array(
            'bname' => 'Test1',
            'sname' => 'Test2',
            'fname' => 'Test3',
            'mname' => 'Test4',
        ));
    }
}
