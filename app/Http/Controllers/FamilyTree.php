<?php

namespace Family\Http\Controllers;

use Illuminate\Http\Request;

use Family\Http\Requests;

use Family\Models\Human;
use Family\Models\Name;
use Family\Models\Surname;
use Family\Models\Relation;

/**
 * Контроллер показ дерева родственных отношений.
 */
class FamilyTree extends Controller
{
    private $relation = null;
    private $human    = null;
    private $names    = array();
    private $surnames = array();
    private $main_humans  = array();
    private $slave_humans = array();

    /**
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        $this->relation = new Relation;
        $this->human    = new Human;
    }

    /**
     * Показ дерева родственных отношений по фамилии.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $surname= new Surname;
        $name   = new Name;
        $sname  = $request->input('surname');

        $this->main_humans = $this->human->getBySurname($sname);
        $this->slave_humans= $this->main_humans;

        $humansIds = array_keys($this->main_humans);
        $relations = $this->getSlaveHumans($humansIds);
        $main_ids  = $this->getNameIds($this->main_humans);
        $slave_ids = $this->getNameIds($this->slave_humans);
        $sname_ids = array_merge($main_ids['sname'], $slave_ids['sname']);
        $name_ids  = array_merge($main_ids['name'], $slave_ids['name']);

        $this->surnames = $surname->getByIds(array_unique($sname_ids));
        $this->names    = $name->getByIds(array_unique($name_ids));

        $tree = $this->getForest($relations);
        $tree = $this->cleanForest($tree);
        $tree = $this->normolizeTree($tree);

        return view('family/forest', ['tree' => $tree]);
    }

    /**
     * Получение списка родственных отношений и родственников.
     *
     * @param  array $humansIds Список id
     * @return array
     */
    private function getSlaveHumans($humansIds)
    {
        $returnValue = array();

        if (!empty($humansIds)) {
            $relations   = $this->getRelationsByIds($humansIds);
            $emptyIds    = $this->getEmptyHumanIds($relations, $this->main_humans);
            $returnValue = array_merge($returnValue, $relations);

            while (!empty($emptyIds)) {

                $humans = $this->human->getByIds($emptyIds);

                foreach ($humans as $id => $item) {
                    $this->slave_humans[$id] = $item;
                }

                $relations   = $this->getRelationsByIds($emptyIds);
                $emptyIds    = $this->getEmptyHumanIds($relations, $this->slave_humans);
                $returnValue = array_merge($returnValue, $relations);
            }
        }

        return $returnValue;
    }

    /**
     * Получение списка родственных отношений.
     *
     * @param  array $ids Список id
     * @return array
     */
    private function getRelationsByIds($ids)
    {
        $mrg_relations = $this->relation->getByField('main_person_id', $ids);
        $prt_relations = $this->relation->getByField('slave_person_id', $ids, 'mrg');
        $returnValue   = array_merge($mrg_relations, $prt_relations);

        return $returnValue;
    }

    /**
     * Получение списка незаполненных персон.
     *
     * @param  array $relations Список родственных отношений
     * @param  array $humans Список персон
     * @return array
     */
    private function getEmptyHumanIds($relations, &$humans)
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

    /**
     * Получение списка id имен и фамилий.
     *
     * @param  array $humans Список персон
     * @return array
     */
    private function getNameIds($humans)
    {
        $sname = array();
        $name  = array();

        foreach ($humans as $item) {
            
            $sname[]= $item['sname_id'];
            $sname[]= $item['bname_id'];
            $name[] = $item['fname_id'];
            $name[] = $item['mname_id'];
        }

        $returnValue = array(
            'sname' => array_unique($sname),
            'name'  => array_unique($name),
        );

        return $returnValue;
    }

