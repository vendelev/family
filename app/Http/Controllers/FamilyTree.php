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
    private $name     = null;
    private $names    = array();

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $human    = new Human;
        $relation = new Relation;

        $sname        = $request->input('surname');
        $this->humans = $human->getBySurname($sname);
        $humansIds    = array_keys($this->humans);
        $relations    = $relation->select('main_person_id', 'slave_person_id', 'type')->whereIn('main_person_id', $humansIds)->get()->toArray();
        $emptyIds     = $this->getEmptyHumans($relations);
        $humans       = $human->select('id', 'fname_id', 'mname_id', 'sname_id', 'bname_id')->whereIn('id', $emptyIds)->get()
                            ->keyBy('id')->toArray();

        foreach ($humans as $id => $item) {
            $this->humans[$id] = $item;
        }

        $ids          = $this->getNameIds($this->humans);
        $this->getSurnames($ids['sname']);
        $this->getNames($ids['name']);

        $tree = $this->getForest($relations);
        $tree = $this->normolizeTree($tree);

        return view('family/forest', ['tree' => $tree]);
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

    private function getSurnames($ids)
    {
        $surname = new Surname;
        $this->surnames = $surname->select('id', 'male', 'female')
                            ->whereIn('id', $ids)
                            ->get()
                            ->keyBy('id')
                            ->toArray();
    }

    private function getNames($ids)
    {
        $name = new Name;
        $this->names = $name->select('id', 'sex', 'fname', 'male_mname', 'female_mname')
                      ->whereIn('id', $ids)
                      ->get()
                      ->keyBy('id')
                      ->toArray();
    }

    private function getEmptyHumans($relations)
    {
        $returnValue = array();

        foreach ($relations as $item) {

            $mpi = $item['main_person_id'];
            $spi = $item['slave_person_id'];

            if (empty($this->humans[$mpi])) {
                $returnValue[] = $mpi;
            }

            if (empty($this->humans[$spi])) {
                $returnValue[] = $spi;
            }
        }

        return $returnValue;
    }

    private function getForest($relations)
    {
        $returnValue = array();
        foreach ($relations as $item) {
            $mpi = $item['main_person_id'];
            $spi = $item['slave_person_id'];

            if (empty($returnValue[$mpi])) {
                $returnValue[$mpi] = $this->getHuman($mpi);
            }

            switch ($item['type']) {
                case 'prt':
                    if (empty($returnValue[$mpi]['children'])) {
                        $returnValue[$mpi]['children'] = array();
                    }
                    $returnValue[$mpi]['children'][$spi] = $this->getHuman($spi);
                    break;
                case 'mrg':
                    if (empty($returnValue[$mpi]['marriage'])) {
                        $returnValue[$mpi]['marriage'] = array();
                    }
                    $returnValue[$mpi]['marriage'][$spi] = $this->getHuman($spi);
                    break;
            }
        }

        //Переделать утечки памяти из-за ссылок
        $toRemove = array();
        foreach ($returnValue as $id => $item) {
            if (!empty($item['children'])) {
                foreach ($item['children'] as $key => $value) {
                    if (!empty($returnValue[$key])) {
                        $returnValue[$id]['children'][$key] = &$returnValue[$key];
                        $toRemove[]= $key;
                    }
                }
            }
            if (!empty($item['marriage'])) {
                foreach ($item['marriage'] as $key => $value) {
                    if (!empty($returnValue[$key])) {
                        $returnValue[$id]['marriage'][$key] = &$returnValue[$key];
                        $toRemove[]= $key;
                    }
                }
            }
        }

        foreach ($toRemove as $key) {
            unset($returnValue[$key]);
        }

        return $returnValue;
    }

    private function getHuman($id)
    {
        $returnValue = array(
            'fio' => array(
                'bname' => '',
                'sname' => '',
                'fname' => '',
                'mname' => '',
            ),
        );

        if (!empty($this->humans[$id])) {

            $human = $this->humans[$id];

            $returnValue['fio']['fname'] = $this->getHumanName($this->names,    $human['fname_id'], 'fname');

            if ($returnValue['fio']['fname']) {
                $sex = ($this->names[$human['fname_id']]['sex'] == 'm') ? 'male' : 'female';

                $returnValue['fio']['bname'] = $this->getHumanName($this->surnames, $human['bname_id'], $sex);
                $returnValue['fio']['sname'] = $this->getHumanName($this->surnames, $human['sname_id'], $sex);
                $returnValue['fio']['mname'] = $this->getHumanName($this->names,    $human['mname_id'], $sex .'_mname');
            }
        } else {
            // var_dump($id);
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
                foreach ($node['marriage'] as $mhid => &$partner) {

                    if (!empty($partner['children'])) {

                        $partner['children'] = $this->normolizeTree($partner['children']);

                        if (!empty($node['children'])) {

                            $node['children'] = array_diff_key($node['children'], $partner['children']);
                        }
                    }
                }
            }

            if (!empty($node['children'])) {
                $node['marriage']['anonim']= $this->getHuman(0);
                $node['marriage']['anonim']['children'] = $this->normolizeTree($node['children']);
            }

            $node['children']  = null;
            $returnValue[$hid] = $node;
        }

        return $returnValue;
    }
}
