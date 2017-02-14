<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

class Surname extends Model
{
    /**
     * Получение списка фамилий.
     *
     * @param  array $ids Список id
     * @return array
     */
    public function getByIds($ids)
    {
        $retrunValue = $this->select('id', 'male', 'female')
                            ->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        return $retrunValue;
    }
}
