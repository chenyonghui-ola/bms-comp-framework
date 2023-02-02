<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Models\Cms\CmsUser;

class AllStaffPorcess
{
    public function handle()
    {
        $users = CmsUser::find([
            'columns' => 'user_id, user_name, user_email',
            'conditions' => 'user_status = :user_status: and system_id=:system_id:',
            'bind' => [
                'user_status' => CmsUser::USER_STATUS_VALID,
                'system_id' => 1
            ]
        ])->toArray();
        return $users;
    }
}
