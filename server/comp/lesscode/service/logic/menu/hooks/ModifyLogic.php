<?php

namespace Imee\Service\Lesscode\Logic\Menu\Hooks;


use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Service\Domain\Service\Auth\ModulesService;
use Imee\Service\Domain\Context\Auth\Modules\InfoContext;
use Imee\Service\Lesscode\Exception\MenuException;
use Imee\Service\Lesscode\Traits\Curd\SaveTrait;
use Imee\Service\Lesscode\Traits\Help\ValidationParamsTrait;

class ModifyLogic
{
    use SaveTrait, ValidationParamsTrait;

    private $params;

    /**
     * @var LesscodeMenu
     */
    private $lesscodeMenuModel = LesscodeMenu::class;

    /**
     * @var LesscodeSchemaConfig
     */
    private $lesscodeSchemaModel = LesscodeSchemaConfig::class;

    private $modulesService;

    private $guid;

    /**
     * @var LesscodeMenu
     */
    private $info;

    private $moduleInfo;

    public function __construct()
    {
        $this->modulesService = new ModulesService();
    }

    public function onSetParams($params): void
    {
        $this->params = $params;
        $this->guid   = $params['guid_name'];
    }

    public function onBeforeSave(&$params)
    {
        $this->validation();
    }

    public function onSave($params)
    {
        if ($this->info->menu_id != $this->params['module_id']) {
            $this->info->menu_id = $this->params['module_id'];
        }

        if ($this->info->getChangedFields()) {
            $this->info->save();

            // todo lesscode 增加日志
        }

        return [];
    }

    public function onAfterSave($params, $model)
    {
        // 判断是否更新菜单数据
        if (!empty($this->moduleInfo)) {

            if ($this->moduleInfo->parent_module_id != $this->params['parent_module_id']) {
                $this->moduleInfo->parent_module_id = $this->params['parent_module_id'];
            }

            if ($this->moduleInfo->deleted != $this->params['deleted']) {
                $this->moduleInfo->deleted = $this->params['deleted'];
            }

            if ($this->moduleInfo->getChangedFields()) {
                $this->moduleInfo->save();
            }
        }
    }

    private function validation()
    {
        $this->validationFieldRequire('id', 'ID');
        $this->validationFieldRequire('guid_name', 'GUID');
        $this->validationFieldRequire('module_id', '菜单ID');

        $this->info = $this->lesscodeMenuModel::findFirstById(intval($this->params['id']));

        if (empty($this->info)) {
            MenuException::throwException(MenuException::NO_DATA_ERROR);
        }

        $schemaInfo = $this->lesscodeSchemaModel::findFirstByGuid($this->guid);

        if (empty($schemaInfo)) {
            MenuException::throwException(MenuException::MENU_GUID_INVALID);
        }

        $context = new InfoContext(['module_id' => $this->params['module_id'], 'format' => 'object']);
        $moduleInfo = $this->modulesService->getInfoById($context);

        if (empty($moduleInfo)) {
            MenuException::throwException(MenuException::MENU_INVALID);
        }

        $this->moduleInfo = $moduleInfo;

        if ($this->info->menu_id != $this->params['module_id']) {
            // menu id唯一
            $menuInfo = $this->lesscodeMenuModel::getInfoByMenu($this->params['module_id']);

            if (!empty($menuInfo)) {
                MenuException::throwException(MenuException::MENU_EXISTED);
            }
        }
    }
}