    /**
     * Получение дерева персон.
     *
     * @param  array $relations Список родственных отношений
     * @return array
     */
    private function getForest($relations)
    {
        $returnValue = array();
        foreach ($relations as $item) {
            $mpi = $item['main_person_id'];
            $spi = $item['slave_person_id'];

            if (empty($returnValue[$mpi])) {
                $returnValue[$mpi] = $this->getHuman($mpi);
            }
            if (empty($returnValue[$spi])) {
                $returnValue[$spi] = $this->getHuman($spi);
            }

            switch ($item['type']) {
                case 'prt':
                    $returnValue[$mpi]['children'][$spi] = &$returnValue[$spi];
                    break;
                case 'mrg':
                    $returnValue[$mpi]['marriage'][$spi] = &$returnValue[$spi];
                    $returnValue[$spi]['marriage'][$mpi] = &$returnValue[$mpi];
                    break;
            }
        }

        return $returnValue;
    }

    /**
     * Получение персоны с заполненным ФИО.
     *
     * @param  array $relations Список родственных отношений
     * @return array
     */
    private function getHuman($id)
    {
        if (!empty($this->main_humans[$id])) {
            $returnValue = $this->getHumanFio($this->main_humans[$id]);
        } elseif (!empty($this->slave_humans[$id])) {
            $returnValue = $this->getHumanFio($this->slave_humans[$id]);
        } else {
            $returnValue = $this->getHumanFio(false);
        }
        $returnValue['marriage'] = array();
        $returnValue['children'] = array();

        return $returnValue;
    }

    /**
     * Получение ФИО персоны.
     *
     * @param  array|false $human Персона
     * @return array
     */
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

        if ($human) {
            $returnValue['fio']['fname'] = $this->getHumanName($this->names, $human['fname_id'], 'fname');

            if ($returnValue['fio']['fname']) {
                $sex = ($this->names[$human['fname_id']]['sex'] == 'm') ? 'male' : 'female';

                $returnValue['fio']['bname'] = $this->getHumanName($this->surnames, $human['bname_id'], $sex);
                $returnValue['fio']['sname'] = $this->getHumanName($this->surnames, $human['sname_id'], $sex);
                $returnValue['fio']['mname'] = $this->getHumanName($this->names,    $human['mname_id'], $sex .'_mname');
            }
        }

        return $returnValue;
    }

    /**
     * Получение Имени/Фамилии персоны.
     *
     * @param  array $names Список значений
     * @param  array $id    
     * @param  array $field Наименование возвращаемого поля
     * @return array
     */
    private function getHumanName($names, $id, $field)
    {
        $returnValue = '';

        if (!empty($names[$id])) {
            $returnValue = $names[$id][$field];
        }

        return $returnValue;
    }

    /**
     * Удаление пустых массивов и дублирующих веток дерева персон.
     *
     * @param  array $returnValue Дерево родственных отношений
     * @return array Дерево родственных отношений
     */
    private function cleanForest($returnValue)
    {
        $toRemove = array();
        foreach ($returnValue as $id => $item) {
            if (!empty($item['children'])) {
                foreach ($item['children'] as $key => $value) {
                    if (!empty($returnValue[$key])) {
                        $toRemove[]= $key;
                    }
                }
            } else {
                unset($returnValue[$id]['children']);
            }

            if (!empty($item['marriage'])) {
                foreach ($item['marriage'] as $key => $value) {
                    if (!empty($returnValue[$key])) {
                        if (empty($this->main_humans[$key])) {
                            $toRemove[]= $key;
                        }
                    }
                }
            } else {
                unset($returnValue[$id]['marriage']);
            }
        }


        foreach ($toRemove as $key) {
            unset($returnValue[$key]);
        }

        return $returnValue;
    }

    /**
     * Удаление дублирующих записей marriage и children в дереве.
     *
     * @param  array $returnValue Дерево родственных отношений
     * @return array Дерево родственных отношений
     */
    private function normolizeTree($tree)
    {
        $returnValue = [];

        foreach ($tree as $hid => $node) {

            if (!empty($node['marriage'])) {
                foreach ($node['marriage'] as $mhid => $partner) {

                    unset($node['marriage'][$mhid]['marriage']);

                    if (!empty($partner['children'])) {

                        unset($node['marriage'][$mhid]['children']);
                    }
                }
            }

            if (!empty($node['children'])) {
                $node['children'] = $this->normolizeTree($node['children']);
            }

            $returnValue[$hid] = $node;
        }

        return $returnValue;
    }
}
