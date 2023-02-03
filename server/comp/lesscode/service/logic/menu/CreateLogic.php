<?php

namespace Imee\Service\Lesscode\Logic\Menu;


use Imee\Helper\MenuConfig;
use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Schema\AdapterSchema;
use Imee\Service\Domain\Context\Auth\Modules\GetInfoContext;
use Imee\Service\Domain\Context\Auth\Modules\InfoContext;
use Imee\Service\Domain\Context\Auth\Modules\ModulePointContext;
use Imee\Service\Domain\Service\Auth\ModulesService;
use Imee\Service\Lesscode\Context\Modules\CreateContext;
use Imee\Service\Lesscode\GetNameService;
use Imee\Service\Lesscode\MenuService;

class CreateLogic
{
    /**
     * @var CreateContext
     */
    private $context;

    /**
     * @var ModulesService
     */
    private $moduleService;

    /**
     * @var GetNameService
     */
    private $getNameService;

    /**
     * @var LesscodeMenu
     */
    private $masterModel = LesscodeMenu::class;

    /**
     * @var MenuService
     */
    private $menuService;

    public function __construct(CreateContext $context)
    {
        $this->context = $context;

        $this->moduleService  = new ModulesService();
        $this->getNameService = new GetNameService();
        $this->menuService    = new MenuService();
    }

    /**
     * 生成菜单
     */
    public function handle()
    {
        $this->create();

        // 创建后 查询出创建数据回写
        $moduleList = [
            $this->moduleService->getInfoBy(new GetInfoContext([
                'controller' => $this->moduleControllerName(),
                'action'     => $this->context->action
            ]))
        ];

        // todo 创建低代码这边菜单
        foreach ($this->context->modulePointContexts as $modulePointContext) {
            $moduleList[] = $this->moduleService->getInfoBy(new GetInfoContext([
                'controller' => $modulePointContext->controller,
                'action'     => $modulePointContext->action
            ]));
        }

        foreach ($moduleList as $module) {
            $model            = new $this->masterModel;
            $model->guid      = $this->context->controller;
//            $model->parent_id = $module['parent_module_id'];
            $model->menu_id   = $module['module_id'];
            $model->save();
        }
    }

    /**
     * 创建菜单
     */
    protected function create()
    {
        $moduleInfo = $this->moduleService->getInfoBy(new GetInfoContext([
            'controller' => $this->moduleControllerName(),
            'action'     => $this->context->action
        ]));

        // 组装功能点
        $modulePointContexts = [];
        $isCreate = false;

        foreach ($this->point() as $item) {
            $moduleItemInfo = $this->moduleService->getInfoBy(new GetInfoContext([
                'controller' => $this->moduleControllerName(),
                'action'     => $item['action']
            ]));

            if (empty($moduleItemInfo)) {
                $isCreate = true;
            }

            $context = new ModulePointContext(array_merge([
                'controller' => $this->moduleControllerName(),
            ], $item));

            $modulePointContexts[] = $context;
        }

        $this->context->setParams([
            'module_point_contexts' => $modulePointContexts
        ]);

        unset($modulePointContexts);

        if ($moduleInfo && false === $isCreate) {
            return;
        }

        $mContext = clone $this->context;
        $mContext->setParams([
            'controller' => $this->moduleControllerName(),
        ]);

        $this->moduleService->create($mContext);
    }

    private function moduleControllerName()
    {
//        if ($this->context->updateMenu == true) {
            // 重写控制器名称
            return $this->rewriteModuleControllerName();
//        }

//        return $this->getNameService->moduleControllerName($this->context->controller);
    }

    private function rewriteModuleControllerName()
    {
        // 查询菜单最上级菜单信息 取出模块名
        $info = $this->menuService->getTopParentMenu(['module_id' => $this->context->parentModuleId]);

        if (empty($info) || !isset($info['parent_module_id']) || $info['parent_module_id'] > 0) {
            $this->getNameService->moduleControllerName($this->context->controller);
        }

        $moduleName = $info['module_name'];
        $menuConfig = MenuConfig::getConfig();

        if (isset($menuConfig[$moduleName])) {
            return $this->getNameService->moduleControllerNameComplete($this->context->controller, $menuConfig[$moduleName]);
        }

        return $this->getNameService->moduleControllerName($this->context->controller);
    }

    /**
     * 默认增加的功能点 todo lesscode more
     * @return \string[][]
     */
    protected function point()
    {
        return [
            ['moduleName' => '列表', 'action' => AdapterSchema::POINT_LIST,   'is_deleted' => 0],
            ['moduleName' => '添加', 'action' => AdapterSchema::POINT_CREATE, 'is_deleted' => 1],
            ['moduleName' => '编辑', 'action' => AdapterSchema::POINT_MODIFY, 'is_deleted' => 1],
            ['moduleName' => '删除', 'action' => AdapterSchema::POINT_DELETE, 'is_deleted' => 1],
            ['moduleName' => '导出', 'action' => AdapterSchema::POINT_EXPORT, 'is_deleted' => 1],
        ];
    }
}