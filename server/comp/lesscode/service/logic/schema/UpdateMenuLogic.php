<?php

namespace Imee\Service\Lesscode\Logic\Schema;

use Imee\Helper\MenuConfig;
use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Schema\AdapterSchema;
use Imee\Service\Domain\Context\Auth\Modules\GetInfoContext;
use Imee\Service\Domain\Service\Auth\ModulesService;
use Imee\Service\Lesscode\Context\GuidContext;
use Imee\Service\Lesscode\Context\Menu\UpdateMenuContext;
use Imee\Service\Lesscode\Exception\FormException;
use Imee\Service\Lesscode\GetNameService;
use Imee\Service\Lesscode\MenuService;
use Imee\Service\Lesscode\Context\Modules\CreateContext as ModulesCreateContext;
use Imee\Service\Domain\Context\Auth\Modules\InfoContext;

class UpdateMenuLogic
{
    /**
     * @var UpdateMenuContext
     */
    protected $context;

    protected $conditions = [];

    /**
     * @var LesscodeSchemaPoint
     */
    protected $masterModel = LesscodeSchemaPoint::class;

    /**
     * @var LesscodeSchemaPointConfig
     */
    protected $configModel = LesscodeSchemaPointConfig::class;

    /**
     * @var LesscodeSchemaConfig
     */
    protected $schemaConfigModel = LesscodeSchemaConfig::class;

    /**
     * @var LesscodeMenu
     */
    protected $schemaMenuModel = LesscodeMenu::class;

    /**
     * @var MenuService
     */
    protected $menuService;

    /**
     * @var ModulesService
     */
    private $moduleService;

    /**
     * @var GetNameService
     */
    private $getNameService;

    /**
     * @var ModulesCreateContext
     */
    protected $menuCreateContext;


    protected $parentId;

    public function __construct(UpdateMenuContext $context)
    {
        $this->context        = $context;
        $this->menuService    = new MenuService();
        $this->getNameService = new GetNameService();
        $this->moduleService  = new ModulesService();
    }

    public function handle()
    {
        $this->parentId = $this->context->params['config'];

        if ($this->menuService->checkCreate(new GuidContext(['guid' => $this->context->guid]))) {
            $this->create();
        } else {
            $this->update();
        }
    }

    private function create()
    {
        $this->menuCreateContext = $this->menuService->init();
        $this->menuCreateContext->setParams([
            // 使用guid作为控制器名字
            'module_name'      => $this->getMainModuleName(),
            'controller'       => $this->context->guid,
            'parent_module_id' => $this->parentId,
            // 更新菜单逻辑
            'update_menu'      => true,
        ]);
        $this->menuService->create($this->menuCreateContext);
    }

