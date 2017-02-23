<?php

namespace Family\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель для работы таблицей фамилий.
 */
class Surname extends Model
{    /**
     * @var array Список id фамилий
     */
    private $ids = [];

    /**
     * Формирование списка id фамилий.
     *
     * @param  array $humans Список персон
     * @return Surname
     */
    public function setIds($humans)
    {
        foreach ($humans as $item) {

            if (!in_array($item['sname_id'], $this->ids)) {
                $this->ids[]= $item['sname_id'];
            }
            if (!in_array($item['bname_id'], $this->ids)) {
                $this->ids[]= $item['bname_id'];
            }
        }

        return $this;
    }

    /**
     * Получение списка id фамилий.
     *
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * Получение списка фамилий.
     *
     * @return array
     */
    public function getByIds()
    {
        $ids   = $this->getIds();
        $items = $this->select('id', 'male', 'female')
                      ->whereIn('id', $ids)
                      ->get()
                      ->keyBy('id')
                      ->toArray();

        return $items;
    }
}
