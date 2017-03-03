<?php

use Family\Http\Controllers\Forest;

class ForestTest extends TestCase
{
    protected function getHumans()
    {
        return [
            11 => [
                'fio' => [
                    'bname' => 'Sur1',
                    'sname' => 'Sur2',
                    'fname' => 'Name3',
                    'mname' => 'Name4',
                ],
            ],
            12 => [
                'fio' => [
                    'bname' => 'Sur1',
                    'sname' => 'Sur1',
                    'fname' => 'Name4',
                    'mname' => 'Name5',
                ],
                'isMain' => true,
            ],
            13 => [
                'fio' => [
                    'bname' => 'Sur1',
                    'sname' => 'Sur1',
                    'fname' => 'Name6',
                    'mname' => 'Name3',
                ],
                'isMain' => true,
            ],
        ];
    }

    protected function getRelations()
    {
        return [
            [
                'main_person_id' => 12,
                'slave_person_id'=> 11,
                'type'           => 'mrg'
            ],
            [
                'main_person_id' => 12,
                'slave_person_id'=> 13,
                'type'           => 'prt'
            ],
            [
                'main_person_id' => 11,
                'slave_person_id'=> 13,
                'type'           => 'prt'
            ],
        ];
    }

    /**
     * @test
     */
    public function getHuman()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $humans = $this->getHumans();
        $result = $this->callPrivateMethod('getHuman', array(12, $humans));

        $human  = $humans[12];
        $human['marriage'] = [];
        $human['children'] = [];

        $this->assertEquals($result, $human);
    }

    /**
     * @test
     */
    public function getEmptyHuman()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $result = $this->callPrivateMethod('getHuman', array(0, array()));

        $human  = [
            'fio' => [
                'bname' => '',
                'sname' => '',
                'fname' => '',
                'mname' => '',
            ],
            'marriage' => [],
            'children' => [],
        ];

        $this->assertEquals($result, $human);
    }

    /**
     * @test
     */
    public function getTree()
    {
        fwrite(STDOUT, "\n". __METHOD__);

        $forest    = new Forest();
        $relations = $this->getRelations();
        $humans    = $this->getHumans();
        $result    = $forest->get($relations, $humans);

        $humans[11]['children'][13] = $humans[13];
        $humans[12]['marriage'][11] = $humans[11];

        unset($humans[11]);
        unset($humans[13]);

        $this->assertEquals($result, $humans);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->className= '\Family\Http\Controllers\Forest';
        $this->object   = new $this->className();
    }
}
