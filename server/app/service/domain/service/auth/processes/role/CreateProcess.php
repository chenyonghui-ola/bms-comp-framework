<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Role;

use Imee\Service\Domain\Context\Auth\Role\CreateContext;
use Imee\Models\Cms\CmsRoles;
use Imee\Models\Cms\CmsRoleModule;
use Imee\Models\Cms\CmsModules;
use Phalcon\Di;
use Imee\Exception\Auth\RoleException;
use Imee\Service\Helper;
use Imee\Service\Domain\Service\Auth\Processes\Role\Traits\ExractTreeTrait;

/**
 * 角色创建
 */
class CreateProcess
{
    use ExractTreeTrait;
    private $context;
    private $allModuleIds;

    public function __construct(CreateContext $context)
    {
        $this->context = $context;
    }

    private function verify()
    {
        $info = CmsRoles::findFirst([
            'conditions' => 'role_name = :role_name:',
            'bind' => array(
                'role_name' => $this->context->roleName,
            ),
            'order' => 'role_id desc'
        ]);
        if (!empty($info)) {
            list($code, $msg) = RoleException::NAME_REPEAT_ERROR;
            throw new RoleException($msg, $code);
        }

        //查询相关module是否存在
        $cmsModuleArr = CmsModules::find([
            'conditions' => 'module_id in({module_ids:array}) and system_id=:system_id: and is_action=:is_action:',
            'bind' => array(
                'module_ids' => $this->context->moduleIds,
                'system_id' => SYSTEM_ID,
                'is_action' => CmsModules::IS_ACTION_YES,
            ),
        ])->toArray();
        if (count($cmsModuleArr) != count($this->context->moduleIds)) {
            list($code, $msg) = RoleException::MODULE_NOEXIST_ERROR;
            throw new RoleException($msg, $code);
        }

        $parentModuleIds = array_column($cmsModuleArr, 'parent_module_id');
        if (!empty(array_diff($parentModuleIds, $this->allModuleIds))) {
            list($code, $msg) = RoleException::MODULE_PARENT_NOEXIST_ERROR;
            throw new RoleException($msg, $code);
        }
    }

    public function handle()
    {
        $conn = Di::getDefault()->getShared('cms');
        $session = Di::getDefault()->getShared('session');
        $adminUid = intval($session->get('uid'));

        $this->allModuleIds = $this->getAllModuleIds();
        
        $this->verify();
        $model = new CmsRoles();
        $model->role_name = $this->context->roleName;
        $model->system_id = SYSTEM_ID;
        $model->modify_username = $adminUid;

        try {
            $conn->begin();
            $model->save();

            if (!empty($this->context->moduleIds)) {
                foreach ($this->context->moduleIds as $moduleId) {
                    $rec = new CmsRoleModule();
                    $rec->role_id = $model->role_id;
                    $rec->module_id = $moduleId;
                    $rec->system_id = SYSTEM_ID;
                    $rec->save();
                }
            }
            if (!empty($this->allModuleIds)) {
                foreach ($this->allModuleIds as $moduleId) {
                    $rec = new CmsRoleModule();
                    $rec->role_id = $model->role_id;
                    $rec->module_id = $moduleId;
                    $rec->system_id = SYSTEM_ID;
                    $rec->save();
                }
            }
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            Helper::debugger()->error(__CLASS__ .' : '. $e->getMessage());
            list($code, $msg) = RoleException::CREATE_FAIL_ERROR;
            throw new RoleException($msg, $code);
        }
    }
}
