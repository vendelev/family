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

\DB::listen(function($sql) {
    $query = $sql->sql;
    $bindings = $sql->bindings;
    $query = str_replace(array('%', '?'), array('%%', '\'%s\''), $query);
    $query = vsprintf($query, $bindings);
    var_dump($query);
    var_dump($sql->time);
});

    	$retrunValue = $this->select(
    							'humans.id',
    							'humans.fname_id',
    							'humans.mname_id',
    							'humans.sname_id',
    							'humans.bname_id')
				            ->join('surnames',
				            	'humans.sname_id', '=', 'surnames.id')
				            ->join('human_trees', 
				                function ($join) {
				                    $join->on('human_trees.human_id', '=', 'humans.id');
				                    $join->on('human_trees.family', '=', 'surnames.id');
				                })
				            ->orWhere('surnames.female', '=', $surname)
				            ->orWhere('surnames.male', '=', $surname)
				            ->get();
 
    	return $retrunValue;
    }

}
