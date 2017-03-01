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
    private static $human = null;
    private $humans = array(
        '123' => array('test' => true),
    );

    /**
     * Constructs a test case with the given name.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::$human = new Human;
    }

    public function testSetHumans()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $result = self::$human->setHumans($this->humans, true);
        $this->assertEquals($result, self::$human);
    }

    /**
     * @depends testSetHumans
     */
    public function testGetHumans()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $this->humans['123']['isMain'] = true;

        $result = self::$human->getHumans();
        $this->assertEquals($result, $this->humans);
    }

    /**
     * @depends testSetHumans
     */
    public function testGetEmptyIds()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $relations = array(
            array(
                'main_person_id' => 123,
                'slave_person_id'=> 124
            )
        );

        $result = self::$human->getEmptyIds($relations);
        $this->assertEquals($result, array(124));
    }
}
