<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Models\Cms\CmsUser;
use Imee\Models\Cms\CmsModules;
use Imee\Models\Cms\CmsRoleModule;
use Imee\Models\Cms\CmsUserRoles;

/**
 * 获取用户对应项目的所有权限
 */
class UserInfoByPermissionProcess
{
    private $controller;
    private $action;
    public function __construct($controller, $action)
    {
        $this->controller = $controller;
        $this->action = $action;
    }

    private function getUids($moduleId)
    {
        $userIds = [];
        $roles = CmsRoleModule::find([
            'columns' => 'role_id',
            'conditions' => 'module_id = :module_id: and system_id=:system_id:',
            'bind' => [
                'module_id' => $moduleId,
                'system_id' => SYSTEM_ID,
            ]
        ])->toArray();

        if (empty($roles)) {
            return $userIds;
        }

        $roleIds = array_column($roles, 'role_id');

        $cmsUserRoles = CmsUserRoles::find([
            'columns' => 'user_id',
            'conditions' => 'role_id in({role_ids:array}) and system_id=:system_id:',
            'bind' => [
                'role_ids' => $roleIds,
                'system_id' => SYSTEM_ID,
            ]
        ])->toArray();

        if (empty($cmsUserRoles)) {
            return $userIds;
        }

        return array_column($cmsUserRoles, 'user_id');
    }

    private function getSuperUids()
    {
        $superUsers = CmsUser::find([
            'conditions' => 'system_id=:system_id: and super=:super:',
            'bind' => [
                'system_id' => 1,
                'super' => 1,
            ],
        ])->toArray();

        if (empty($superUsers)) {
            return [];
        }
        return array_column($superUsers, 'user_id');
    }

    public function handle()
    {
        $returnData = [];

        $module = CmsModules::findFirst([
            'conditions' => 'controller = :controller: and action = :action: and is_action=:is_action: and system_id=:system_id:',
            'bind' => [
                'controller' => $this->controller,
                'action' => $this->action,
                'system_id' => SYSTEM_ID,
                'is_action' => CmsModules::IS_ACTION_YES,
            ]
        ]);

        if (empty($module)) {
            return $returnData;
        }

        $userIds = array_merge(
            $this->getUids($module->module_id),
            $this->getSuperUids()
        );

        if (empty($userIds)) {
            return $returnData;
        }

        return CmsUser::find([
            'columns' => 'user_id, user_name',
            'conditions' => 'user_id in({user_ids:array})',
            'bind' => [
                'user_ids' => array_values($userIds),
            ],
        ])->toArray();
    }
}
