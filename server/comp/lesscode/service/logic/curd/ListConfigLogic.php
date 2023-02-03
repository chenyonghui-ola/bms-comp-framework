<?php


namespace Imee\Service\Lesscode\Logic\Curd;


use Imee\Helper\Traits\FactoryServiceTrait;

use Imee\Helper\Traits\ResponseInside;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Context\GuidContext;
use Imee\Service\Lesscode\Context\ListConfigContext;
use Imee\Service\Lesscode\FactoryService;
use Imee\Service\Lesscode\Data\SchemaConfigData;
use Imee\Service\Lesscode\FilterService;
use Imee\Service\Lesscode\HelperService;
use Imee\Service\Lesscode\MenuService;


/**
 * @property SchemaConfigData schemaConfigData
 * @property FilterService    FilterService
 */
class ListConfigLogic
{
    use FactoryServiceTrait, ResponseInside;

    /**
     * 工厂映射
     */
    protected $factorys = [
        FactoryService::class
    ];

    /**
     * @var ListConfigContext
     */
    protected $context;

    /**
     * @var AdapterSchema
     */
    protected $schema;

    protected $table;

    protected $filter;

    protected $point = [];

    protected $purviewPoint = [];

    protected $operate = [];

    protected $extra = [];

    protected $action = [];

    private $menuService;

    private $listFilterCache = false;

    private $listPatch = [];

    /**
     * @var LesscodeSchemaPoint
     */
    private $lesscodeSchemaPoint = LesscodeSchemaPoint::class;

    public function __construct(ListConfigContext $context)
    {
        $this->context = $context;

        $this->menuService = new MenuService();
    }

    /**
     * 列表配置
     */
    public function handle()
    {
//        $info = $this->schemaConfigData->getInfoByGuid($this->context);
//
//        if (empty($info)) {
//            return self::error('数据异常');
//        }

        $this->schema = new AdapterSchema($this->context->guid);

        $this->table = $this->schema->getTable();
        if (!empty($this->table)) {
            $this->formatTable();
        }

        $this->filter = $this->schema->getListFilter();
        if (!empty($this->filter)) {
            $this->formatFilter();
        }

        // 获取现有菜单
        $this->getPoint();

        // 获取额外的一些操作
        $this->operate = $this->schema->getListOperate();
        if (!empty($this->operate)) {
            $this->formatOperate();
        }

        // 获取额外的一些操作
        $this->extra = $this->schema->getListExtra();
        if (!empty($this->extra)) {
            $this->formatExtra();
        }

        // 增加跟创建按钮同级操作 支持批量等操作
        $this->action = $this->schema->getListAction();
        if (!empty($this->action)) {
            $this->formatAction();
        }

        $res = [
            'list'    => $this->table,
            'filter'  => $this->filter,
            'point'   => $this->point,
            'operate' => $this->operate,
            'extra'   => $this->extra,
            'action'  => $this->action,
        ];

        if ($this->listFilterCache === true) {
            $res['cache'] = $this->listFilterCache;
        }

        if (!empty($this->listPatch)) {
            $res['patch'] = $this->listPatch;
        }

        return self::success($res);
    }


