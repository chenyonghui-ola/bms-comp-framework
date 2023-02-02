<?php


namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Models\Cms\CmsModules;
use Imee\Models\Cms\CmsRoleModule;
use Imee\Models\Cms\CmsUserRoles;

class BaseProcess
{
    protected function getSuperRootMenu(): array
    {
        return CmsModules::findModules([], 0);
    }

    protected function getNormalRootMenu($uid): array
    {
        $moduleIds = $this->getModuleIdsByUid($uid);
        if (empty($moduleIds)) {
            return [];
        }
        return CmsModules::findModules($moduleIds, 0);
    }

    protected function getSuperMenu($root_id)
    {
        $modules = [];
        CmsModules::findModulesByParent($modules, [$root_id]);
        return $modules;
    }

    protected function getNormalMenu($uid, $root_id)
    {
        if ($uid <= 0) {
            return [];
        }

        $normal_modules = $this->getNormalModule($uid);
        if (empty($normal_modules)) {
            return [];
        }

        $modules = [];
        CmsModules::findModulesByParent($modules, [$root_id]);
        if (empty($modules)) {
            return [];
        }

        $normal_module_ids = array_column($normal_modules, 'module_id');

        $data = [];
        foreach ($modules as $module) {
            if (in_array($module['module_id'], $normal_module_ids)) {
                $data[] = $module;
            }
        }

        return $data;
    }

    protected function getSuperModule()
    {
        return CmsModules::findModules([]);
    }

    protected function getNormalModule($uid)
    {
        $moduleIds = $this->getModuleIdsByUid($uid);
        if (empty($moduleIds)) {
            return [];
        }
        return CmsModules::findModules($moduleIds);
    }

    protected function getModuleIdsByUid($uid)
    {
        //查找该用户的所有角色
        $roles = CmsUserRoles::find([
            'conditions' => 'user_id = :user_id: and system_id=:system_id:',
            'bind' => array(
                'user_id' => $uid,
                'system_id' => SYSTEM_ID,
            ),
            'order' => 'role_id desc'
        ])->toArray();

        if (empty($roles)) {
            return [];
        }

        $roleIds = array_column($roles, 'role_id');

        $roleModuleArr = CmsRoleModule::find([
            'conditions' => 'role_id in({role_ids:array}) and system_id=:system_id:',
            'bind' => array(
                'system_id' => SYSTEM_ID,
                'role_ids' => $roleIds,
            ),
        ])->toArray();
        if (empty($roleModuleArr)) {
            return [];
        }

        return array_column($roleModuleArr, 'module_id');
    }
}