    private function update()
    {
        // 查询原菜单数据
        $guidMenu = $this->schemaMenuModel::findFirstByGuid($this->context->guid);
        $guidInfo = [];

        if (!empty($guidMenu)) {
            $guidInfo = $this->menuService->getTopParentMenu(['module_id' => $guidMenu->menu_id]);
        }

        // 查询出相关的几个菜单即可 然后更新menu表
        $topInfo = $this->menuService->getTopParentMenu(['module_id' => $this->parentId]);

        if (empty($topInfo) || !isset($topInfo['parent_module_id']) || $topInfo['parent_module_id'] > 0) {
            return;
        }

        $moduleName = !empty($guidInfo) ? $guidInfo['module_name'] : $topInfo['module_name'];
        $menuConfig = MenuConfig::getConfig();

        // 需要更新菜单
        $updateModuleArr = [];

        if (isset($menuConfig[$moduleName])) {
            $controller = $this->getNameService->moduleControllerNameComplete($this->context->guid, $menuConfig[$moduleName]);

            $info = $this->moduleService->getInfoBy(new GetInfoContext([
                'controller' => $controller,
                'action'     => AdapterSchema::POINT_MAIN,
            ]));

            if (empty($info)) {
                $controller = $this->getNameService->moduleControllerName($this->context->guid);
                $info       = $this->moduleService->getInfoBy(new GetInfoContext([
                    'controller' => $controller,
                    'action'     => AdapterSchema::POINT_MAIN,
                ]));

                $info && $updateModuleArr[] = $info;
            }

        } else {
            $controller = $this->getNameService->moduleControllerName($this->context->guid);

            $info = $this->moduleService->getInfoBy(new GetInfoContext([
                'controller' => $controller,
                'action'     => AdapterSchema::POINT_MAIN,
            ]));
        }

        if (empty($info)) {
            [$code, $msg] = FormException::MENU_MAIN_NOT_EXIST;
            throw new FormException($msg, $code);
        }

        // 父菜单控制器
        if (isset($menuConfig[$topInfo['module_name']])) {
            $parentController = $this->getNameService->moduleControllerNameComplete($this->context->guid, $menuConfig[$topInfo['module_name']]);
        } else {
            $parentController = $this->getNameService->moduleControllerName($this->context->guid);
        }

        $arr = [$info['module_id']];

        // 查询其他菜单
        foreach (AdapterSchema::getInstance([])->pointArr as $point) {
            // 单独处理了
            if ($point === AdapterSchema::POINT_MAIN) {
                continue;
            }

            if (isset($menuConfig[$moduleName])) {
                $info = $this->moduleService->getInfoBy(new GetInfoContext([
                    'controller' => $this->getNameService->moduleControllerNameComplete($this->context->guid, $menuConfig[$moduleName]),
                    'action'     => $point,
                ]));

                if (empty($info)) {
                    $info              = $this->moduleService->getInfoBy(new GetInfoContext([
                        'controller' => $this->getNameService->moduleControllerName($this->context->guid),
                        'action'     => $point,
                    ]));
                    $info && $updateModuleArr[] = $info;
                }

            } else {
                $info = $this->moduleService->getInfoBy(new GetInfoContext([
                    'controller' => $this->getNameService->moduleControllerName($this->context->guid),
                    'action'     => $point,
                ]));
            }

            if ($info) {
                $arr[] = $info['module_id'];
            }
        }

        $menuList = $this->schemaMenuModel::findByGuid($this->context->guid);

        // 更新父菜单
        if (!empty($arr)) {
            $this->updateParent($arr, $parentController);
        }

        if (empty($menuList)) {
            $this->insertData($arr);
        } else {
            foreach ($menuList as $k => $menu) {
                $id = $arr[$k] ?? '';
                unset($arr[$k]);

                if (empty($id)) {
                    continue;
                }

                if ($menu->menu_id != $id) {
                    $menu->menu_id = $id;
                    $menu->save();
                }
            }

            if (!empty($arr)) {
                $this->insertData($arr);
            }
        }

        if (!empty($updateModuleArr)) {
            foreach ($updateModuleArr as $item) {
                $info = $this->moduleService->getInfoById(new InfoContext([
                    'module_id' => $item['module_id'],
                    'format'    => 'object',
                ]));

                if (!empty($info)) {
                    $info->controller = $this->getNameService->moduleControllerNameComplete($this->context->guid, $menuConfig[$moduleName]);
                    $info->save();
                }
            }
        }
    }

    private function getMainModuleName()
    {
        $config      = $this->schemaConfigModel::findFirstByGuid($this->context->guid);
        $tableConfig = (array) json_decode($config->table_config, true);
        return $tableConfig['comment'] ?? '';
    }

    private function insertData($list)
    {
        foreach ($list as $id) {
            $model          = new $this->schemaMenuModel;
            $model->guid    = $this->context->guid;
            $model->menu_id = $id;
            $model->save();
        }
    }

    private function updateParent($arr, $controller = null)
    {
        try {
            $pinfo       = $this->moduleService->getInfoById(new InfoContext(['module_id' => $this->parentId, 'format' => 'object']));
            $pointMainId = 0;

            foreach ($arr as $id) {
                $context = new InfoContext(['module_id' => $id, 'format' => 'object']);
                $info    = $this->moduleService->getInfoById($context);

                // 是个菜单 只需要把 main菜单挂上
                if ($pinfo->m_type == 1) {
                    if ($info->action == AdapterSchema::POINT_MAIN) {
                        $pointMainId            = $info->module_id;
                        $info->parent_module_id = $this->parentId;

                        if (!empty($controller)) {
                            $info->controller = $controller;
                        }

                        $info->save();
                    } else {

                        if (!empty($controller)) {
                            $info->controller = $controller;
                        }

                        $info->parent_module_id = $pointMainId > 0 ? $pointMainId : $this->parentId;
                        $info->save();
                    }
                }

                // 是个功能 所有功能全部挂上
                if ($pinfo->m_type == 2) {

                    if (!empty($controller)) {
                        $info->controller = $controller;
                    }
                    $info->parent_module_id = $this->parentId;
                    $info->save();
                }
            }

        } catch (\Exception $e) {

        }
    }
}