<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Models\Cms\CmsUser;
use Imee\Service\Domain\Context\Auth\Staff\BaseInfosContext;

/**
 * 获取用户基本信息
 */
class BaseInfosProcess
{
    private $context;
    public function __construct(BaseInfosContext $context)
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
        if (!empty($this->context->userIds)) {
            $where['condition'][] = 'user_id in({user_ids:array})';
            $where['bind']['user_ids'] = $this->context->userIds;
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

        $userModels = CmsUser::find([
            'conditions' => implode(' and ', $where['condition']),
            'bind' => $where['bind'],
        ]);

        foreach ($userModels as $userModel) {
            $tmp = [
                'user_id' => $userModel->user_id,
                'user_email' => $userModel->user_email,
                'user_name' => $userModel->user_name,
                'user_status' => $userModel->user_status
            ];
            $returnData[] = $tmp;
        }

        return array_column($returnData, null, 'user_id');
    }
}
