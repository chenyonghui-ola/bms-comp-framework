<?php


namespace Imee\Service\Lesscode\Logic\Curd\Mongo;

use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\FilterService;
use Imee\Service\Lesscode\Logic\Curd\BaseLogic;

use Imee\Service\Lesscode\Schema\Mongo\FieldService as MongoFieldService;

class ListLogic extends BaseLogic
{
    protected $opType = AdapterSchema::POINT_LIST;
    protected $drive  = AdapterSchema::DRIVE_MONGO;

    public function handle()
    {
        parent::handle();

        $this->filterService = new FilterService($this->model, $this->schema);
        $this->fieldService  = new MongoFieldService($this->model, $this->schema);

        $this->hookService->onSetParams($this->params);

        $filter = $this->filterService
            ->setParams($this->params)
            ->setDrive($this->drive)
            ->getFilter();

        // 实现自己特殊的转化
        $this->hookService->onGetFilter($filter);
        $join = $this->hookService->onJoin($filter);
        $join = empty($join) ? [] : ['join' => $join];

        $conditions = ['conditions' => $filter];
        $count      = $this->model::aggCount(array_merge($conditions, $join));

        if ($count == 0) {
            return ['list' => [], 'total' => $count];
        }

        $page     = $this->getPageNo();
        $pageSize = $this->getPageSize();

        $order  = $this->getPageOrder();
        $order  = empty($order) ? [] : ['order' => $order];

        // 处理连表等场景排序
        $this->hookService->onOrderBy($order);

        $offset = ['offset' => ($page - 1) * $pageSize];
        $limit  = ['limit' => $pageSize];

        $list = $this->model::aggFind(array_merge($conditions, $join, $order, $limit, $offset));

        // 处理关联数据
        $list = $this->fieldService->setAttach($list);

        // todo lesscode 解决一些关联数据查询
        $list = $this->formatList($list, function (&$item)
        {
            $this->hookService->onListFormat($item);
        });

        $list = $this->hookService->onAfterList($list);

        return ['list' => $list, 'total' => $count];
    }

    public function getPageOrder()
    {
        return empty($this->sort) ? [] : [$this->sort => $this->dir];
    }
}