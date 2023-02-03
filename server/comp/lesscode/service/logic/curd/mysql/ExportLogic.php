<?php


namespace Imee\Service\Lesscode\Logic\Curd\Mysql;

use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\FilterService;
use Imee\Service\Lesscode\Schema\FieldService;
use Imee\Service\Lesscode\Logic\Curd\ExportBaseLogic;

class ExportLogic extends ExportBaseLogic
{
    protected $opType = AdapterSchema::POINT_EXPORT;
    protected $drive  = AdapterSchema::DRIVE_MYSQL;

    public function getConditions()
    {
        $filter = $this->filterService
            ->setParams($this->params)
            ->setDrive($this->drive)
            ->getFilter();

        // 实现自己特殊的转化
        $this->hookService->onGetFilter($filter);

        return $filter;
    }


    public function rewriteExport()
    {
        $this->filterService = new FilterService($this->model, $this->schema);
        $this->fieldService  = new FieldService($this->model, $this->schema);

        $this->hookService->onSetParams($this->params);

        $filter = $this->filterService
            ->setParams($this->params)
            ->setDrive($this->drive)
            ->getFilter();

        $this->hookService->onGetFilter($filter);
        $result = $this->hookService->onList($filter, $this->params);

        return $result;
    }
}