<?php

namespace Family\Http\Controllers;

use Illuminate\Http\Request;

use Family\Http\Requests;

use Family\Models\Human;
use Family\Models\Name;
use Family\Models\Surname;

/**
 * Контроллер показ дерева родственных отношений.
 */
class FamilyTree extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Показ дерева родственных отношений по фамилии.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $sname  = $request->input('surname');
        $human  = new Human;
        $surname= new Surname;
        $name   = new Name;

        $human->getBySurname($sname);
        $relations    = $human->getRelations();
        $slave_humans = $human->getSlave();

        $names   = $name->setIds($slave_humans)->getByIds();
        $surnames= $surname->setIds($slave_humans)->getByIds();

        $human->setFio($surnames, $names);
        $main_humans  = $human->getMain();
        $slave_humans = $human->getSlave();

        $tree = $this->getForest($relations, $main_humans, $slave_humans);
        $tree = $this->cleanForest($tree, $main_humans);
        $tree = $this->normolizeTree($tree);

        return view('family/forest', ['tree' => $tree]);
    }

    /**
     * Получение дерева персон.
     *
     * @param  array $relations     Список родственных отношений
     * @param  array $main_humans   Список основных персон
     * @param  array $slave_humans  Список зависымых перосон
     *
     * @return array
     */
    private function getForest($relations, $main_humans, $slave_humans)
    {
        $returnValue = array();
        foreach ($relations as $item) {
            $mpi = $item['main_person_id'];
            $spi = $item['slave_person_id'];

            if (empty($returnValue[$mpi])) {
                $returnValue[$mpi] = $this->getHuman($mpi, $main_humans, $slave_humans);
            }
            if (empty($returnValue[$spi])) {
                $returnValue[$spi] = $this->getHuman($spi, $main_humans, $slave_humans);
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
     * @param  int   $id            Id персоны
     * @param  array $main_humans   Список основных персон
     * @param  array $slave_humans  Список зависымых перосон
     *
     * @return array
     */
    private function getHuman($id, $main_humans, $slave_humans)
    {
        if (!empty($main_humans[$id])) {
            $returnValue = $main_humans[$id];
        } elseif (!empty($slave_humans[$id])) {
            $returnValue = $slave_humans[$id];
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
        $returnValue['marriage'] = array();
        $returnValue['children'] = array();

        return $returnValue;
    }

    /**
     * Удаление пустых массивов и дублирующих веток дерева персон.
     *
     * @param  array $returnValue Дерево родственных отношений
     * @param  array $main_humans   Список основных персон
     *
     * @return array Дерево родственных отношений
     */
    private function cleanForest($returnValue, $main_humans)
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
                foreach ($item['marriage'] as $key => $partner) {
                    
                    if (!empty($returnValue[$key])) {
                        if (empty($main_humans[$key])) {
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
     * @param  array $tree  Дерево родственных отношений
     * @return array        Дерево родственных отношений
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
