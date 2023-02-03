<?php


namespace Imee\Service\Lesscode\Logic\Curd\Api;

use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\FilterService;
use Imee\Service\Lesscode\Logic\Curd\BaseLogic;
use Imee\Service\Lesscode\Schema\FieldService;

class ListLogic extends BaseLogic
{
    protected $opType = AdapterSchema::POINT_LIST;
    protected $drive  = AdapterSchema::DRIVE_API;

    public function handle()
    {
        parent::handle();

        return $this->rewriteList();
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
}