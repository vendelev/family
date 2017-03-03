<?php

namespace Family\Http\Controllers;

class Forest
{
    /**
     * Получение дерева персон.
     *
     * @param  array $relations Список родственных отношений
     * @param  array $humans    Список основных персон
     *
     * @return array
     */
    public function get($relations, $humans)
    {
        $returnValue = $this->create($relations, $humans);
        $returnValue = $this->clean($returnValue, $humans);
        $returnValue = $this->normolize($returnValue);

        return $returnValue;
    }

    /**
     * Получение дерева персон.
     *
     * @param  array $relations Список родственных отношений
     * @param  array $humans    Список основных персон
     *
     * @return array
     */
    private function create($relations, $humans)
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
    private function clean($returnValue, $humans)
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
                        if (empty($humans[$key]['isMain'])) {
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
    private function normolize($tree)
    {
        $returnValue = [];

        foreach ($tree as $hid => $node) {
            $hasPartnerChild = false;

            if (!empty($node['marriage'])) {
                foreach ($node['marriage'] as $mhid => $partner) {

                    unset($node['marriage'][$mhid]['marriage']);

                    if (!empty($partner['children'])) {

                        $hasPartnerChild = true;
                        $node['marriage'][$mhid]['children'] = $this->normolize($partner['children']);
                    }
                }
            }

            if (!empty($node['children'])) {

                if ($hasPartnerChild) {

                    unset($node['children']);

                } else {

                    $node['children'] = $this->normolize($node['children']);
                }
            }

            $returnValue[$hid] = $node;
        }

        return $returnValue;
    }
}
