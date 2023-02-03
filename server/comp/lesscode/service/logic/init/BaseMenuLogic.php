<?php

namespace Imee\Service\Lesscode\Logic\Init;


use Imee\Models\Cms\CmsModules;
use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Service\Helper;

class BaseMenuLogic
{
    private $createUid = 1;

    private $list = [
        [
            'module_name'      => '低代码平台',
            'parent_module_id' => 0,
            'is_action'        => 0,
            'controller'       => '',
            'action'           => '',
            'icon'             => 'Thunderbolt',
            'm_type'           => 1,
            'deleted'          => 0,
            'children'         => [
                [
                    'module_name' => '表单设计器',
                    'is_action'   => 0,
                    'controller'  => 'lesscode/form',
                    'action'      => 'main',
                    'm_type'      => 2,
                    'deleted'     => 0,
                    'children'    => [
                        [
                            'module_name' => '表单设计器创建表单',
                            'is_action'   => 1,
                            'controller'  => 'lesscode/form',
                            'action'      => 'create',
                            'm_type'      => 2,
                            'deleted'     => 0,
                        ],
                        [
                            'module_name' => '表单设计器编辑表单',
                            'is_action'   => 1,
                            'controller'  => 'lesscode/form',
                            'action'      => 'update',
                            'm_type'      => 2,
                            'deleted'     => 0,
                        ],
                        [
                            'module_name' => '表单设计器检测是否合法',
                            'is_action'   => 1,
                            'controller'  => 'lesscode/form',
                            'action'      => 'check',
                            'm_type'      => 2,
                            'deleted'     => 0,
                        ]
                    ],
                ], [
                    'module_name'   => '功能管理',
                    'is_action'     => 0,
                    'controller'    => 'lesscode/guidList',
                    'action'        => 'main',
                    'm_type'        => 2,
                    'deleted'       => 0,
                    'lesscode_menu' => ['guid' => 'guidList'],
                    'children'      => [
                        [
                            'module_name'   => '列表',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidList',
                            'action'        => 'list',
                            'm_type'        => 2,
                            'deleted'       => 0,
                            'lesscode_menu' => ['guid' => 'guidList'],
                        ], [
                            'module_name'   => '创建',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidList',
                            'action'        => 'create',
                            'm_type'        => 2,
                            'deleted'       => 0,
                            'lesscode_menu' => ['guid' => 'guidList'],
                        ], [
                            'module_name'   => '编辑',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidList',
                            'action'        => 'modify',
                            'm_type'        => 2,
                            'deleted'       => 0,
                            'lesscode_menu' => ['guid' => 'guidList'],
                        ], [
                            'module_name'   => '删除',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidList',
                            'action'        => 'delete',
                            'm_type'        => 2,
                            'deleted'       => 1,
                            'lesscode_menu' => ['guid' => 'guidList'],
                        ], [
                            'module_name'   => '导出',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidList',
                            'action'        => 'export',
                            'm_type'        => 2,
                            'deleted'       => 1,
                            'lesscode_menu' => ['guid' => 'guidList'],
                        ],
                        [
                            'module_name'   => '功能点管理',
                            'is_action'     => 0,
                            'controller'    => 'lesscode/guidPointList',
                            'action'        => 'list',
                            'm_type'        => 2,
                            'deleted'       => 0,
                            'lesscode_menu' => ['guid' => 'guidPointList'],
                        ], [
                            'module_name'   => '列表',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidPointList',
                            'action'        => 'list',
                            'm_type'        => 2,
                            'deleted'       => 0,
                            'lesscode_menu' => ['guid' => 'guidPointList'],
                        ], [
                            'module_name'   => '添加',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidPointList',
                            'action'        => 'create',
                            'm_type'        => 2,
                            'deleted'       => 0,
                            'lesscode_menu' => ['guid' => 'guidPointList'],
                        ], [
                            'module_name'   => '编辑',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidPointList',
                            'action'        => 'modify',
                            'm_type'        => 2,
                            'deleted'       => 0,
                            'lesscode_menu' => ['guid' => 'guidPointList'],
                        ], [
                            'module_name'   => '删除',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidPointList',
                            'action'        => 'delete',
                            'm_type'        => 2,
                            'deleted'       => 1,
                            'lesscode_menu' => ['guid' => 'guidPointList'],
                        ], [
                            'module_name'   => '导出',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidPointList',
                            'action'        => 'export',
                            'm_type'        => 2,
                            'deleted'       => 1,
                            'lesscode_menu' => ['guid' => 'guidPointList'],
                        ], [
                            'module_name'   => '字段管理',
                            'is_action'     => 0,
                            'controller'    => 'lesscode/guidPointFields',
                            'action'        => 'main',
                            'm_type'        => 2,
                            'deleted'       => 0,
                            'lesscode_menu' => ['guid' => 'guidPointFields'],
                        ], [
                            'module_name'   => '列表',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidPointFields',
                            'action'        => 'list',
                            'm_type'        => 2,
                            'deleted'       => 0,
                            'lesscode_menu' => ['guid' => 'guidPointFields'],
                        ], [
                            'module_name'   => '添加',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidPointFields',
                            'action'        => 'create',
                            'm_type'        => 2,
                            'deleted'       => 1,
                            'lesscode_menu' => ['guid' => 'guidPointFields'],
                        ], [
                            'module_name'   => '编辑',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidPointFields',
                            'action'        => 'modify',
                            'm_type'        => 2,
                            'deleted'       => 0,
                            'lesscode_menu' => ['guid' => 'guidPointFields'],
                        ], [
                            'module_name'   => '删除',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidPointFields',
                            'action'        => 'delete',
                            'm_type'        => 2,
                            'deleted'       => 0,
                            'lesscode_menu' => ['guid' => 'guidPointFields'],
                        ], [
                            'module_name'   => '导出',
                            'is_action'     => 1,
                            'controller'    => 'lesscode/guidPointFields',
                            'action'        => 'export',
                            'm_type'        => 2,
                            'deleted'       => 1,
                            'lesscode_menu' => ['guid' => 'guidPointFields'],
                        ],
                    ],
                ]
            ],
        ]
    ];

