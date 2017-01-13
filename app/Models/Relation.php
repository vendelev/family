<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    public function getByField($field, $ids)
    {
        $returnValue = $this->select('main_person_id', 'slave_person_id', 'type')->whereIn($field, $ids)->get()->toArray();

        return $returnValue;
    }
}
