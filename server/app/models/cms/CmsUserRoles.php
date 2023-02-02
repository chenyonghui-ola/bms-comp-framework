<?php

namespace Imee\Models\Cms;

class CmsUserRoles extends BaseModel
{
    public static function getUserRolesByUserIds($userIds = [])
    {
        $userRoleMap = [];
        if (empty($userIds)) {
            return $userRoleMap;
        }
        $userRoleArr = CmsUserRoles::find([
            'conditions' => 'user_id in({user_ids:array}) and system_id=:system_id:',
            'bind' => [
                'user_ids' => $userIds,
                'system_id' => SYSTEM_ID,
            ],
        ])->toArray();
        
        if (empty($userRoleArr)) {
            return $userRoleMap;
        }
        $roleIds = array_column($userRoleArr, 'role_id');
        
        $roleArr = CmsRoles::find([
            'conditions' => 'role_id in({role_ids:array}) and system_id=:system_id:',
            'bind' => [
                'role_ids' => $roleIds,
                'system_id' => SYSTEM_ID,
            ],
        ])->toArray();

        $roleMap = array_column($roleArr, null, 'role_id');
        
        foreach ($userRoleArr as $userRole) {
            if (!isset($roleMap[$userRole['role_id']])) {
                continue;
            }
            $userRoleMap[$userRole['user_id']][] = $roleMap[$userRole['role_id']];
        }
        return $userRoleMap;
    }
}
