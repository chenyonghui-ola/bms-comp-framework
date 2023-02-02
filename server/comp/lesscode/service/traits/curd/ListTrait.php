<?php

namespace Imee\Service\Lesscode\Traits\Curd;

trait ListTrait
{
    /**
     * todo 重写获取筛选条件
     * @param $filter
     */
    abstract public function onGetFilter(&$filter);

    /**
     * todo 链表查询
     * @param $filter
     * @return array
     */
    public function onJoin($filter): array
    {
        return [];
    }

    /**
     * todo 重写格式化列表数据
     * @param $item
     */
    abstract public function onListFormat(&$item);

    /**
     * todo 特殊处理最后输出的列表数据
     * @param $list
     * @return array
     */
    abstract public function onAfterList($list): array;

    /**
     * 处理用户排序-连表查询
     * @param $orderBy
     */
    public function onOrderBy(&$orderBy): void
    {

    }

    public function onSetParams($params): void
    {

    }

    /**
     * 获取查询字段 配合join使用
     * @return string
     */
    public function onGetColumns(): string
    {
        return '*';
    }
}