    /**
     * @var CmsModules
     */
    protected $moduleModel = CmsModules::class;

    /**
     * @var LesscodeMenu
     */
    protected $lesscodeMenuModel = LesscodeMenu::class;

    /**
     * @var LesscodeSchemaConfig
     */
    protected $lesscodeSchemaConfigModel = LesscodeSchemaConfig::class;

    /**
     * @var LesscodeSchemaPoint
     */
    protected $lesscodeSchemaPointModel = LesscodeSchemaPoint::class;

    /**
     * @var LesscodeSchemaPointConfig
     */
    protected $lesscodeSchemaPointConfigModel = LesscodeSchemaPointConfig::class;

    protected $systemId;

    public function handle()
    {
        $this->systemId = defined('SYSTEM_ID') ? SYSTEM_ID : 0;

        foreach ($this->list as $item) {
            $info                   = new $this->moduleModel;
            $info->module_name      = $item['module_name'];
            $info->parent_module_id = $item['parent_module_id'];
            $info->is_action        = $item['is_action'];
            $info->controller       = $item['controller'];
            $info->action           = $item['action'];
            $info->m_type           = $item['m_type'];
            $info->icon             = $item['icon'] ?? '';
            $info->system_id        = $this->systemId;
            $info->deleted          = $item['deleted'] ?? 0;
            $info->is_delete        = $item['is_delete'] ?? 0;
            $info->save();

            if ($info->module_id > 0) {
                $this->insertChildren($item, $info->module_id);
            }
        }

        // 插入别的表数据
//        $this->insertLesscodeSchemaConfig();
//        $this->insertLesscodeSchemaPoint();
//        $this->insertLesscodeSchemaPointConfig();

        return [];
    }

