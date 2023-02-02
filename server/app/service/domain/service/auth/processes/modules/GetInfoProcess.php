<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Modules;

use Imee\Models\Cms\CmsModules;
use Imee\Service\Domain\Context\Auth\Modules\GetInfoContext;

/**
 * 模块明细
 */
class GetInfoProcess
{
    /**
     * @var GetInfoContext
     */
    private $context;

    public function __construct(GetInfoContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        $model = CmsModules::findFirst([
            'conditions' => 'controller = :controller: and action = :action: and system_id=:system_id:',
            'bind'       => [
                'controller' => $this->context->controller,
                'action' => $this->context->action,
                'system_id' => SYSTEM_ID
            ],
        ]);

        return $model ? $model->toArray() : [];
    }
}
