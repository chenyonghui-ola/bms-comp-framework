<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Role;

use Imee\Service\Domain\Context\Auth\Role\InfoContext;
use Imee\Models\Cms\CmsRoles;
use Imee\Models\Cms\CmsRoleModule;
use Imee\Models\Cms\CmsModules;
use Phalcon\Di;
use Imee\Service\Helper;
use Imee\Service\Domain\Service\Auth\Processes\Traits\MenuFormatTrait;

/**
 * 角色明细
 */
class InfoProcess
{
    use MenuFormatTrait;
    private $context;

    public function __construct(InfoContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        $data = [];
        $info = CmsRoles::findFirst([
            'conditions' => 'role_id = :id: and system_id=:system_id:',
            'bind' => array(
                'id' => $this->context->roleId,
                'system_id' => SYSTEM_ID,
            ),
            'order' => 'role_id desc'
        ]);
        if (empty($info)) {
            return $data;
        }

        $data = $info->toArray();
        $data['menus'] = $data['pages'] = $data['points'] = [];
       
        $roleModuleArr = CmsRoleModule::find([
                'conditions' => 'role_id = :id: and system_id=:system_id:',
                'bind' => array(
                    'id' => $this->context->roleId,
                    'system_id' => SYSTEM_ID,
                ),
            ])->toArray();
        
        if (empty($roleModuleArr)) {
            return $data;
        }

        $moduleIds = array_column($roleModuleArr, 'module_id');
        $moduleArr = CmsModules::find([
            'conditions' => 'module_id in({module_ids:array}) and system_id=:system_id:',
            'bind' => array(
                'module_ids' => $moduleIds,
                'system_id' => SYSTEM_ID,
            ),
        ])->toArray();
        if (empty($moduleArr)) {
            return $data;
        }
        
        $moduleArr = $this->formatMenuList($moduleArr);
        $data['menus'] = $moduleArr['menus'];
        $data['pages'] = $moduleArr['pages'];
        $data['points'] = $moduleArr['points'];
        
        return $data;
    }
}