    /**
     * 格式化筛选
     * @param $filter
     */
    public function formatTable(): void
    {
        $list = [
            'title'  => $this->table['comment'] ?? '',
            'pk'     => $this->table['pk'] ?? '',
            'fields' => [],
        ];

        $class = new \stdClass();

        foreach ($this->table['fields'] as $name => $field) {

            $listConfig = $this->schema->getListConfig();

            $form = [];

            if (!empty($this->schema->getCreate()) && !empty($this->schema->getCreateFields())) {
                $tmpFields                         = $this->schema->getCreateFields();
                // 处理组件大小写问题
                $tmpField = [];
                if (isset($tmpFields[$name])) {
                    $tmpField = $tmpFields[$name];
                    if (isset($tmpField['component']) && !empty($tmpField['component'])) {
                        $tmpField['component'] = strtolower($this->formatComponent($tmpField['component']));
                    }
                }
                $form[$this->schema::POINT_CREATE] = $tmpField;
            }

            if (!empty($this->schema->getModify()) && !empty($this->schema->getModifyFields())) {
                $tmpFields                         = $this->schema->getModifyFields();
                // 处理组件大小写问题
                $tmpField = [];
                if (isset($tmpFields[$name])) {
                    $tmpField = $tmpFields[$name];
                    if (isset($tmpField['component']) && !empty($tmpField['component'])) {
                        $tmpField['component'] = strtolower($this->formatComponent($tmpField['component']));
                    }
                }
                $form[$this->schema::POINT_MODIFY] = $tmpField;
            }

            $tmp = [
                'name'      => $name,
                'default'   => $field['default'],
                'comment'   => $field['comment'],
                'component' => isset($listConfig[$name]['component']) && !empty($listConfig[$name]['component']) ? strtolower($this->formatComponent($listConfig[$name]['component'])) : '',
                'hidden'    => isset($listConfig[$name]['hidden']) ? $listConfig[$name]['hidden'] == true : false,
                'form'      => !empty($form) ? $form : $class,
                'sort'      => $listConfig[$name]['sort'] ?? false,
                'dataType'  => $listConfig[$name]['dataType'] ?? '',
            ];

            // 增加是否验证数据是否为空
            if (isset($listConfig[$name]['required'])) {
                $tmp['required'] = $listConfig[$name]['required'];
            }

            // 增加字段格式
            if (isset($listConfig[$name]['style'])) {
                $tmp['style'] = $listConfig[$name]['style'];
            }

            // 增加字段格式
            if (isset($listConfig[$name]['flag'])) {
                $tmp['flag'] = $listConfig[$name]['flag'];
            }

            // 增加url地址
            if (isset($listConfig[$name]['url'])) {
                $tmp['url'] = $listConfig[$name]['url'];
            }

            // operate/action 支持必填选项
            // 支持 placeholder
            $extraOp = ['required', 'placeholder'];

            foreach ($extraOp as $op)
            {
                if (isset($listConfig[$name][$op])) {
                    $tmp[$op] = $listConfig[$name][$op];
                }
            }

            if (isset($listConfig[$name]['enum']) || (isset($listConfig[$name]['component']) && HelperService::isEnum($listConfig[$name]['component']))) {

                if (isset($listConfig[$name]['func']) && !empty($listConfig[$name]['func'])) {

                    $service = $listConfig[$name]['func']['service'];
                    $method  = $listConfig[$name]['func']['method'];
                    $params  = $listConfig[$name]['func']['params'] ?? [];
                    if (method_exists($service, $method)) {
                        $refiection = new \ReflectionMethod($service, $method);
                        if ($refiection->isStatic()) {
                            $tmpEnum = !empty($params) ? call_user_func([$service, $method], ...$params) : call_user_func([$service, $method]);
                        } else {
                            $tmpEnum = !empty($params) ? call_user_func([new $service, $method], ...$params) : call_user_func([new $service, $method]);
                        }
                        $tmp['enum'] = !empty($tmpEnum) ? $tmpEnum : [];
                    }
                } else {
                    $tmp['enum'] = $this->formatEnum($listConfig[$name]['enum'] ?? []);
                }

                $tmp['enum'] = $this->formatEnumStr($tmp['enum'] ?? []);
            }

            $list['fields'][] = $tmp;
        }

        // 列表是否支持多选
        $list['multiple'] = $this->schema->getListMultiple();

        // 列表是否支持缓存
        $listFilterCache = $this->schema->getListFilterCache();
        if ($listFilterCache === true) {
            $this->listFilterCache = true;
            $list['filterCache']   = true;
        }

        // 列表是否支持补丁挂件
        $listPatch = $this->schema->getListPatch();
        if (!empty($listPatch)) {
            $this->listPatch = $listPatch;
        }

        $this->table = $list;
    }

