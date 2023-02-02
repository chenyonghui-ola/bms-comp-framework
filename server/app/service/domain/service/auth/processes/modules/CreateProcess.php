<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Modules;

use Imee\Service\Domain\Context\Auth\Modules\CreateContext;
use Imee\Models\Cms\CmsModules;
use Phalcon\Di;
use Imee\Service\Helper;
use Imee\Exception\Auth\ModulesException;

/**
 * 模块创建
 */
class CreateProcess extends CommonVeryfyProcess
{
    protected $context;
    protected $parentModule;

    public function handle()
    {
        !$this->check() && $this->vefiry();
        $mType = array_search($this->context->type, CmsModules::$displayMType);
        $conn = Di::getDefault()->getShared(\Imee\Models\Cms\BaseModel::SCHEMA);
        $session = Di::getDefault()->getShared('session');
        $adminUid = intval($session->get('uid'));
        $model = new CmsModules();
        $model->module_name = $this->context->moduleName;
        $model->icon = !empty($this->context->icon) ? $this->context->icon : '';
        $model->parent_module_id = !empty($this->context->parentModuleId) ? $this->context->parentModuleId : 0;
        $model->is_action = CmsModules::IS_ACTION_NO;

        $model->modify_username = $adminUid;
        $model->modify_time = date('Y-m-d H:i:s');
        $model->system_id = SYSTEM_ID;
        $model->m_type = $mType;

        $model->controller = $mType == CmsModules::M_TYPE_PAGE ? $this->context->controller : '';
        $model->action = $mType == CmsModules::M_TYPE_PAGE ? $this->context->action : '';

        $model->dateline = time();
        $model->create_uid = $adminUid;
        $model->modify_time = date('Y-m-d H:i:s');
        $model->system_id = SYSTEM_ID;

        try {
            $conn->begin();
            if ($mType == CmsModules::M_TYPE_PAGE && !$this->check()) {
                $names = explode('-', $this->controllerRoute['name']);
                $model->module_name = array_pop($names);
            }
            
            $model->save();

            if ($mType == CmsModules::M_TYPE_PAGE) {

                $this->check() && $this->rewriteControllerRoute();

                if (!empty($this->controllerRoute['points'])) {
                    foreach ($this->controllerRoute['points'] as $route) {
                        $pointModel = new CmsModules();
                        $pointModel->module_name = $route['name'];
                        $pointModel->parent_module_id = $model->module_id;
                        $pointModel->is_action = CmsModules::IS_ACTION_YES;
                        $pointModel->modify_username = $adminUid;
                        $pointModel->modify_time = date('Y-m-d H:i:s');
                        $pointModel->system_id = SYSTEM_ID;
                        $pointModel->m_type = CmsModules::M_TYPE_PAGE;
                        $pointModel->controller = $route['controller'];
                        $pointModel->action = $route['action'];
                        $pointModel->save();
                    }
                }
            }
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            Helper::debugger()->error(__CLASS__ .' : '. $e->getMessage());
            list($code, $msg) = ModulesException::CREATE_FAIL_ERROR;
            throw new ModulesException($msg, $code);
        }
    }

    protected function check()
    {
        return property_exists($this->context, 'flag');
    }

    protected function rewriteControllerRoute()
    {
        $this->controllerRoute['points'] = [];

        $modulePointContexts = $this->context->modulePointContexts;

        foreach ($modulePointContexts as $modulePointContext)
        {
            $tmp = $modulePointContext->toArray();
            $this->controllerRoute['points'][] = array_merge($tmp, ['name' => $tmp['module_name']]);
        }
    }
}
