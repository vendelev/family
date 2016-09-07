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
        $name     = new Name;
        $surname  = new Surname;
        $relation = new Relation;
        $human    = new Human;

        $sname    = $request->input('surname');

        $human_ids= array();
        $sname_ids= array();
        $name_ids = array();
        $human_id = array();

        $this->humans   = $human->getBySurname($sname);

        foreach ($this->humans as $item) {
            
            if (!in_array($item['id'], $human_ids)) {
                $human_ids[]= $item['id'];
            }
            
            if (!in_array($item['sname_id'], $sname_ids)) {
                $sname_ids[]= $item['sname_id'];
            }
            
            if (!in_array($item['bname_id'], $sname_ids)) {
                $sname_ids[]= $item['bname_id'];
            }
            
            if (!in_array($item['fname_id'], $name_ids)) {
                $name_ids[]= $item['fname_id'];
            }
            
            if (!in_array($item['mname_id'], $name_ids)) {
                $name_ids[]= $item['mname_id'];
            }
        }

        $relations= $relation->select('main_person_id', 'slave_person_id', 'type')->whereIn('main_person_id', $human_ids)->get()->toArray();

        $this->surnames = $surname->select('id', 'male', 'female')->whereIn('id', $sname_ids)->get()->keyBy('id')->toArray();
        $this->names    = $name->select('id', 'sex', 'fname', 'male_mname', 'female_mname')->whereIn('id', $name_ids)->get()->keyBy('id')->toArray();


        $newTree = array();
        foreach ($relations as $item) {
            $mpi = $item['main_person_id'];
            $spi = $item['slave_person_id'];

            if (empty($newTree[$mpi])) {
                $newTree[$mpi] = $this->getHuman($mpi);
            }

            switch ($item['type']) {
                case 'prt':
                    if (empty($newTree[$mpi]['children'])) {
                        $newTree[$mpi]['children'] = array();
                    }
                    $newTree[$mpi]['children'][$spi] = $this->getHuman($spi);
                    break;
                case 'mrg':
                    if (empty($newTree[$mpi]['marriage'])) {
                        $newTree[$mpi]['marriage'] = array();
                    }
                    $newTree[$mpi]['marriage'][$spi] = $this->getHuman($spi);
                    break;
            }
        }

        //Переделать утечки памяти из-за ссылок
        $toRemove = array();
        foreach ($newTree as $id => $item) {
            if (!empty($item['children'])) {
                foreach ($item['children'] as $key => $value) {
                    if (!empty($newTree[$key])) {
                        $newTree[$id]['children'][$key] = &$newTree[$key];
                        $toRemove[]= $key;
                    }
                }
            }
            if (!empty($item['marriage'])) {
                foreach ($item['marriage'] as $key => $value) {
                    if (!empty($newTree[$key])) {
                        $newTree[$id]['marriage'][$key] = &$newTree[$key];
                        $toRemove[]= $key;
                    }
                }
            }
        }

        foreach ($toRemove as $key) {
            unset($newTree[$key]);
        }

        var_dump($newTree);


        // exit;
        // return view('welcome');
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

            if (!empty($this->names[$human['fname_id']])) {
                $sex = ($this->names[$human['fname_id']]['sex'] == 'm') ? 'male' : 'female';
                $returnValue['fio']['fname'] = $this->names[$human['fname_id']]['fname'];

                if (!empty($this->surnames[$human['bname_id']])) {
                    $returnValue['fio']['bname'] = $this->surnames[$human['bname_id']][$sex];
                }

                if (!empty($this->surnames[$human['sname_id']])) {
                    $returnValue['fio']['sname'] = $this->surnames[$human['sname_id']][$sex];
                }

                if (!empty($this->names[$human['mname_id']])) {
                    $returnValue['fio']['mname'] = $this->names[$human['mname_id']][$sex .'_mname'];
                }
            }
        }

        return $returnValue;
    }
}
