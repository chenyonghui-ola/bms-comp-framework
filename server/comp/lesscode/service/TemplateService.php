<?php

namespace Imee\Service\Lesscode;

use \Imee\Service\BaseService;

use Imee\Service\Lesscode\Context\TemplateContext;

use Imee\Service\Lesscode\Logic\TemplateLogic;

/**
 * @property \Imee\Service\Lesscode\Logic\TemplateLogic templateLogic
 */
class TemplateService extends BaseService
{
    protected $factorys = [
        FactoryService::class
    ];

    public function getModel(TemplateContext $context)
    {
        $logic = new TemplateLogic($context);

        return $logic->getModel();
    }
}