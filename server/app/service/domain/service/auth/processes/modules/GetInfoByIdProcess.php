<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Modules;

use Imee\Models\Cms\CmsModules;
use Imee\Service\Domain\Context\Auth\Modules\InfoContext;

/**
 * 模块明细
 */
class GetInfoByIdProcess
{
    /**
     * @var InfoContext
     */
    private $context;

    public function __construct(InfoContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        $model = CmsModules::findFirstByModuleId($this->context->moduleId);

        return $model ? (empty($this->context->format) || $this->context->format == 'array' ? $model->toArray() : $model)  : [];
    }
}
