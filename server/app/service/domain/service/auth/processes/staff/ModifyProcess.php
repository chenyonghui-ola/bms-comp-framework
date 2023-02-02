<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Service\Domain\Context\Auth\Staff\ModifyContext;
use Imee\Models\Cms\CmsUser;
use Imee\Models\Cms\CmsRoles;
use Imee\Models\Cms\CmsUserRoles;
use Imee\Exception\Auth\StaffException;
use Imee\Libs\Google2FA;
use Phalcon\Di;
use Imee\Service\Helper;

/**
 * 用户修改
 */
class ModifyProcess
{
    private $context;
    public function __construct(ModifyContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        $model = CmsUser::findFirst([
            'conditions' => 'user_id=:user_id: and system_id=:system_id:',
            'bind' => [
                'user_id' => $this->context->userId,
                'system_id' => 1,
            ],
        ]);

        if (empty($model)) {
            list($code, $msg) = StaffException::DATA_NOEXIST_ERROR;
            throw new StaffException($msg, $code);
        }

        if ($model->user_email != $this->context->userEmail) {
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
            $model->user_email = $this->context->userEmail;
        }

        if ($model->user_name != $this->context->userName) {
            $model->user_name = $this->context->userName;
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
        
        
        
        if (!empty($this->context->password)) {
            $model->password = md5($this->context->password);
            $model->salt = Google2FA::generate_secret_key();
        }
        
        $model->user_status = $this->context->userStatus;
        
        $model->modify_username = $adminUid;

        $model->language = $this->context->language ? implode(',', $this->context->language) : '';
        $model->bigarea = $this->context->bigarea ? implode(',', $this->context->bigarea) : '';
        
        try {
            $conn->begin();
            $model->save();
            
            Helper::exec("delete from cms_user_roles where user_id = {$model->user_id} and system_id = " . SYSTEM_ID);
            
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
            list($code, $msg) = StaffException::MODIFY_FAIL_ERROR;
            throw new StaffException($msg, $code);
        }
    }
}
