<?php


namespace Imee\Service\Lesscode\Logic\Curd\Api;

use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\Logic\Curd\BaseLogic;

class SaveLogic extends BaseLogic
{
    protected $opType = AdapterSchema::POINT_MODIFY;
    protected $drive  = AdapterSchema::DRIVE_API;

    public function handle()
    {
        parent::handle();

        $this->hookService->onBeforeSave($this->params, []);

        $bool = $this->hookService->onSave($this->params);

        if (false === $bool) {
            [$code, $msg] = CurdException::SAVE_ERROR;
            throw new CurdException($msg, $code);
        }

        $res = $this->hookService->onAfterSave($this->params, []);

        return $res;
    }
}