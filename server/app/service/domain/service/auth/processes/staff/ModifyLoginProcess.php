<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Service\Domain\Context\Auth\Staff\ModifyContext;
use Imee\Models\Cms\CmsUser;
use Imee\Models\Cms\CmsUserLog;
use Imee\Models\Cms\CmsRoles;
use Imee\Models\Cms\CmsUserRoles;
use Imee\Exception\Auth\StaffException;
use Imee\Libs\Google2FA;
use Phalcon\Di;
use Imee\Service\Helper;

/**
 * 用户登录
 */
class ModifyLoginProcess
{
    public function handle()
    {
        $model = CmsUser::findFirst([
            'conditions' => 'user_id=:user_id:',
            'bind' => [
                'user_id' => Helper::getSystemUid(),
                
            ],
        ]);

        if (empty($model)) {
            list($code, $msg) = StaffException::DATA_NOEXIST_ERROR;
            throw new StaffException($msg, $code);
        }
        $model->last_login_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        $model->save();
    }
}
