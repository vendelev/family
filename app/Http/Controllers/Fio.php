<?php

namespace Family\Http\Controllers;

use Family\Models\Name;
use Family\Models\Surname;

class Fio
{
    private $names    = [];
    private $surnames = [];

    /**
     * @param array $humans
     *
     * @return Fio
     */
    public function selectSurnames($humans)
    {
        $model = new Surname;
        $this->surnames = $model->setIds($humans)->getByIds();

        return $this;
    }

    /**
     * @param array $humans
     *
     * @return Fio
     */
    public function selectNames($humans)
    {
        $model = new Name;
        $this->names = $model->setIds($humans)->getByIds();

        return $this;
    }

    /**
     * @return array
     */
    public function getSurnames()
    {
        return $this->surnames;
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @param array $humans
     *
     * @return array
     */
    public function fillFio($humans)
    {
        $this->selectNames($humans)
             ->selectSurnames($humans);

        $surnames = $this->getSurnames();
        $names    = $this->getNames();

        foreach ($humans as $id => $human) {
            $humans[$id]['fio'] = $this->getFio($human, $surnames, $names);

        }

        return $humans;
    }

    /**
     * Получение ФИО персоны.
     *
     * @param  array $human Персона
     * @param  array $surnames
     * @param  array $names
     *
     * @return array
     */
    public function getFio($human, $surnames, $names)
    {
        $returnValue = array(
            'bname' => '',
            'sname' => '',
            'fname' => $this->getName($names, $human['fname_id'], 'fname'),
            'mname' => '',
        );

        if ($returnValue['fname']) {
            $sex = ($names[$human['fname_id']]['sex'] == 'm') ? 'male' : 'female';

            $returnValue['bname'] = $this->getName($surnames, $human['bname_id'], $sex);
            $returnValue['sname'] = $this->getName($surnames, $human['sname_id'], $sex);
            $returnValue['mname'] = $this->getName($names,    $human['mname_id'], $sex .'_mname');
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
