<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    public function getByField($field, $ids, $type='')
    {
        $select = $this->select('main_person_id', 'slave_person_id', 'type')->whereIn($field, $ids);
        if (!empty($type)) {
            $select->where('type', '=', $type);
        }
        $returnValue = $select->get()->toArray();

        return $returnValue;
    }
}
