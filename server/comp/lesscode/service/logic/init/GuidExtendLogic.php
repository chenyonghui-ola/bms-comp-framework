<?php

namespace Imee\Service\Lesscode\Logic\Init;

use Imee\Models\Cms\CmsModules;
use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Schema\AdapterSchema;

class GuidExtendLogic
{
    /**
     * @var CmsModules
     */
    protected $moduleModel = CmsModules::class;

    /**
     * @var LesscodeMenu
     */
    protected $lesscodeMenu = LesscodeMenu::class;

    /**
     * @var LesscodeSchemaConfig
     */
    protected $lesscodeConfig = LesscodeSchemaConfig::class;

    /**
     * @var LesscodeSchemaPoint
     */
    protected $lesscodePoint = LesscodeSchemaPoint::class;

    /**
     * @var LesscodeSchemaPointConfig
     */
    protected $lesscodePointConfig = LesscodeSchemaPointConfig::class;


    public function handle()
    {
        $this->guidList();
    }

    /**
     * 功能管理扩展，增加创建/编辑功能
     */
    public function guidList()
    {
        // 检查是否存在菜单，不存在创建
        $list = $this->moduleModel::find([
            'conditions' => 'controller like :controller:',
            'bind' => ['controller' => '%/guidList']
        ])->toArray();

        if (empty($list)) {
            return;
        }

        $actions = array_column($list, null, 'action');
        $main    = $actions['main'] ?? [];

        if (empty($main)) {
            return;
        }

        $pointMap = [
            AdapterSchema::POINT_LIST   => '列表',
            AdapterSchema::POINT_CREATE => '创建',
            AdapterSchema::POINT_MODIFY => '编辑',
            AdapterSchema::POINT_DELETE => '删除',
            AdapterSchema::POINT_EXPORT => '导出',
        ];

        foreach (AdapterSchema::getInstance('guidList')->pointArr as $point)
        {
            if (isset($actions[$point])) {
                continue;
            }

            // 不存在创建菜单
            $info = new $this->moduleModel;
            $info->module_name = $pointMap[$point];
            $info->parent_module_id = $main['module_id'];
            $info->is_action = 1;
            $info->controller = 'lesscode/guidList';
            $info->action = $point;
            $info->m_type = 2;
            $info->dateline = time();
            $info->system_id = defined('SYSTEM_ID') ? SYSTEM_ID : 0;

            if (in_array($point, [AdapterSchema::POINT_DELETE, AdapterSchema::POINT_EXPORT])) {
                $info->deleted = 1;
            }

            $info->save();

            if ($info->module_id > 0) {
                $lesscodeMenu = new $this->lesscodeMenu;
                $lesscodeMenu->guid = 'guidList';
                $lesscodeMenu->menu_id = $info->module_id;
                $lesscodeMenu->save();
            }
        }

        // 更新配置
        $config = $this->lesscodeConfig::findFirstByGuid('guidList');
        $config->table_config = '{"fields":{"id":{"type":"int","length":10,"default":0,"unsigned":true,"comment":"id"},"title":{"type":"varchar","length":255,"default":"","comment":"标题"},"guid":{"type":"varchar","length":255,"default":"","comment":"Guid"},"model":{"type":"varchar","length":255,"default":"","comment":"Model"},"table_config":{"type":"varchar","length":255,"default":"","comment":"JSON配置"}},"pk":"id","comment":"功能管理"}';
        if ($config->getChangedFields()) {
            $config->save();
        }

        $listPoint = $this->lesscodePoint::getInfoByGuidAndType('guidList', AdapterSchema::POINT_LIST);
        if (!empty($listPoint)) {
            $listPointConfig = $this->lesscodePointConfig::findFirstByPointId($listPoint->id);
            $listPointConfig->config = '{"list":{"title":{"component":"Input"},"guid":{"component":"Input"},"model":{"component":"Input"},"table_config":{"component":"Input.TextArea","hidden":"1","specialchar":false}},"filter":[]}';
            if ($listPointConfig->getChangedFields()) {
                $listPointConfig->save();
            }
        }

        $createPoint = $this->lesscodePoint::getInfoByGuidAndType('guidList', AdapterSchema::POINT_CREATE);
        if (!empty($createPoint)) {
            $createPoint->logic = '\\Imee\\Service\\Lesscode\\Logic\\Schema\\GuidCreateLogic';
            if ($createPoint->getChangedFields()) {
                $createPoint->save();
            }
        }

        $modifyPoint = $this->lesscodePoint::getInfoByGuidAndType('guidList', AdapterSchema::POINT_MODIFY);
        if (!empty($modifyPoint)) {
            $modifyPoint->logic = '\\Imee\\Service\\Lesscode\\Logic\\Schema\\GuidModifyLogic';
            if ($modifyPoint->getChangedFields()) {
                $modifyPoint->save();
            }
            $modifyPointConfig = $this->lesscodePointConfig::findFirstByPointId($modifyPoint->id);
            $modifyPointConfig->config = '{"fields":{"guid":{"disabled":true}}}';
            if ($modifyPointConfig->getChangedFields()) {
                $modifyPointConfig->save();
            }
        }

    }
}