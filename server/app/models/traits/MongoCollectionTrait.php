<?php

namespace Imee\Models\Traits;

trait MongoCollectionTrait
{
    /**
     * 获取条数
     * @param null $parameters
     * @return int
     */
    public static function aggCount($parameters = null)
    {
        $aggregate = static::parseParameters($parameters);

        if (!isset($parameters['count'])) {
            $aggregate['count'] = [
                '$count' => 'count'
            ];
        }

        $agg = static::aggregate(array_values($aggregate));
        $agg = current(static::formatList($agg));

        return (int)($agg['count'] ?? 0);
    }

    public static function aggFind($parameters = null)
    {
        $aggregate = static::parseParameters($parameters);

        $agg = static::aggregate(array_values($aggregate));

        return static::formatList($agg);
    }

    public static function aggFindFirst($parameters = null)
    {
        $parameters['limit'] = 1;

        $aggregate = static::parseParameters($parameters);

        $agg = static::aggregate(array_values($aggregate));

        return current(static::formatList($agg));
    }

    public static function parseParameters($conditions = null)
    {
        $aggregate = $fields = $join = $unwind = $order = $offset = $limit = [];

        if (is_null($conditions) || empty($conditions)) {
            $conditions['offset'] = 0;
        }

        if (isset($conditions['fields'])) {
            $fields = array_merge($aggregate, static::parseFields($conditions['fields']));
            unset($conditions['fields']);
        }

        if (isset($conditions['join'])) {
            $join = array_merge($aggregate, static::parseJoin($conditions['join']));
            unset($conditions['join']);
        }

        if (isset($conditions['unwind'])) {
            $unwind = array_merge($aggregate, static::parseUnwind($conditions['unwind']));
            unset($conditions['unwind']);
        }

        if (isset($conditions['order'])) {
            $order = array_merge($aggregate, static::parseOrder($conditions['order']));
            unset($conditions['order']);
        }

        if (isset($conditions['offset'])) {
            $offset = array_merge($aggregate, static::parseOffset($conditions['offset']));
            unset($conditions['offset']);
        }

        if (isset($conditions['limit'])) {
            $limit = array_merge($aggregate, static::parseLimit($conditions['limit']));
            unset($conditions['limit']);
        }

        // todo group by 待实现
        if (isset($conditions['group'])) {
            unset($conditions['group']);
        }

        if (isset($conditions['conditions'])) {
            $where = $conditions['conditions'];
        } else {
            $where = $conditions;
        }

        if (!empty($where)) {
            // 不能调整顺序!!!
            $aggregate = array_merge($fields, $join, $unwind, static::parseConditions($where, $aggregate), $order, $offset, $limit);
        } else {
            $aggregate = array_merge($fields, $join, $unwind, $order, $offset, $limit);
        }

        return $aggregate;
    }

    public static function parseJoin($joins)
    {
        $aggregate = [];

        foreach ($joins as $key => $condition) {
            [$table, $fields, $fieldAs] = $condition;
            [$leftPk, $rightPk] = $fields;

            $aggregate[$table] = [
                '$lookup' => [
                    'from'         => $table,
                    'localField'   => $leftPk,
                    'foreignField' => $rightPk,
                    'as'           => $fieldAs
                ]
            ];
        }

        return $aggregate;
    }

    public static function parseUnwind($unwinds)
    {
        $aggregate = [];

        foreach ($unwinds as $unwind) {
            $aggregate['unwind_' . $unwind] = ['$unwind' => ['path' => '$' . $unwind, 'preserveNullAndEmptyArrays' => true]];
        }

        return $aggregate;
    }

    public static function parseLimit($limit)
    {
        return ['limit' => ['$limit' => (int)$limit]];
    }

    public static function parseOffset($offset)
    {
        return ['offset' => ['$skip' => (int)$offset]];
    }

    public static function parseOrder($order)
    {
        $aggOrder = [];

        $orderMap = [
            'asc'  => 1,
            'desc' => -1,
        ];

        foreach ($order as $field => $desc) {
            $aggOrder[$field] = $orderMap[strtolower($desc)];
        }

        return ['order' => ['$sort' => $aggOrder]];
    }

    public static function parseConditions($condition, $aggregate)
    {
        $master = $join = [];

        foreach ($condition as $field => $item) {
            if (!is_array($item)) {
                if (false === strpos($field, '.')) {
                    $master[$field] = $item;
                } else {
                    $join[$field] = $item;
                }
                continue;
            }

            // 处理模糊搜索
            if (isset($item['like'])) {
                if (false === strpos($field, '.')) {
                    $master[$field] = ['$regex' => $item['like']];
                } else {
                    $join[$field] = ['$regex' => $item['like']];
                }
                $condition[$field] = ['$regex' => $item['like']];
            }

            if (false === strpos($field, '.')) {
                $master[$field] = $item;
            } else {
                $join[$field] = $item;
            }
        }

        if (!empty($master)) {
            $aggregate = array_merge(['master_conditions' => ['$match' => $master]], $aggregate);
        }

        if (!empty($join)) {
            $aggregate = array_merge($aggregate, ['join_conditions' => ['$match' => $join]]);
        }

        return $aggregate;
    }

    public static function parseFields($fields)
    {
        return ['fields' => ['$project' => $fields]];
    }

    /**
     * 格式化数据
     * @param $items
     * @return array
     */
    protected static function formatList($items)
    {
        $list = [];

        if (empty($items)) {
            return [];
        }

        foreach ($items as $item) {
            $tmp = json_decode(json_encode($item), true);
            $list[] = $tmp;
        }

        return $list;
    }
}
