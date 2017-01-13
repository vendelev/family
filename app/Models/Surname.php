<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

class Surname extends Model
{
    public function getByIds($ids)
    {
        $retrunValue = $this->select('id', 'male', 'female')
                            ->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        return $retrunValue;
    }
}
