<?php

namespace Imee\Service\Lesscode\Schema;

use Imee\Service\Lesscode\Context\Schema\ListContext;
use Imee\Service\Lesscode\Logic\Schema\ListLogic;

use Imee\Service\Lesscode\Context\Schema\CreateContext;
use Imee\Service\Lesscode\Logic\Schema\CreateLogic;
use Imee\Service\Lesscode\Logic\Schema\ModifyLogic;


class SchemaPointService
{
    public function getList(ListContext $context)
    {
        $logic = new ListLogic($context);

        return $logic->handle();
    }

    public function create(CreateContext $context)
    {
        $logic = new CreateLogic($context);

        return $logic->handle();
    }

    public function modify(CreateContext $context)
    {
        $logic = new ModifyLogic($context);

        return $logic->handle();
    }
}