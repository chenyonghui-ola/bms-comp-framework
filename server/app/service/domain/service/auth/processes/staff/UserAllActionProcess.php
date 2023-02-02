<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Models\Cms\CmsUserRoles;
use Imee\Models\Cms\CmsRoleModule;
use Imee\Models\Cms\CmsModules;

/**
 * 获取用户对应项目的所有权限
 */
class UserAllActionProcess extends BaseProcess
{
    private static $superInitModule = [
        'auth/modules.index',
        'auth/modules.info',
        'auth/modules.create',
        'auth/modules.modify',
        'auth/modules.remove',
        'auth/modules.search',
    ];

    public function handle($userInfo)
    {
        $returnData = [];
        $res = $userInfo['super'] ? $this->getSuperModule() : $this->getNormalModule($userInfo['user_id']);
        if (!empty($res)) {
            foreach ($res as $rec) {
                if (!$rec['is_action']) {
                    continue;
                }
                $returnData[] = $rec['controller'] . '.' . $rec['action'];
            }
        }

        return $userInfo['super'] ? array_merge($returnData, self::$superInitModule) : $returnData;
    }
}
