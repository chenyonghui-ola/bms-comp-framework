<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Modules;

use Imee\Models\Cms\CmsModules;
use Imee\Service\Domain\Service\Auth\Processes\Traits\MenuFormatTrait;

/**
 * 模块列表
 */
class ListProcess
{
    use MenuFormatTrait;
    public function handle()
    {
        $res = CmsModules::find(array(
            'conditions' => 'system_id=:system_id: and is_action=:is_action:',
            'bind' => array(
                'system_id' => SYSTEM_ID,
                'is_action' => CmsModules::IS_ACTION_NO,
            ),
            'order' => 'parent_module_id asc, module_id asc'
        ));

        return $this->formatMenuList($res->toArray());
    }
}