    private function insertChildren($item, $pid)
    {
        if (!isset($item['children']) || empty($item['children'])) {
            return;
        }

        foreach ($item['children'] as $citem) {
            $info                   = new $this->moduleModel;
            $info->module_name      = $citem['module_name'];
            $info->parent_module_id = $pid;
            $info->is_action        = $citem['is_action'];
            $info->controller       = $citem['controller'];
            $info->action           = $citem['action'];
            $info->m_type           = $citem['m_type'];
            $info->icon             = $citem['icon'] ?? '';
            $info->system_id        = $this->systemId;
            $info->deleted          = $citem['deleted'] ?? 0;
            $info->is_delete        = $citem['is_delete'] ?? 0;
            $info->save();

            if ($info->module_id > 0) {

                // lesscode menu处理
                if (isset($citem['lesscode_menu']) && !empty($citem['lesscode_menu'])) {
                    $lesscodeMenu          = new $this->lesscodeMenuModel;
                    $lesscodeMenu->menu_id = $info->module_id;
                    $lesscodeMenu->guid    = $citem['lesscode_menu']['guid'];
                    $lesscodeMenu->save();
                }

                $this->insertChildren($citem, $info->module_id);
            }
        }
    }

    private function insertLesscodeSchemaConfig()
    {
        try {
            $tableName = (new $this->lesscodeSchemaConfigModel)->getSource();
            $sql       = <<<SQL
INSERT ignore INTO `{{tableName}}` (`id`, `schema_json`, `guid`, `model`, `table_config`)
VALUES
	(1, '{\"form\":{\"labelCol\":6,\"wrapperCol\":12},\"schema\":{\"type\":\"object\",\"properties\":{\"tldnh8p5fh6\":{\"type\":\"void\",\"x-component\":\"Card\",\"x-component-props\":{\"title\":\"\\\\u529f\\\\u80fd\\\\u7ba1\\\\u7406\"},\"x-designable-id\":\"tldnh8p5fh6\",\"x-index\":0,\"properties\":{\"title\":{\"type\":\"string\",\"title\":\"\\\\u6807\\\\u9898\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"title\",\"default\":\"\",\"x-designable-id\":\"ejedutxc5y6\",\"x-index\":0},\"guid\":{\"type\":\"string\",\"title\":\"Guid\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"guid\",\"default\":\"\",\"x-designable-id\":\"jz9gg8ega83\",\"x-index\":1},\"model\":{\"type\":\"string\",\"title\":\"Model\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"model\",\"x-designable-id\":\"zi0j9ohg163\",\"x-index\":2}}}},\"x-designable-id\":\"0rh69jtj8xz\"}}', 'guidList', '\\\\Imee\\\\Models\\\\Cms\\\\Lesscode\\\\LesscodeSchemaConfig', '{\"fields\":{\"id\":{\"type\":\"int\",\"length\":10,\"default\":0,\"unsigned\":true,\"comment\":\"id\"},\"title\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"标题\"},\"guid\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"Guid\"},\"model\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"Model\"}},\"pk\":\"id\",\"comment\":\"功能管理\"}'),
	(2, '{\"form\":{\"labelCol\":6,\"wrapperCol\":12},\"schema\":{\"type\":\"object\",\"properties\":{\"xrfcio2a0jv\":{\"type\":\"void\",\"x-component\":\"Card\",\"x-component-props\":{\"title\":\"\\\\u529f\\\\u80fd\\\\u70b9\\\\u7ba1\\\\u7406\"},\"x-designable-id\":\"xrfcio2a0jv\",\"x-index\":0,\"properties\":{\"guid\":{\"type\":\"string\",\"title\":\"Guid\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"guid\",\"x-designable-id\":\"mznzfvkemxd\",\"x-index\":0},\"title\":{\"type\":\"string\",\"title\":\"\\\\u529f\\\\u80fd\\\\u540d\\\\u79f0\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"title\",\"x-designable-id\":\"ta5zg6f7fos\",\"x-index\":1},\"type\":{\"type\":\"string\",\"title\":\"\\\\u64cd\\\\u4f5c\\\\u7c7b\\\\u578b\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"type\",\"x-designable-id\":\"9y12fno49l7\",\"x-index\":2},\"drive\":{\"title\":\"\\\\u9a71\\\\u52a8\\\\u7c7b\\\\u578b\",\"x-decorator\":\"FormItem\",\"x-component\":\"Select\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"drive\",\"x-designable-id\":\"3tlwq3301je\",\"x-index\":3},\"state\":{\"title\":\"\\\\u72b6\\\\u6001\",\"x-decorator\":\"FormItem\",\"x-component\":\"Select\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"state\",\"enum\":[{\"children\":[],\"label\":\"\\\\u5f00\\\\u542f\",\"value\":\"1\"},{\"children\":[],\"label\":\"\\\\u5173\\\\u95ed\",\"value\":\"0\"}],\"x-designable-id\":\"2qc2e890pas\",\"x-index\":4},\"is_system\":{\"title\":\"\\\\u662f\\\\u5426\\\\u7cfb\\\\u7edf\\\\u529f\\\\u80fd\",\"x-decorator\":\"FormItem\",\"x-component\":\"Select\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"is_system\",\"x-designable-id\":\"jnf4antwlej\",\"x-index\":5,\"enum\":[{\"children\":[],\"label\":\"\\\\u662f\",\"value\":\"1\"},{\"children\":[],\"label\":\"\\\\u5426\",\"value\":\"0\"}]},\"logic\":{\"type\":\"string\",\"title\":\"\\\\u94a9\\\\u5b50\\\\u6587\\\\u4ef6\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"logic\",\"x-designable-id\":\"g6axkm9hlnp\",\"x-index\":6},\"config\":{\"type\":\"string\",\"title\":\"\\\\u914d\\\\u7f6eJSON\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input.TextArea\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"config\",\"x-designable-id\":\"58gx3i5hxj6\",\"x-index\":7},\"create_time\":{\"type\":\"string\",\"title\":\"\\\\u521b\\\\u5efa\\\\u65f6\\\\u95f4\",\"x-decorator\":\"FormItem\",\"x-component\":\"DatePicker\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"create_time\",\"x-designable-id\":\"5kfdl7hme8b\",\"x-index\":8},\"update_time\":{\"type\":\"string\",\"title\":\"\\\\u66f4\\\\u65b0\\\\u65f6\\\\u95f4\",\"x-decorator\":\"FormItem\",\"x-component\":\"DatePicker\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"update_time\",\"x-designable-id\":\"po3rbh6285h\",\"x-index\":9}}}},\"x-designable-id\":\"qwkc925zyby\"}}', 'guidPointList', '\\\\Imee\\\\Models\\\\Cms\\\\Lesscode\\\\LesscodeSchemaPoint', '{\"fields\":{\"id\":{\"type\":\"int\",\"length\":10,\"default\":0,\"unsigned\":true,\"comment\":\"id\"},\"guid\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"Guid\"},\"title\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"功能名称\"},\"type\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"操作类型\"},\"drive\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"驱动类型\"},\"state\":{\"type\":\"tinyint\",\"length\":1,\"default\":\"\",\"enum\":[[\"开启\",\"1\"],[\"关闭\",\"0\"]],\"comment\":\"状态\"},\"is_system\":{\"type\":\"tinyint\",\"length\":1,\"default\":\"\",\"enum\":[[\"是\",\"1\"],[\"否\",\"0\"]],\"comment\":\"是否系统功能\"},\"logic\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"钩子文件\"},\"config\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"配置JSON\"},\"create_time\":{\"type\":\"int\",\"length\":10,\"default\":\"\",\"comment\":\"创建时间\"},\"update_time\":{\"type\":\"int\",\"length\":10,\"default\":\"\",\"comment\":\"更新时间\"}},\"pk\":\"id\",\"comment\":\"功能点管理\"}'),
	(3, '{\"form\":{\"labelCol\":6,\"wrapperCol\":12},\"schema\":{\"type\":\"object\",\"properties\":{\"yyx2ts62ir8\":{\"type\":\"void\",\"x-component\":\"Card\",\"x-component-props\":{\"title\":\"\\\\u5b57\\\\u6bb5\\\\u7ba1\\\\u7406\"},\"x-designable-id\":\"yyx2ts62ir8\",\"x-index\":0,\"properties\":{\"op_name\":{\"type\":\"string\",\"title\":\"\\\\u64cd\\\\u4f5c\\\\u7c7b\\\\u578b\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"op_name\",\"x-designable-id\":\"7drge7fi378\",\"x-index\":0},\"field_key\":{\"type\":\"string\",\"title\":\"\\\\u5b57\\\\u6bb5key\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"field_key\",\"x-designable-id\":\"zulol89yx18\",\"x-index\":1},\"field_name\":{\"type\":\"string\",\"title\":\"\\\\u5b57\\\\u6bb5\\\\u540d\\\\u79f0\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"field_name\",\"x-designable-id\":\"9pdwj73w78g\",\"x-index\":2},\"component\":{\"type\":\"string\",\"title\":\"\\\\u7ec4\\\\u4ef6\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"component\",\"x-designable-id\":\"5bs78s9arhh\",\"x-index\":3},\"enum\":{\"type\":\"string\",\"title\":\"\\\\u679a\\\\u4e3e\\\\u503c(Select\\\\u7ec4\\\\u4ef6)\",\"x-decorator\":\"FormItem\",\"x-component\":\"Input\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"enum\",\"x-designable-id\":\"kbjq81hqceu\",\"x-index\":4},\"is_hidden\":{\"title\":\"\\\\u662f\\\\u5426\\\\u663e\\\\u793a\",\"x-decorator\":\"FormItem\",\"x-component\":\"Select\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"is_hidden\",\"enum\":[{\"children\":[],\"label\":\"\\\\u662f\",\"value\":\"1\"},{\"children\":[],\"label\":\"\\\\u5426\",\"value\":\"0\"}],\"x-designable-id\":\"0hba5qpe874\",\"x-index\":5},\"is_sort\":{\"title\":\"\\\\u662f\\\\u5426\\\\u652f\\\\u6301\\\\u6392\\\\u5e8f(\\\\u5217\\\\u8868)\",\"x-decorator\":\"FormItem\",\"x-component\":\"Select\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"is_sort\",\"enum\":[{\"children\":[],\"label\":\"\\\\u662f\",\"value\":\"1\"},{\"children\":[],\"label\":\"\\\\u5426\",\"value\":\"0\"}],\"x-designable-id\":\"oc9jxyx124g\",\"x-index\":6},\"is_disabled\":{\"title\":\"\\\\u662f\\\\u5426\\\\u7981\\\\u7528(\\\\u8868\\\\u5355)\",\"x-decorator\":\"FormItem\",\"x-component\":\"Select\",\"x-validator\":[],\"x-component-props\":[],\"x-decorator-props\":[],\"name\":\"is_disabled\",\"enum\":[{\"children\":[],\"label\":\"\\\\u662f\",\"value\":\"1\"},{\"children\":[],\"label\":\"\\\\u5426\",\"value\":\"0\"}],\"x-designable-id\":\"sh7sqn2dki2\",\"x-index\":7}}}},\"x-designable-id\":\"50qkx4me35t\"}}', 'guidPointFields', '\\\\Imee\\\\Models\\\\Cms\\\\Lesscode\\\\LesscodeSchemaPoint', '{\"fields\":{\"id\":{\"type\":\"int\",\"length\":10,\"default\":0,\"unsigned\":true,\"comment\":\"id\"},\"op_name\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"操作类型\"},\"field_key\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"字段key\"},\"field_name\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"字段名称\"},\"component\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"组件\"},\"enum\":{\"type\":\"varchar\",\"length\":255,\"default\":\"\",\"comment\":\"枚举值(Select组件)\"},\"is_hidden\":{\"type\":\"tinyint\",\"length\":1,\"default\":\"\",\"enum\":[[\"是\",\"1\"],[\"否\",\"0\"]],\"comment\":\"是否隐藏\"},\"is_sort\":{\"type\":\"tinyint\",\"length\":1,\"default\":\"\",\"enum\":[[\"是\",\"1\"],[\"否\",\"0\"]],\"comment\":\"是否支持排序(列表)\"},\"is_disabled\":{\"type\":\"tinyint\",\"length\":1,\"default\":\"\",\"enum\":[[\"是\",\"1\"],[\"否\",\"0\"]],\"comment\":\"是否禁用(表单)\"}},\"pk\":\"id\",\"comment\":\"字段管理\"}');
SQL;
            $sql       = str_replace('{{tableName}}', $tableName, $sql);

            Helper::exec($sql, $this->lesscodeSchemaConfigModel::SCHEMA);
        } catch (\Exception $e) {
            Helper::console('insertLesscodeSchemaConfig err:' . $e->getMessage() . ', file:' . $e->getFile() . ':' . $e->getLine());
        }
    }

