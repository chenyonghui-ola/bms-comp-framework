<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Service\Domain\Context\Auth\Staff\CreateContext;
use Imee\Models\Cms\CmsUser;
use Imee\Models\Cms\CmsRoles;
use Imee\Models\Cms\CmsUserRoles;
use Imee\Exception\Auth\StaffException;
use Imee\Libs\Google2FA;
use Phalcon\Di;
use Imee\Service\Helper;

/**
 * 用户创建
 */
class CreateProcess
{
    private $context;
    public function __construct(CreateContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        $cmsUserModel = CmsUser::findFirst([
            'conditions' => 'user_email=:user_email: and system_id=:system_id:',
            'bind' => [
                'user_email' => $this->context->userEmail,
                'system_id' => 1,
            ],
        ]);
        if (!empty($cmsUserModel)) {
            list($code, $msg) = StaffException::EMAIL_EXIST_ERROR;
            throw new StaffException($msg, $code);
        }
        $roleArr = [];
        if ($this->context->roleIds) {
            $roleArr = CmsRoles::find([
                'conditions' => 'role_id in({role_ids:array}) and system_id=:system_id:',
                'bind' => array(
                    'role_ids' => $this->context->roleIds,
                    'system_id' => SYSTEM_ID,
                )
            ])->toArray();
        }

        if (count($roleArr) != count($this->context->roleIds)) {
            list($code, $msg) = StaffException::ROLE_NOEXIST_ERROR;
            throw new StaffException($msg, $code);
        }

        $session = Di::getDefault()->getShared('session');
        $conn = Di::getDefault()->getShared('cms');

        $adminUid = intval($session->get('uid'));
        $model = new CmsUser();
        $model->user_name = $this->context->userName;
        $model->user_email = $this->context->userEmail;
        $model->password = md5($this->context->password);
        $model->salt = Google2FA::generate_secret_key();
        $model->user_status = $this->context->userStatus;
        $model->is_salt = 1;
        $model->modify_username = $adminUid;
        $model->language = $this->context->language ? implode(',', $this->context->language) : '';
        $model->bigarea = $this->context->bigarea ? implode(',', $this->context->bigarea) : '';
        try {
            $conn->begin();
            $model->save();

            foreach ($this->context->roleIds as $roleId) {
                $rec = new CmsUserRoles();
                $rec->user_id = $model->user_id;
                $rec->role_id = $roleId;
                $rec->system_id = SYSTEM_ID;
                $rec->save();
            }
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            Helper::debugger()->error(__CLASS__ .' : '. $e->getMessage());
            Helper::debugger()->error(__CLASS__ .' : '. $e->getTraceAsString());
            list($code, $msg) = StaffException::CREATE_FAIL_ERROR;
            throw new StaffException($msg, $code);
        }
    }
}
