<?php


namespace Imee\Service\Lesscode\Logic\Curd\Api;

use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\Logic\Curd\BaseLogic;

class CreateLogic extends BaseLogic
{
    protected $opType = AdapterSchema::POINT_CREATE;
    protected $drive  = AdapterSchema::DRIVE_API;

    public function handle()
    {
        parent::handle();

        $this->hookService->onBeforeCreate($this->params);

        $bool = $this->hookService->onCreate($this->params);

        if (false === $bool) {
            [$code, $msg] = CurdException::CREATE_ERROR;
            throw new CurdException($msg, $code);
        }

        $res = $this->hookService->onAfterCreate($this->params, []);

        return $res;
    }
}