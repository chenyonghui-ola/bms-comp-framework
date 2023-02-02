<?php


namespace Imee\Service\Lesscode\Logic\Curd\Mysql;

use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\FilterService;
use Imee\Service\Lesscode\Schema\FieldService;
use Imee\Service\ModelSupportService;
use Imee\Service\Lesscode\Logic\Curd\BaseLogic;

class ListLogic extends BaseLogic
{
    protected $opType = AdapterSchema::POINT_LIST;
    protected $drive  = AdapterSchema::DRIVE_MYSQL;

    public function handle()
    {
        parent::handle();

        if (true === $this->hookService->onRewriteList()) {
            return $this->rewriteList();
        }

        $this->filterService = new FilterService($this->model, $this->schema);
        $this->fieldService  = new FieldService($this->model, $this->schema);

        $this->hookService->onSetParams($this->params);

        $filter = $this->filterService
            ->setParams($this->params)
            ->setDrive($this->drive)
            ->getFilter();

        // 实现自己特殊的转化
        $this->hookService->onGetFilter($filter);

        // 不存在的字段过滤
        $this->filter($filter);

        $filter['_model'] = get_class($this->model);

        if (defined('LESSCODE_VERSION') && version_compare(LESSCODE_VERSION, '1.1', '>=')) {
            $join = $this->hookService->onJoin($filter);

            // 关联查询
            if (!empty($join)) {
                $filter['_join'] = $join;
            }
        }

        $count = ModelSupportService::getCount($filter);

        if ($count == 0) {
            return ['list' => [], 'total' => $count];
        }

        $page     = $this->getPageNo();
        $pageSize = $this->getPageSize();
        $order    = $this->getPageOrder();

        // 处理连表等场景排序
        $this->hookService->onOrderBy($order);
//        $fields = $this->fieldService->getListShowFields();
        $fields = $this->hookService->onGetColumns();
        $fields = !empty($fields) ? $fields : '*';
        $list = ModelSupportService::getList($filter, $fields, $order, $page, $pageSize)->toArray();

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

    public function rewriteList()
    {
        $result = [
            'list'  => [],
            'total' => 0,
        ];

        $this->filterService = new FilterService($this->model, $this->schema);
        $this->fieldService  = new FieldService($this->model, $this->schema);

        $this->hookService->onSetParams($this->params);

        $filter = $this->filterService
            ->setParams($this->params)
            ->setDrive($this->drive)
            ->getFilter();

        $this->hookService->onGetFilter($filter);
        $res = $this->hookService->onList($filter, $this->params);
        $list = $res['list'] ?? [];

        if (!empty($list)) {
            $list = $this->formatList($list, function (&$item)
            {
                $this->hookService->onListFormat($item);
            });

            $list = $this->hookService->onAfterList($list);
            $res['list'] = $list;
        }

        $result['list']  = $res['list'] ?? [];
        $result['total'] = $res['total'] ?? 0;

        return $result;
    }

    private function filter(&$filter)
    {
        if (empty($filter)) {
            return;
        }

        if (!method_exists($this->model, 'getTableFields')) {
            return;
        }

        $fields = $this->model::getTableFields();

        foreach ($filter as $key => $value)
        {
            if (false === stripos($key, '.')) {
                if (!in_array($key, $fields)) {
                    unset($filter[$key]);
                }
            }
        }
    }
}