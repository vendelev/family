<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель для работы таблицей родственных отношений.
 */
class Relation extends Model
{
    /**
     * Получение списка родственных отношений по конкретному полю.
     *
     * @param  string $field Наименование поля
     * @param  array  $ids   Список значений для выборки по полю
     * @param  string $type  Тип связи: prt|mrg
     * @return array
     */
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