    /**
     * 格式化筛选
     * @param $filter
     */
    public function formatFilter(): void
    {
        $filter = [];

        $fields = $this->table['fields'];
        $fields = array_column($fields, null, 'name');

        foreach ($this->filter as $fieldName => $item) {

            $component = $default = '';

            if (isset($item['component'])) {
                $component = $item['component'];
            }

            if (isset($item['default'])) {
                $default = $item['default'];
            }

            if (isset($fields[$fieldName])) {
                $filter[$fieldName] = [
                    'label'     => $fields[$fieldName]['comment'],
                    'name'      => $fieldName,
                    'default'   => $default,
                    'component' => !empty($component) ? strtolower($component) : (isset($fields[$fieldName]['component']) ? strtolower($this->formatComponent($fields[$fieldName]['component'])) : ''),
                ];

                if (isset($item['enum']) || (isset($item['component']) && HelperService::isEnum($item['component']))) {

                    if (isset($item['func']) && !empty($item['func'])) {
                        $service = $item['func']['service'];
                        $method  = $item['func']['method'];
                        $params  = $item['func']['params'] ?? [];

                        if (method_exists($service, $method)) {
                            $refiection = new \ReflectionMethod($service, $method);
                            if ($refiection->isStatic()) {
                                $tmpEnum = call_user_func_array([$service, $method], $params);
                            } else {
                                $tmpEnum = call_user_func_array([new $service, $method], $params);
                            }

                            $filter[$fieldName]['enum'] = !empty($tmpEnum) ? $tmpEnum : [];
                        }
                    } elseif (isset($fields[$fieldName]['enum']) || (isset($fields[$fieldName]['component']) && HelperService::isEnum($fields[$fieldName]['component']))) {
                        if (isset($fields[$fieldName]['func']) && !empty($fields[$fieldName]['func'])) {
                            $service = $fields[$fieldName]['func']['service'];
                            $method  = $fields[$fieldName]['func']['method'];
                            $params  = $fields[$fieldName]['func']['params'] ?? [];

                            if (method_exists($service, $method)) {
                                $refiection = new \ReflectionMethod($service, $method);
                                if ($refiection->isStatic()) {
                                    $tmpEnum = call_user_func_array([$service, $method], $params);
                                } else {
                                    $tmpEnum = call_user_func_array([new $service, $method], $params);
                                }

                                $filter[$fieldName]['enum'] = !empty($tmpEnum) ? $tmpEnum : [];
                            }
                        } else {
                            $filter[$fieldName]['enum'] = $this->formatEnum($fields[$fieldName]['enum'] ?? []);
                        }
                    } else {
                        $filter[$fieldName]['enum'] = $this->formatEnum($item['enum'] ?? []);
                    }
                } elseif (isset($fields[$fieldName]['enum']) || (isset($fields[$fieldName]['component']) && HelperService::isEnum($fields[$fieldName]['component']))) {
                    if (isset($fields[$fieldName]['func']) && !empty($fields[$fieldName]['func'])) {
                        $service = $fields[$fieldName]['func']['service'];
                        $method  = $fields[$fieldName]['func']['method'];
                        $params  = $fields[$fieldName]['func']['params'] ?? [];

                        if (method_exists($service, $method)) {
                            $refiection = new \ReflectionMethod($service, $method);
                            if ($refiection->isStatic()) {
                                $tmpEnum = call_user_func_array([$service, $method], $params);
                            } else {
                                $tmpEnum = call_user_func_array([new $service, $method], $params);
                            }

                            $filter[$fieldName]['enum'] = !empty($tmpEnum) ? $tmpEnum : [];
                        }
                    } else {
                        $filter[$fieldName]['enum'] = $this->formatEnum($fields[$fieldName]['enum'] ?? []);
                    }
                }

                $filter[$fieldName]['enum'] = $this->formatEnumStr($filter[$fieldName]['enum'] ?? []);
            }

            if (FilterService::isDateTimeField($fieldName)) {
                $fieldNameRef = FilterService::getDateTimeField($fieldName);

                if (isset($filter[$fieldNameRef])) {
                    $filter[$fieldNameRef]['name'][] = $fieldName;
                } else {
                    $filter[$fieldNameRef] = [
                        'label'     => $fields[$fieldNameRef]['comment'],
                        'name'      => [$fieldName],
                        'default'   => $default,
                        'component' => isset($fields[$fieldNameRef]['component']) ? strtolower($this->formatComponent($fields[$fieldNameRef]['component'])) : '',
                    ];
                }
            }
        }

        $this->filter = array_values($filter);
    }

    /**
     * 前端支持的类型
     * @param $value
     * @return mixed|string
     */
    public function formatComponent($value)
    {
        $lowVal = strtolower($value);

        $map = [
            'radio.group'    => 'radio',
            'numberpicker'   => 'input',
            'input.textarea' => 'textarea',
            'checkbox.group' => 'checkbox',
        ];

        return isset($map[$lowVal]) ? $map[$lowVal] : $value;
    }

    /**
     * 格式化enum
     * @param $value
     * @return mixed|string
     */
    public function formatEnum($data)
    {
        $res = [];

        if (empty($data)) {
            return $res;
        }

        foreach ($data as $item) {
            [$label, $value] = array_values($item);

            $res[] = ['label' => $label, 'value' => strval($value)];
        }

        return $res;
    }

