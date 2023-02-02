<?php


namespace Imee\Service\Lesscode\Logic\Curd\Api;

use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\Logic\Curd\BaseLogic;

class DeleteLogic extends BaseLogic
{
    protected $opType = AdapterSchema::POINT_DELETE;
    protected $drive  = AdapterSchema::DRIVE_API;

    public function handle()
    {
        parent::handle();

        $this->hookService->onBeforeDelete($this->params, []);

        $bool = $this->hookService->onDelete($this->params);

        if (false === $bool) {
            [$code, $msg] = CurdException::DELETE_ERROR;
            throw new CurdException($msg, $code);
        }

        $res = $this->hookService->onAfterDelete($this->params, []);

        return $res;
    }
}