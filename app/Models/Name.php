<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    public function getByIds($ids)
    {
        $retrunValue = $this->select('id', 'sex', 'fname', 'male_mname', 'female_mname')
                            ->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        return $retrunValue;
    }
}
