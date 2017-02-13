<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

class Human extends Model
{

    /**
     * Получение списка людей входящих в одно дерево по фамилии.
     *
     * @param string $surname
     *
     * @return Illuminate\Support\Collection
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

        $retrunValue = $this->select(
                                'humans.id',
                                'humans.fname_id',
                                'humans.mname_id',
                                'humans.sname_id',
                                'humans.bname_id')
                            ->join('surnames',
                                function ($join) {
                                    // $join->on('humans.sname_id', '=', \DB::raw('CONCAT(`surnames`.`id`, "")'))
                                         // ->orOn('humans.bname_id', '=', \DB::raw('CONCAT(`surnames`.`id`, "")'));
                                    $join->on('humans.bname_id', '=', \DB::raw('CONCAT(`surnames`.`id`, "")'));
                                })
                            // ->join('human_trees', 
                            //     function ($join) {
                            //         $join->on('human_trees.human_id', '=', 'humans.id')
                            //              ->on('human_trees.family', '=', \DB::raw('CONCAT(`surnames`.`id`, "")'));
                            //     })
                            ->orWhere('surnames.female', '=', $surname)
                            ->orWhere('surnames.male', '=', $surname)
                            ->get()
                            ->keyBy('id')
                            ->toArray();
 
        return $retrunValue;
    }

    public function getByIds($ids)
    {
        $retrunValue = $this->select('id', 'fname_id', 'mname_id', 'sname_id', 'bname_id')
                             ->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        return $retrunValue;
    }
}
