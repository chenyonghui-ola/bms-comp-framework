<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Modules;

use Imee\Service\Domain\Context\Auth\Modules\PointListContext;
use Imee\Models\Cms\CmsModules;
use Phalcon\Di;

class PointListProcess
{
    private $context;

    public function __construct(PointListContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        return CmsModules::find([
            'conditions' => 'parent_module_id = :parent_module_id: and system_id=:system_id: and is_action=:is_action:',
            'bind' => array(
                'parent_module_id' => $this->context->parentModuleId,
                'system_id' => SYSTEM_ID,
                'is_action' => CmsModules::IS_ACTION_YES,
            ),
            'order' => 'module_id desc'
        ])->toArray();
    }
}
