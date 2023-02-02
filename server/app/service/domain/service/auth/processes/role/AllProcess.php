<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Role;

use Imee\Models\Cms\CmsRoles;
use Phalcon\Di;
use Imee\Service\Domain\Service\Auth\Processes\Role\Traits\RoleModelTrait;

/**
 * 角色
 */
class AllProcess
{
    use RoleModelTrait;
    public function handle()
    {
        $items = CmsRoles::find([
            'conditions' => 'system_id=:system_id:',
            'bind' => [
                'system_id' => SYSTEM_ID,
            ],
        ]);
        return $this->formatList($items);
    }
}