    /**
     * 格式化enum
     * @param $value
     * @return mixed|string
     */
    private function formatEnumStr($data)
    {
        $res = [];

        if (empty($data)) {
            return $res;
        }

        foreach ($data as &$item)
        {
            if (isset($item['value'])) {
                $item['value'] = strval($item['value']);
            }
        }

        return $data;
    }

    protected function getPoint()
    {
        $menuList = $this->menuService->getListById(new GuidContext(['guid' => $this->context->guid]));

        if (empty($menuList)) {
            return;
        }

        $ignoreOp = [
            $this->schema::POINT_CREATE,
            $this->schema::POINT_MODIFY,
            $this->schema::POINT_DELETE,
        ];

        foreach ($menuList as $item) {
            if ($item['is_action'] == 0) {
                continue;
            }

            $checkPurview = $item['controller'] . '.' . $item['action'];

            if (in_array($checkPurview, $this->context->purview)) {
                // 拥有权限的操作
                $this->purviewPoint[] = $item['action'];
                if (in_array($item['action'], $ignoreOp)) {
                    continue;
                }
                $this->point[] = $item['action'];
            }
        }

        // 只能测试服使用 增加字段功能
        ENV == 'dev' && $this->point[] = 'add_field';
    }

    /**
     * 获取操作按钮
     * title 标题
     * path 页面路径 （低代码生成的页面可不需要）
     * icon 图标，不传默认是个详情按钮
     * modal 是否弹窗 todo
     * guid 低代码标识（只有低代码生成的页面才需要）
     */
    protected function formatOperate()
    {
        if (empty($this->operate)) {
            $this->operate = [];
            return;
        }

        $this->operate = $this->formatOpActionHandle($this->operate);
    }

    /**
     * 获取创建按钮同级的操作按钮
     * title 标题
     * path 页面路径 （低代码生成的页面可不需要）
     * icon 图标，不传默认是个详情按钮
     * modal 是否弹窗
     * guid 低代码标识（只有低代码生成的页面才需要）
     */
    protected function formatAction()
    {
        if (empty($this->action)) {
            $this->action = [];
            return;
        }

        $this->action = $this->formatOpActionHandle($this->action);
    }

