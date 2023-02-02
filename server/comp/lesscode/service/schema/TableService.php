<?php

namespace Imee\Service\Lesscode\Schema;

use Imee\Service\Lesscode\Context\TableContext;
use Imee\Service\Lesscode\Logic\Table\CreateLogic;

use Imee\Service\Lesscode\Context\Table\ParseContext;
use Imee\Service\Lesscode\Logic\Table\ParseLogic;

class TableService
{
    // 创建表
    public function create(TableContext $context)
    {
        $logic = new CreateLogic($context);

        return $logic->handle();
    }

    /**
     * 解析表
     * @param  array  $context
     * @return string
     */
    public function parse($params): array
    {
        $context = new ParseContext($params);
        $logic   = new ParseLogic($context);
        return $logic->handle();
    }
}