    private function insertLesscodeSchemaPoint()
    {
        try {
            $tableName = (new $this->lesscodeSchemaPointModel)->getSource();
            $sql       = <<<SQL
INSERT ignore INTO `{{tableName}}` (`id`, `guid`, `title`, `type`, `drive`, `state`, `is_system`, `logic`, `create_time`, `update_time`)
VALUES
	(1, 'guidList', '功能管理', 'list', 'mysql', 1, 1, '\\\\Imee\\\\Service\\\\Lesscode\\\\Logic\\\\Schema\\\\GuidListLogic', 1646899816, 0),
	(2, 'guidList', '创建', 'create', 'mysql', 1, 1, '', 1646899816, 0),
	(3, 'guidList', '编辑', 'modify', 'mysql', 1, 1, '', 1646899816, 0),
	(4, 'guidList', '删除', 'delete', 'mysql', 1, 1, '', 1646899816, 0),
	(5, 'guidList', '功能点列表', 'guid', 'mysql', 1, 1, '', 1646904025, 0),
	(6, 'guidPointList', '列表', 'list', 'mysql', 1, 1, '\\\\Imee\\\\Service\\\\Lesscode\\\\Logic\\\\Schema\\\\GuidPointListLogic', 1646904025, 0),
	(7, 'guidPointList', '创建', 'create', 'mysql', 1, 1, '\\\\Imee\\\\Service\\\\Lesscode\\\\Logic\\\\Schema\\\\GuidPointCreateLogic', 1646904025, 0),
	(8, 'guidPointList', '编辑', 'modify', 'mysql', 1, 1, '\\\\Imee\\\\Service\\\\Lesscode\\\\Logic\\\\Schema\\\\GuidPointModifyLogic', 1646904025, 0),
	(9, 'guidPointList', '删除', 'delete', 'mysql', 1, 1, '', 1646904025, 0),
	(10, 'guidPointList', '字段管理', 'guid', 'mysql', 1, 1, '', 0, 0),
	(11, 'guidPointFields', '列表', 'list', 'mysql', 1, 1, '\\\\Imee\\\\Service\\\\Lesscode\\\\Logic\\\\Schema\\\\GuidPointFieldsLogic', 1646908431, 0),
	(12, 'guidPointFields', '创建', 'create', 'mysql', 1, 1, '', 1646908431, 0),
	(13, 'guidPointFields', '编辑', 'modify', 'mysql', 1, 1, '\\\\Imee\\\\Service\\\\Lesscode\\\\Logic\\\\Schema\\\\GuidPointFieldsModifyLogic', 1646908431, 0),
	(14, 'guidPointFields', '删除', 'delete', 'mysql', 1, 1, '', 1646908431, 0),
	(15, 'guidPointList', '更新菜单', 'action', 'mysql', 1, 1, '', 1656667161, 0);

SQL;
            $sql       = str_replace('{{tableName}}', $tableName, $sql);

            Helper::exec($sql, $this->lesscodeSchemaPointModel::SCHEMA);
        } catch (\Exception $e) {
            Helper::console('insertLesscodeSchemaPoint err:' . $e->getMessage() . ', file:' . $e->getFile() . ':' . $e->getLine());
        }

    }

