<?php

namespace Imee\Service\Lesscode\Logic\Menu\Hooks;


use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Service\Domain\Service\Auth\ModulesService;
use Imee\Service\Domain\Context\Auth\Modules\InfoContext;
use Imee\Service\Lesscode\Exception\MenuException;
use Imee\Service\Lesscode\Traits\Curd\CreateTrait;
use Imee\Service\Lesscode\Traits\Help\ValidationParamsTrait;

class CreateLogic
{
    use CreateTrait, ValidationParamsTrait;

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

    public function __construct()
    {
        $this->modulesService = new ModulesService();
    }

    public function onSetParams($params): void
    {
        $this->params = $params;
        $this->guid   = $params['guid_name'];
    }

    public function onBeforeCreate(&$params)
    {
        $this->validation();
    }

    public function onCreate($params)
    {
        $model = new $this->lesscodeMenuModel;
        $model->menu_id = $this->params['module_id'];
        $model->guid = $this->params['guid_name'];
        $model->save();

        // todo lesscode 增加日志

        return [];
    }

    public function onAfterCreate($params, $model)
    {

    }

    private function validation()
    {
        $this->validationFieldRequire('guid_name', 'GUID');
        $this->validationFieldRequire('module_id', '菜单ID');

        $schemaInfo = $this->lesscodeSchemaModel::findFirstByGuid($this->guid);

        if (empty($schemaInfo)) {
            MenuException::throwException(MenuException::MENU_GUID_INVALID);
        }

        $context = new InfoContext(['module_id' => $this->params['module_id']]);
        $moduleInfo = $this->modulesService->getInfoById($context);

        if (empty($moduleInfo)) {
            MenuException::throwException(MenuException::MENU_INVALID);
        }

        // menu id唯一
        $menuInfo = $this->lesscodeMenuModel::getInfoByMenu($this->params['module_id']);

        if (!empty($menuInfo)) {
            MenuException::throwException(MenuException::MENU_EXISTED);
        }
    }
}