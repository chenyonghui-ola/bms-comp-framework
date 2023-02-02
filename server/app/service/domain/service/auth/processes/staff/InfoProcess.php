<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Models\Cms\CmsUserRoles;

/**
 * 用户信息
 */
class InfoProcess
{
    private $userInfo;
    public function __construct($userInfo)
    {
        $this->userInfo = $userInfo;
    }

    public function handle()
    {
        if (empty($this->userInfo)) {
            return $this->userInfo;
        }
        $userRoleMap = CmsUserRoles::getUserRolesByUserIds([$this->userInfo['user_id']]);

        $this->userInfo['role_ids'] = isset($userRoleMap[$this->userInfo['user_id']]) ?
            array_column($userRoleMap[$this->userInfo['user_id']], 'role_id') : [];

        return [
            'user_name' => $this->userInfo['user_name'],
            'user_id' => $this->userInfo['user_id'],
            'user_email' => $this->userInfo['user_email'],
            'user_status' => $this->userInfo['user_status'],
            'role_ids' => $this->userInfo['role_ids'],
            'language' => explode(',', $this->userInfo['language']),
            'bigarea' => !empty($this->userInfo['bigarea']) ? explode(',', $this->userInfo['bigarea']) : [],
        ];
    }
}
