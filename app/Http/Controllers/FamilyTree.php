<?php

namespace Family\Http\Controllers;

use Illuminate\Http\Request;

use Family\Models\Human;
use Family\Http\Controllers\Fio;

/**
 * Контроллер показ дерева родственных отношений.
 */
class FamilyTree extends Controller
{
    /**
     *
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
        $human  = new Human();
        $fio    = new Fio();

        $humans  = $human->getBySurname($sname);
        $human->setHumans($humans, true);

        $relations = $human->getRelations();
        $humans    = $human->getHumans();
        $humans    = $fio->fillFio($humans);

        $tree = $this->getForest($relations, $humans);
        $tree = $this->cleanForest($tree, $humans);
        $tree = $this->normolizeTree($tree);

        return view('family/forest', ['tree' => $tree]);
    }

    /**
     * Получение дерева персон.
     *
     * @param  array $relations Список родственных отношений
     * @param  array $humans    Список основных персон
     *
     * @return array
     */
    private function getForest($relations, $humans)
    {
        $returnValue = array();
        foreach ($relations as $item) {
            $mpi = $item['main_person_id'];
            $spi = $item['slave_person_id'];

            if (empty($returnValue[$mpi])) {
                $returnValue[$mpi] = $this->getHuman($mpi, $humans);
            }
            if (empty($returnValue[$spi])) {
                $returnValue[$spi] = $this->getHuman($spi, $humans);
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
     * @param  int   $id       Id персоны
     * @param  array $humans   Список основных персон
     *
     * @return array
     */
    private function getHuman($id, $humans)
    {
        if (!empty($humans[$id])) {
            $returnValue = $humans[$id];
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
     * @param  array $humans   Список основных персон
     *
     * @return array Дерево родственных отношений
     */
    private function cleanForest($returnValue, $humans)
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
                        if (!$humans[$key]['isMain']) {
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
