<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель для работы таблицей имен.
 */
class Name extends Model
{
    /**
     * @var array Список id имен
     */
    private $ids = [];

    /**
     * Формирование списка id имен.
     *
     * @param  array $humans Список персон
     * @return Name
     */
    public function setIds($humans)
    {
        foreach ($humans as $item) {

            if (!in_array($item['fname_id'], $this->ids)) {
                $this->ids[]= $item['fname_id'];
            }
            if (!in_array($item['mname_id'], $this->ids)) {
                $this->ids[]= $item['mname_id'];
            }
        }

        return $this;
    }

    /**
     * Получение списка id имен.
     *
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * Получение списка имен.
     *
     * @return array
     */
    public function getByIds()
    {
        $ids   = $this->getIds();
        $items = $this->select('id', 'sex', 'fname', 'male_mname', 'female_mname')
                      ->whereIn('id', $ids)
                      ->get()
                      ->keyBy('id')
                      ->toArray();

        return $items;
    }
}
