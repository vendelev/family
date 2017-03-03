<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

use Family\Models\Relation;

/**
 * Модель для работы таблицей персон.
 */
class Human extends Model
{
    private $humans  = array();

    /**
     * @param array $humans
     * @param bool $isMain
     *
     * @return Human
     */
    public function setHumans($humans, $isMain=false)
    {
        foreach ($humans as $id=>$human) {
            $this->humans[$id] = $human;
            $this->humans[$id]['isMain'] = $isMain;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getHumans()
    {
        return $this->humans;
    }

    /**
     * Получение списка родственных отношений и родственников.
     *
     * @return array
     */
    public function getRelations()
    {
        $returnValue = array();
        $relation    = new Relation;
        $humans      = $this->getHumans();

        if (!empty($humans)) {
            $returnValue = $relation->getByIds(array_keys($humans));
            $emptyIds    = $this->getEmptyIds($returnValue);

            while (!empty($emptyIds)) {

                $humans = $this->getByIds($emptyIds);
                $this->setHumans($humans);

                $returnValue = $relation->getByIds(array_keys($humans));
                $emptyIds    = $this->getEmptyIds($returnValue);
            }
        }

        return $returnValue;
    }

    /**
     * Получение списка незаполненных персон.
     *
     * @param  array $relations Список родственных отношений
     * @return array
     */
    public function getEmptyIds($relations)
    {
        $returnValue = array();
        $humans = $this->getHumans();

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
     * Получение списка людей входящих в одно дерево по фамилии.
     *
     * @param string $surname
     *
     * @return array
     */
    public function getBySurname($surname)
    {

// \DB::listen(function($sql) {
//     $query = $sql->sql;
//     $bindings = $sql->bindings;
//     $query = str_replace(array('%', '?'), array('%%', '\'%s\''), $query);
//     $query = vsprintf($query, $bindings);
//     var_dump($query);
//     var_dump($sql->time);
// });

// ->join('human_trees',
//     function ($join) {
//         $join->on('human_trees.human_id', '=', 'humans.id')
//              ->on('human_trees.family', '=', \DB::raw('CONCAT(`surnames`.`id`, "")'));
//     })

        $returnValue = $this->select(
                                'humans.id',
                                'humans.fname_id',
                                'humans.mname_id',
                                'humans.sname_id',
                                'humans.bname_id')
                            ->join('surnames', 'humans.bname_id', '=', 'surnames.id')
                            ->orWhere('surnames.female', '=', $surname)
                            ->orWhere('surnames.male', '=', $surname)
                            ->get()
                            ->keyBy('id')
                            ->toArray();

        return $returnValue;
    }

    /**
     * Получение списка персон.
     *
     * @param  array $ids Список id
     * @return array
     */
    public function getByIds($ids)
    {
        $retrunValue = $this->select('id', 'fname_id', 'mname_id', 'sname_id', 'bname_id')
                            ->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        return $retrunValue;
    }
}