    private function formatOpActionHandle($operateData)
    {
        $tmpOperate = [];

        $tmp = [
            'icon'  => '',
            'modal' => false,
        ];

        foreach ($operateData as $operate) {
            $currentOperate = [];

            // 禁用的不显示
            if ($operate['state'] == $this->lesscodeSchemaPoint::STATE_CLOSE) {
                continue;
            }

            if ($operate['type'] == 'guid') {
                $currentOperate          = $operate;
                $tmpSchema               = new AdapterSchema($operate['guid']);
                $currentOperate['type']  = $operate['type'];
                $currentOperate['title'] = $tmpSchema->getTableTitle();

                // 先写死，这里最好是查询module表
                $currentOperate['guid'] = $operate['guid'];

                $menuList = $this->menuService->getListById(new GuidContext(['guid' => $operate['guid']]));

                if (!empty($menuList)) {
                    foreach ($menuList as $item) {
                        // icon
                        if (!empty($item['icon'])) {
                            $currentOperate['icon'] = $item['icon'];
                            break;
                        }
                    }
                }
            } elseif ($operate['type'] == 'modal') {  // 弹窗
                // 解析弹窗里的字段
                if (isset($operate['fields'])) {
                    $operateFields = (array) $operate['fields'];
                    unset($operate['fields']);
                } else {
                    $operateFields = [];
                }
                $currentOperate = $operate;
                $rewriteFields  = ['comment', 'component', 'writeback', 'default'];


                foreach ($operateFields as $tmpOpField => $tmpVal) {
                    // 获取字段信息
                    $currentTmp = $this->findTableField($tmpOpField);

                    // 出现这种情况肯定是配置错误，配置的字段本身schema不存在，还是包容一点 - -
                    if (empty($currentTmp)) {
                        continue;
                    }

                    if (isset($tmpVal['enum']) || (isset($tmpVal['component']) && HelperService::isEnum($tmpVal['component']))) {
                        if (isset($tmpVal['func']) && !empty($tmpVal['func'])) {
                            $service = $tmpVal['func']['service'];
                            $method  = $tmpVal['func']['method'];
                            $params  = $tmpVal['func']['params'] ?? [];

                            if (method_exists($service, $method)) {
                                $refiection = new \ReflectionMethod($service, $method);
                                if ($refiection->isStatic()) {
                                    $tmpEnum = call_user_func_array([$service, $method], $params);
                                } else {
                                    $tmpEnum = call_user_func_array([new $service, $method], $params);
                                }
                                $currentTmp['enum'] = !empty($tmpEnum) ? $tmpEnum : [];
                            }
                        } else {
                            $currentTmp['enum'] = $this->formatEnum($tmpVal['enum'] ?? []);
                        }

                        $currentTmp['enum'] = $this->formatEnumStr($currentTmp['enum'] ?? []);
                    }

                    foreach ($rewriteFields as $rewriteField) {
                        if (isset($tmpVal[$rewriteField])) {
                            $currentTmp[$rewriteField] = $tmpVal[$rewriteField];
                        }

                        if ($rewriteField == 'component') {
                            $currentTmp[$rewriteField] = strtolower($currentTmp[$rewriteField]);
                        }
                    }

                    // operate/action 支持必填选项
                    // 支持 placeholder
                    $extraOp = ['required', 'placeholder'];

                    foreach ($extraOp as $op)
                    {
                        if (isset($tmpVal[$op])) {
                            $currentTmp[$op] = $tmpVal[$op];
                        }
                    }

                    // 处理隐藏禁用等属性
                    $currentTmp['hidden']   = $tmpVal['hidden'] ?? false;
                    $currentTmp['disabled'] = $tmpVal['disabled'] ?? false;

                    $currentOperate['fields'][] = $currentTmp;

                    unset($currentTmp, $tmpVal);
                }

            } elseif ($operate['type'] == 'need_confirm') { // 确认框
                $currentOperate = $operate;
            } elseif ($operate['type'] == 'url') {  // 跳转url
                $currentOperate = $operate;
            } elseif ($operate['type'] == 'manMadeModal') {
                // 解析弹窗里的字段
                if (isset($operate['fields'])) {
                    $operateFields = (array) $operate['fields'];
                    unset($operate['fields']);
                } else {
                    $operateFields = [];
                }
                $currentOperate = $operate;

                foreach ($operateFields as $tmpOpField => $tmpVal) {
                    // 获取字段信息
                    $currentTmp = $this->findTableField($tmpOpField);

                    // 出现这种情况肯定是配置错误，配置的字段本身schema不存在，还是包容一点 - -
                    if (empty($currentTmp)) {
                        continue;
                    }
                    unset($currentTmp['form'], $currentTmp['hidden']);
                    if (isset($tmpVal['enum']) || (isset($tmpVal['component']) && HelperService::isEnum($tmpVal['component']))) {
                        if (isset($tmpVal['func']) && !empty($tmpVal['func'])) {
                            $service = $tmpVal['func']['service'];
                            $method  = $tmpVal['func']['method'];
                            $params  = $tmpVal['func']['params'] ?? [];

                            if (method_exists($service, $method)) {
                                $refiection = new \ReflectionMethod($service, $method);
                                if ($refiection->isStatic()) {
                                    $tmpEnum = call_user_func_array([$service, $method], $params);
                                } else {
                                    $tmpEnum = call_user_func_array([new $service, $method], $params);
                                }
                                $currentTmp['enum'] = !empty($tmpEnum) ? $tmpEnum : [];
                            }
                        } else {
                            $currentTmp['enum'] = $this->formatEnum($tmpVal['enum'] ?? []);
                        }

                        $currentTmp['enum'] = $this->formatEnumStr($currentTmp['enum'] ?? []);
                    }

                    $currentOperate['fields'][] = $currentTmp;

                    unset($currentTmp, $tmpVal);
                }
            } else {
                // 判断权限
                if (in_array($operate['type'], [$this->schema::POINT_CREATE, $this->schema::POINT_MODIFY, $this->schema::POINT_DELETE, $this->schema::POINT_EXPORT])) {
                    if (!in_array($operate['type'], $this->purviewPoint)) {
                        continue;
                    }

                    if (isset($operate['fields'])) {
                        unset($operate['fields']);
                    }
                }
                $currentOperate = $operate;
            }

            $tmpOperate[] = array_merge($tmp, $currentOperate);
        }

        return $tmpOperate;
    }

    protected function formatExtra()
    {
        if (empty($this->extra)) {
            $this->extra = [];
            return;
        }
    }

    /**
     * @param $field
     * @return array|mixed
     */
    private function findTableField($field): array
    {
        if (empty($this->table['fields'])) {
            return [];
        }

        foreach ($this->table['fields'] as $tmpField) {
            if ($tmpField['name'] == $field) {
                return $tmpField;
            }
        }

        return [];
    }
}