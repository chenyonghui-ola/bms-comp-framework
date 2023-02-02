<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Models\Cms\CmsUser;
use Imee\Service\Domain\Context\Auth\Staff\UserInfoContext;

/**
 * 获取用户对应项目的所有权限
 */
class UserInfoProcess
{
    private $context;
    public function __construct(UserInfoContext $context)
    {
        $this->context = $context;
    }

    private function buildWhere()
    {
        $where = [
            'condition' => [],
            'bind' => [],
        ];
        $where['condition'][] = 'system_id=:system_id:';
        $where['bind']['system_id'] = 1;
        
        if (!empty($this->context->userId)) {
            $where['condition'][] = 'user_id = :user_id:';
            $where['bind']['user_id'] = $this->context->userId;
        }

        if (!empty($this->context->userEmail)) {
            $where['condition'][] = 'user_email = :user_email:';
            $where['bind']['user_email'] = $this->context->userEmail;
        }

        return $where;
    }

    public function handle()
    {
        $where = $this->buildWhere();
        $returnData = [];

        
        if (empty($where['condition'])) {
            return $returnData;
        }

        $profile = CmsUser::findFirst([
            'conditions' => implode(' and ', $where['condition']),
            'bind' => $where['bind'],
        ]);

        if (empty($profile)) {
            return $returnData;
        }
        return $profile->toArray();
    }
}