    private function insertLesscodeSchemaPointConfig()
    {
        try {
            $tableName = (new $this->lesscodeSchemaPointConfigModel)->getSource();
            $sql       = <<<SQL
INSERT ignore INTO `{{tableName}}` (`id`, `guid`, `point_id`, `config`, `create_time`, `update_time`)
VALUES
	(1, 'guidList', 1, '{\"list\":{\"title\":{\"component\":\"Input\"},\"guid\":{\"component\":\"Input\"},\"model\":{\"component\":\"Input\"}},\"filter\":{}}', 1646899816, 1651721659),
	(2, 'guidList', 2, '{}', 1646899816, 0),
	(3, 'guidList', 3, '{}', 1646899816, 0),
	(4, 'guidList', 4, '{}', 1646899816, 0),
	(5, 'guidList', 5, '{\"guid\":\"guidPointList\"}', 1646904025, 0),
	(6, 'guidPointList', 6, '{\"list\":{\"guid\":{\"component\":\"Input\"},\"title\":{\"component\":\"Input\"},\"type\":{\"component\":\"Input\"},\"drive\":{\"component\":\"Select\",\"func\":{\"service\":\"\\\\\\\\Imee\\\\\\\\Service\\\\\\\\Lesscode\\\\\\\\EnumService\",\"method\":\"getDriveMap\",\"params\":[null,\"label,value\"]},\"enum\":[]},\"state\":{\"component\":\"Select\",\"enum\":[[\"\\\\u5f00\\\\u542f\",\"1\"],[\"\\\\u5173\\\\u95ed\",\"0\"]]},\"is_system\":{\"component\":\"Select\",\"enum\":[[\"\\\\u662f\",\"1\"],[\"\\\\u5426\",\"0\"]]},\"logic\":{\"component\":\"Input\"},\"config\":{\"component\":\"Input.TextArea\", \"hidden\":true},\"create_time\":{\"component\":\"DatePicker\"},\"update_time\":{\"component\":\"DatePicker\"}},\"filter\":{},\"fields\":[]}', 1646904025, 1656337099),
	(7, 'guidPointList', 7, '{\"fields\":{\"create_time\":{\"disabled\":true,\"hidden\":true},\"update_time\":{\"disabled\":true,\"hidden\":true}}}', 1646904025, 0),
	(8, 'guidPointList', 8, '{\"fields\":{\"create_time\":{\"disabled\":true,\"hidden\":true},\"update_time\":{\"disabled\":true,\"hidden\":true},\"guid\":{\"disabled\":true}}}', 1646904025, 0),
	(9, 'guidPointList', 9, '{}', 1646904025, 0),
	(10, 'guidPointList', 10, '{\"guid\":\"guidPointFields\"}', 1646908431, 0),
	(11, 'guidPointFields', 11, '{\"list\":{\"op_name\":{\"component\":\"Input\"},\"field_key\":{\"component\":\"Input\"},\"field_name\":{\"component\":\"Input\"},\"component\":{\"component\":\"Input\"},\"enum\":{\"component\":\"Input\"},\"is_hidden\":{\"component\":\"Select\",\"enum\":[[\"\\\\u662f\",\"1\"],[\"\\\\u5426\",\"0\"]]},\"is_sort\":{\"component\":\"Select\",\"enum\":[[\"\\\\u662f\",\"1\"],[\"\\\\u5426\",\"0\"]]},\"is_disabled\":{\"component\":\"Select\",\"enum\":[[\"\\\\u662f\",\"1\"],[\"\\\\u5426\",\"0\"]]}},\"filter\":{},\"fields\":[]}', 1646908431, 1646910056),
	(12, 'guidPointFields', 12, '{}', 1646908431, 0),
	(13, 'guidPointFields', 13, '{\"fields\":{\"op_name\":{\"disabled\":true},\"field_key\":{\"disabled\":true}}}', 1646908431, 0),
	(14, 'guidPointFields', 14, '{}', 1646908431, 0),
	(15, 'guidPointList', 15, '{\"type\":\"modal\",\"modal\":true,\"url\":\"\\/lesscode\\/index\\/create\",\"hidden\":false,\"fields\":{\"guid\":{},\"type\":{\"default\":\"updateMenu\"},\"config\":{\"comment\":\"父级菜单\",\"component\":\"Select\",\"enum\":[],\"func\":{\"service\":\"\\\\\\\\Imee\\\\\\\\Service\\\\\\\\Lesscode\\\\\\\\StatusService\",\"method\":\"getMainMenuMap\",\"params\":[null,\"label,value\"]}}}}', 1656667161, 0);

SQL;
            $sql       = str_replace('{{tableName}}', $tableName, $sql);

            Helper::exec($sql, $this->lesscodeSchemaPointConfigModel::SCHEMA);
        } catch (\Exception $e) {
            Helper::console('insertLesscodeSchemaPointConfig err:' . $e->getMessage() . ', file:' . $e->getFile() . ':' . $e->getLine());
        }
    }
}