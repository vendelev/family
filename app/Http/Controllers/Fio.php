<?php

namespace Family\Http\Controllers;

use Family\Models\Name;
use Family\Models\Surname;

class Fio
{
    private $names    = [];
    private $surnames = [];
    private $humans   = [];

    /**
     * @param $humans
     *
     * @return $this
     */
    public function setHumans($humans)
    {
        $this->humans = $humans;

        return $this;
    }

    /**
     * @return array
     */
    private function getHumans()
    {
        return $this->humans;
    }

    /**
     * @return $this
     */
    public function setSurnames()
    {
        $model = new Surname;
        $this->surnames = $model->setIds($this->getHumans())->getByIds();

        return $this;
    }

    /**
     * @return $this
     */
    public function setNames()
    {
        $model = new Name;
        $this->names = $model->setIds($this->getHumans())->getByIds();

        return $this;
    }

    /**
     * @return array
     */
    private function getSurnames()
    {
        return $this->surnames;
    }

    /**
     * @return array
     */
    private function getNames()
    {
        return $this->names;
    }

    /**
     * @param $humans
     *
     * @return array
     */
    public function setFio($humans)
    {
        foreach ($humans as $id => $human) {
            $humans[$id]['fio'] = $this->getFio($human);
        }

        return $humans;
    }

    /**
     * Получение ФИО персоны.
     *
     * @param  array $human Персона
     * @return array
     */
    private function getFio($human)
    {
        $returnValue = array(
            'bname' => '',
            'sname' => '',
            'fname' => '',
            'mname' => '',
        );

        if ($human) {
            $surnames = $this->getSurnames();
            $names    = $this->getNames();

            $returnValue['fname'] = $this->getName($names, $human['fname_id'], 'fname');

            if ($returnValue['fname']) {
                $sex = ($names[$human['fname_id']]['sex'] == 'm') ? 'male' : 'female';

                $returnValue['bname'] = $this->getName($surnames, $human['bname_id'], $sex);
                $returnValue['sname'] = $this->getName($surnames, $human['sname_id'], $sex);
                $returnValue['mname'] = $this->getName($names,    $human['mname_id'], $sex .'_mname');
            }
        }

        return $returnValue;
    }

    /**
     * Получение Имени/Фамилии персоны.
     *
     * @param  array  $names Список значений
     * @param  int  $id
     * @param  string $field Наименование возвращаемого поля
     * @return string
     */
    private function getName($names, $id, $field)
    {
        $returnValue = '';

        if (!empty($names[$id])) {
            $returnValue = $names[$id][$field];
        }

        return $returnValue;
    }
}
