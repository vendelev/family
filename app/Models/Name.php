<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    /**
     * Получение списка имен.
     *
     * @param  array $ids Список id
     * @return array
     */
    public function getByIds($ids)
    {
        $retrunValue = $this->select('id', 'sex', 'fname', 'male_mname', 'female_mname')
                            ->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        return $retrunValue;
    }
}
