<?php

namespace Family\Http\Controllers;

use Illuminate\Http\Request;

use Family\Http\Requests;

use Family\Models\Human;
use Family\Models\Name;
use Family\Models\Surname;
use Family\Models\Relation;

class FamilyTree extends Controller
{

    private $surname  = null;
    private $surnames = array();
    private $relation = null;
    private $name     = null;
    private $names    = array();
    private $main_humans  = array();
    private $slave_humans = array();

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        $this->relation = new Relation;
    }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $human  = new Human;
        $surname= new Surname;
        $name   = new Name;
        $sname  = $request->input('surname');

        $this->main_humans = $human->getBySurname($sname);
        $this->slave_humans= $this->main_humans;

        $humansIds = array_keys($this->main_humans);
        $relations = $this->getRelations($humansIds);
        $emptyIds  = $this->getEmptyHumans($relations, $this->main_humans);

        while (!empty($emptyIds)) {

            $humans = $human->getByIds($emptyIds);

            foreach ($humans as $id => $item) {
                $this->slave_humans[$id] = $item;
            }

            $slave_relations = $this->getRelations($emptyIds);
            $relations = array_merge($relations, $slave_relations);
            $emptyIds  = $this->getEmptyHumans($slave_relations, $this->slave_humans);
        }

        $main_ids  = $this->getNameIds($this->main_humans);
        $slave_ids = $this->getNameIds($this->slave_humans);
        $sname_ids = array_merge($main_ids['sname'], $slave_ids['sname']);
        $name_ids  = array_merge($main_ids['name'], $slave_ids['name']);

        $this->surnames = $surname->getByIds(array_unique($sname_ids));
        $this->names    = $name->getByIds(array_unique($name_ids));

        $tree = $this->getForest($relations);
        $tree = $this->normolizeTree($tree);

        return view('family/forest', ['tree' => $tree]);
    }

    private function getRelations($ids)
    {
        $mrg_relations = $this->relation->getByField('main_person_id', $ids);
        $prt_relations = $this->relation->getByField('slave_person_id', $ids, 'mrg');
        $returnValue   = array_merge($mrg_relations, $prt_relations);

        return $returnValue;
    }

    private function getEmptyHumans($relations, &$humans)
    {
        $returnValue = array();

        foreach ($relations as $item) {

            $mpi = $item['main_person_id'];
            $spi = $item['slave_person_id'];

            if (empty($humans[$mpi])) {
                $returnValue[] = $mpi;
            }

            if (empty($humans[$spi])) {
                $returnValue[] = $spi;
            }
        }

        $returnValue = array_unique($returnValue);

        return $returnValue;
    }

    private function getNameIds($humans)
    {
        $returnValue = array(
            'sname' => array(),
            'name'  => array(),
        );

        foreach ($humans as $item) {
            
            if ($id = $this->getHumanId($returnValue['sname'], $item['sname_id'])) {
                $returnValue['sname'][]= $id;
            }
            
            if ($id = $this->getHumanId($returnValue['sname'], $item['bname_id'])) {
                $returnValue['sname'][]= $id;
            }
            
            if ($id = $this->getHumanId($returnValue['name'], $item['fname_id'])) {
                $returnValue['name'][]= $id;
            }
            
            if ($id = $this->getHumanId($returnValue['name'], $item['mname_id'])) {
                $returnValue['name'][]= $id;
            }
        }

        return $returnValue;
    }

    private function getHumanId($items, $id)
    {
        $returnValue = false;

        if (!in_array($id, $items)) {
            $returnValue = $id;
        }

        return $returnValue;
    }

    private function getForest($relations)
    {
        $returnValue = array();
        foreach ($relations as $item) {
            $mpi = $item['main_person_id'];
            $spi = $item['slave_person_id'];

            if (empty($returnValue[$mpi]) /*&& !empty($this->main_humans[$mpi])*/) {
                $returnValue[$mpi] = $this->getHuman($mpi);
            }
            if (empty($returnValue[$spi])) {
                $returnValue[$spi] = $this->getHuman($spi);
            }

            switch ($item['type']) {
                case 'prt':
                    if (empty($returnValue[$mpi]['children'])) {
                        $returnValue[$mpi]['children'] = array();
                    }
                    $returnValue[$mpi]['children'][$spi] = &$returnValue[$spi];
                    break;
                case 'mrg':
                    if (empty($returnValue[$mpi]['marriage'])) {
                        $returnValue[$mpi]['marriage'] = array();
                    }
                    if (empty($returnValue[$spi]['marriage'])) {
                        $returnValue[$spi]['marriage'] = array();
                    }
                    $returnValue[$mpi]['marriage'][$spi] = &$returnValue[$spi];
                    $returnValue[$spi]['marriage'][$mpi] = &$returnValue[$mpi];
                    break;
            }
        }

        $toRemove = array();
        foreach ($returnValue as $id => $item) {
            if (!empty($item['children'])) {
                foreach ($item['children'] as $key => $value) {
                    if (!empty($returnValue[$key])) {
                        // $returnValue[$id]['children'][$key] = &$returnValue[$key];
                        $toRemove[]= $key;
                    }
                }
            }
            if (!empty($item['marriage'])) {
                foreach ($item['marriage'] as $key => $value) {
                    if (!empty($returnValue[$key])) {
                        // $returnValue[$id]['marriage'][$key] = &$returnValue[$key];
                        // $returnValue[$key]['marriage'][$id] = &$returnValue[$id];
                        if (empty($this->main_humans[$key])) {
                            $toRemove[]= $key;
                        }
                    }
                }
            }
        }


        foreach ($toRemove as $key) {
            unset($returnValue[$key]);
        }

// var_dump($this->humans);
        return $returnValue;
    }

    private function getHuman($id)
    {
        if (!empty($this->main_humans[$id])) {
            $returnValue = $this->getHumanFio($this->main_humans[$id]);
        } elseif (!empty($this->slave_humans[$id])) {
            $returnValue = $this->getHumanFio($this->slave_humans[$id]);
        } else {
            $returnValue = array(
                'fio' => array(
                    'bname' => '',
                    'sname' => '',
                    'fname' => '',
                    'mname' => '',
                ),
            );
        }

        return $returnValue;
    }

    private function getHumanFio($human)
    {
        $returnValue = array(
            'fio' => array(
                'bname' => '',
                'sname' => '',
                'fname' => '',
                'mname' => '',
            ),
        );
        $returnValue['fio']['fname'] = $this->getHumanName($this->names,    $human['fname_id'], 'fname');

        if ($returnValue['fio']['fname']) {
            $sex = ($this->names[$human['fname_id']]['sex'] == 'm') ? 'male' : 'female';

            $returnValue['fio']['bname'] = $this->getHumanName($this->surnames, $human['bname_id'], $sex);
            $returnValue['fio']['sname'] = $this->getHumanName($this->surnames, $human['sname_id'], $sex);
            $returnValue['fio']['mname'] = $this->getHumanName($this->names,    $human['mname_id'], $sex .'_mname');
        }

        return $returnValue;
    }

    private function getHumanName($names, $id, $field)
    {
        $returnValue = '';

        if (!empty($names[$id])) {
            $returnValue = $names[$id][$field];
        }

        return $returnValue;
    }

    private function normolizeTree($tree)
    {
        $returnValue = [];

        foreach ($tree as $hid => $node) {

            if (!empty($node['marriage'])) {
                foreach ($node['marriage'] as $mhid => $partner) {

                    unset($node['marriage'][$mhid]['marriage']);

                    if (!empty($partner['children'])) {

                        // $partner['children'] = $this->normolizeTree($partner['children']);
                        unset($node['marriage'][$mhid]['children']);

                        // if (!empty($node['children'])) {

                        //     $node['children'] = array_diff_key($node['children'], $partner['children']);
                        // }
                    }
                }
            }

            if (!empty($node['children'])) {
                // $node['marriage']['anonim']= $this->getHuman(0);
                // $node['marriage']['anonim']['children'] = $this->normolizeTree($node['children']);
                $node['children'] = $this->normolizeTree($node['children']);
            }

            // $node['children']  = null;
            $returnValue[$hid] = $node;
        }

        return $returnValue;
    }
}
