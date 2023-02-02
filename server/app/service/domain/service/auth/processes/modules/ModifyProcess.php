<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Modules;

use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Service\Domain\Context\Auth\Modules\ModifyContext;
use Imee\Models\Cms\CmsModules;
use Phalcon\Di;
use Imee\Service\Helper;
use Imee\Exception\Auth\ModulesException;

/**
 * 模块修改
 */
class ModifyProcess extends CommonVeryfyProcess
{

    protected $context;
    protected $parentModule;
    protected $model;

    public function __construct(ModifyContext $context)
    {
        $this->context = $context;
    }

    protected function vefiry()
    {
        if (empty($this->model)) {
            list($code, $msg) = ModulesException::MODULE_NOEXIST_ERROR;
            throw new ModulesException($msg, $code);
        }

        $mType = array_search($this->context->type, CmsModules::$displayMType);
        if ($mType != $this->model->m_type) {
            list($code, $msg) = ModulesException::TYPE_NO_MATCH_ERROR;
            throw new ModulesException($msg, $code);
        }

        parent::vefiry();
    }

    

    public function handle()
    {
        $conn = Di::getDefault()->getShared('cms');
        $this->model = $model = CmsModules::findFirst([
            'conditions' => 'module_id = :module_id: and system_id=:system_id: and is_action=:is_action:',
            'bind' => array(
                'module_id' => $this->context->moduleId,
                'system_id' => SYSTEM_ID,
                'is_action' => CmsModules::IS_ACTION_NO,
            ),
            'order' => 'module_id desc'
        ]);

        $this->vefiry($model);

        $existPointModuleIds = [];
        $controllerRouteMap = [];
        if ($model->m_type == CmsModules::M_TYPE_PAGE) {
            //查询子模块
            $pointModels = CmsModules::find([
                'conditions' => 'parent_module_id = :parent_module_id: and system_id=:system_id: and '.
                    'is_action=:is_action:',
                'bind' => array(
                    'parent_module_id' => $this->context->moduleId,
                    'system_id' => SYSTEM_ID,
                    'is_action' => CmsModules::IS_ACTION_YES,
                ),
                'order' => 'module_id desc'
            ]);

            $controllerRouteMap = array_column($this->controllerRoute['points'], null, 'path');

            foreach ($pointModels as $pointModel) {
                $pointPath = '/' . $pointModel->controller . '/' . $pointModel->action;
                if (isset($controllerRouteMap[$pointPath])) {
                    $existPointModuleIds[] = $pointModel->module_id;
                    unset($controllerRouteMap[$pointPath]);
                    continue;
                }

                // 如果是低代码生成的菜单 在这里过滤
                $lesscodeMenu = LesscodeMenu::findFirstByMenuId($pointModel->module_id);
                if ($lesscodeMenu) {
                    $existPointModuleIds[] = $pointModel->module_id;
                }
            }
        }

        $session = Di::getDefault()->getShared('session');
        $adminUid = intval($session->get('uid'));

        $model->module_name = $this->context->moduleName;
        $model->icon = !empty($this->context->icon) ? $this->context->icon : '';

        $model->modify_username = $adminUid;
        $model->modify_time = date('Y-m-d H:i:s');

        try {
            $conn->begin();

            if ($model->m_type == CmsModules::M_TYPE_PAGE) {
                $newArr = explode('-', $this->controllerRoute['name']);
                $model->module_name = array_pop($newArr);
                
                foreach ($pointModels as $pointModel) {
                    if (in_array($pointModel->module_id, $existPointModuleIds)) {
                        continue;
                    }
                    $pointModel->delete();
                }
                if (!empty($controllerRouteMap)) {
                    foreach ($controllerRouteMap as $route) {
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
            $model->save();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            Helper::debugger()->error(__CLASS__ .' : '. $e->getMessage());
            list($code, $msg) = ModulesException::MODIFY_FAIL_ERROR;
            throw new ModulesException($msg, $code);
        }
    }
}
