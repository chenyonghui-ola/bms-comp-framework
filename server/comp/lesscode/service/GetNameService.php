<?php

namespace Imee\Service\Lesscode;

use Imee\Service\Lesscode\Logic\GetNameLogic;

class GetNameService
{
    public function getModel($guid)
    {
        return GetNameLogic::getModel($guid);
    }

    public function getSchema($guid)
    {
        return GetNameLogic::getSchema($guid);
    }

    public function getAll($guid)
    {
        return GetNameLogic::getAll($guid);
    }

    public function moduleControllerName($controller)
    {
        return GetNameLogic::moduleControllerName($controller);
    }

    public function moduleControllerNameComplete($controller, $prefix)
    {
        return GetNameLogic::moduleControllerNameComplete($controller, $prefix);
    }
}