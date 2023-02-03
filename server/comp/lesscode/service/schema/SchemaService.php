<?php

namespace Imee\Service\Lesscode\Schema;

use Imee\Service\Lesscode\Context\GuidContext;
use Imee\Service\Lesscode\Context\ListConfigContext;
use Imee\Service\Lesscode\FactoryService;

use Imee\Service\Lesscode\Context\Schema\ParseContext;
use Imee\Service\Lesscode\Logic\Schema\ParseLogic;

class SchemaService
{
    public function getConfig(GuidContext $context)
    {
        return FactoryService::get('schemaConfigLogic', $context)->handle();
    }

    public function getListConfig(ListConfigContext $context)
    {
        return FactoryService::get('listConfigLogic', $context)->handle();
    }

    public function getFields(GuidContext $context)
    {
        return FactoryService::get('listFieldsLogic', $context)->handle();
    }

    public function convertSchemaJson($params): array
    {
        $context = new ParseContext($params);
        $logic   = new ParseLogic($context);
        return $logic->handle();
    }
}