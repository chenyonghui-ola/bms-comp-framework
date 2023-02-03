<?php

namespace Imee\Service\Lesscode\Logic\Menu\Hooks;


use Imee\Service\Domain\Service\Auth\ModulesService;
use Imee\Service\Domain\Context\Auth\Modules\InfoContext;
use Imee\Service\Lesscode\Traits\Curd\ListTrait;

class ListLogic
{
    use ListTrait;

    private $params;

    private $modulesService;

    public function __construct()
    {
        $this->modulesService = new ModulesService();
    }

    public function onSetParams($params): void
    {
        $this->params = $params;
    }

    public function onGetFilter(&$filter)
    {
        if (isset($filter['guid_name'])) {
            $filter['guid'] = $filter['guid_name'];
            unset($filter['guid_name']);
        }
    }

    public function onListFormat(&$item)
    {
        $item['guid_name'] = $item['guid'];

        // 获取菜单信息
        $context = new InfoContext(['module_id' => $item['menu_id']]);
        $moduleInfo = (array) $this->modulesService->getInfoById($context);
        $moduleInfo['module_id'] = (string) $moduleInfo['module_id'];
        $item = array_merge($item, $moduleInfo);
        $item['parent_module_id'] = (string) $item['parent_module_id'];

        if (isset($item['deleted'])) {
            $item['deleted'] = (string) $item['deleted'];
        }

        if (isset($item['is_delete'])) {
            $item['is_delete'] = (string) $item['is_delete'];
        }

        unset($item['guid']);
    }

    public function onAfterList($list): array
    {

        return $list;
    }
}