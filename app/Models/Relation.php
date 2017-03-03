<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель для работы с таблицей родственных отношений.
 */
class Relation extends Model
{
    private $items = [];

    /**
     * Получение списка родственных отношений по списку id.
     *
     * @param  array $ids Список id
     * @return array
     */
    public function getByIds($ids)
    {
        $mrg_relations = $this->getByField('main_person_id', $ids);
        $prt_relations = $this->getByField('slave_person_id', $ids, 'mrg');
        $relations     = array_merge($mrg_relations, $prt_relations);
        $this->items   = array_merge($this->items, $relations);

        return $this->items;
    }

    /**
     * Получение списка родственных отношений по конкретному полю.
     *
     * @param  string $field Наименование поля
     * @param  array  $ids   Список значений для выборки по полю
     * @param  string $type  Тип связи: prt|mrg
     * @return array
     */
    private function getByField($field, $ids, $type='')
    {
        $select = $this->select('main_person_id', 'slave_person_id', 'type')->whereIn($field, $ids);
        if (!empty($type)) {
            $select->where('type', '=', $type);
        }
        $returnValue = $select->get()->toArray();

        return $returnValue;
    }
}
