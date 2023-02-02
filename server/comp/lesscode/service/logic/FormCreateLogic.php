<?php


namespace Imee\Service\Lesscode\Logic;


use Imee\Helper\Traits\FactoryServiceTrait;

use Imee\Helper\Traits\ResponseTrait;
use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Context\FileBuildContext;
use Imee\Service\Lesscode\Context\GuidContext;
use Imee\Service\Lesscode\Context\Modules\CreateContext;
use Imee\Service\Lesscode\Data\SchemaMenuData;
use Imee\Service\Lesscode\Exception\FormException;
use Imee\Service\Lesscode\FactoryService;

use Imee\Service\Lesscode\Context\FormCreateContext;
use Imee\Service\Lesscode\Context\FormFieldContext;
use Imee\Service\Lesscode\Context\TemplateContext;

use Imee\Service\Lesscode\Data\SchemaConfigData;
use Imee\Service\Lesscode\FilterService;
use Imee\Service\Lesscode\HelperService;
use Imee\Service\Lesscode\MenuService;

use Imee\Service\Lesscode\Context\TableContext;
use Imee\Service\Lesscode\Schema\TableService;
use Phalcon\Mvc\Model;


/**
 * Class FormCreateLogic
 * @package Imee\Service\Lesscode\Logic
 * @property SchemaConfigData schemaConfigData
 * @property SchemaMenuData   schemaMenuData
 * @property MenuService      menuService
 * @property TableService     tableService
 */
class FormCreateLogic
{
    use FactoryServiceTrait, ResponseTrait;

    /**
     * 工厂映射
     */
    protected $factorys = [
        FactoryService::class
    ];

    /**
     * @var FormCreateContext
     */
    protected $context;

    /**
     * @var FileBuildContext
     */
    protected $fileBuildContext;

    /**
     * @var CreateContext
     */
    protected $menuCreateContext;

    /**
     * @var array 源数据
     */
    private $_schema;

    /**
     * @var string 源数据json
     */
    private $_schemaJson;

    protected $opType = 'create';

    public function __construct(FormCreateContext $context)
    {
        // 创建表单时guid转为全小写
        if ($this->opType === 'create') {
            $context->setParams([
                'guid' => strtolower($context->guid)
            ]);
        }

        $this->context          = $context;
        $this->fileBuildContext = new FileBuildContext(['schema_class' => new AdapterSchema()]);

        // 菜单
        $this->menuCreateContext = $this->menuService->init();
        $this->menuCreateContext->setParams([
            // 使用guid作为控制器名字
            'controller'       => $this->context->guid,
            'parent_module_id' => $this->context->parentId,
        ]);
    }

    /**
     * 生成必要文件
     */
    public function handle()
    {
        $this->common();

        // todo lesscode 针对生成的功能 生成菜单
        if ($this->menuService->checkCreate(new GuidContext(['guid' => $this->context->guid]))) {
            $this->menuService->create($this->menuCreateContext);
        }

        unset($contexts);

        return [];
    }

    protected function common()
    {
        // 简单验证一下
        $this->validation();

        // 解析schemaJson 返回字段
        $fields = $this->parseSchemaJson();

        // 生成schema field
        $this->createSchemaField($fields);

        // 各文件配置参数
        $maps = $this->getTemplateMap();

        $contexts = [];

        // 操作创建文件
        foreach ($maps as $type => $map) {
            // 公用一个 context 只有 model/schema 部分数据不需要覆盖
            if (isset($mapContext)) {
                $mapContext->setParams(array_merge($map['context'], ['op_type' => $this->opType]));
            } else {
                $mapContext = new TemplateContext(array_merge($map['context'], ['op_type' => $this->opType]));
            }

            $contexts[$type] = clone $mapContext;

            $templateLogic = new TemplateLogic($mapContext);

            call_user_func([$templateLogic, $map['action']]);
        }

        $lastContext = end($contexts);

        $this->context->setParams([
            'model_namespace'  => $lastContext->fileBuildContext->modelContext->namespace,
//            'schema_namespace' => $lastContext->fileBuildContext->schemaContext->namespace,
            'schema_class' => $lastContext->fileBuildContext->schemaClass,
        ]);

        // 保存 schema 数据
        $this->schemaConfigData->save($this->context);

        if (AdapterSchema::isMysqlDriveFunc()) {
            // 执行sql
            $this->tableService->create(new TableContext([
                'guid'            => $this->context->guid,
//                'schema'           => $lastContext->fileBuildContext->schemaContext->name,
//                'schema_namespace' => $lastContext->fileBuildContext->schemaContext->namespace,
                'model'           => $lastContext->fileBuildContext->modelContext->name,
                'model_namespace' => $lastContext->fileBuildContext->modelContext->namespace,
                'execSql'         => empty($this->context->model)
            ]));
        }
    }


    /**
     * 解析schema数据
     * @param $schemaJson
     * @return array
     */
    protected function parseSchemaJson()
    {
        $this->_schemaJson = $this->context->formilySchema;
        $this->_schema     = json_decode($this->context->formilySchema, true);

        if (empty($this->_schema)) {
            throw new FormException(FormException::SCHEMA_JSON_DATA_ERROR[1], FormException::SCHEMA_JSON_DATA_ERROR[0]);
        }

        // 存储所有后端需要关注的字段
        $fields = [];

        // 模块名称
        $moduleName = '';

        // 可能有多个布局卡片等，会有多个 property，后端只需要关注里面有多少字段
        foreach ($this->_schema['schema']['properties'] as &$property) {
            // 只需要关注里面的字段
            if (empty($property['properties'])) {
                continue;
            }

            foreach ($property['properties'] as $field => $fieldInfo) {
                $fieldInfo = $this->formatFieldKey($fieldInfo);

                if (!isset($fieldInfo['name']) || empty($fieldInfo['name'])) {
                    [$code, $msg] = FormException::SCHEMA_JSON_FIELD_NAME_EMPTY;
                    $msg = sprintf($msg, $fieldInfo['title']);
                    throw new FormException($msg, $code);
                }

                $component = $this->formatComponent($fieldInfo['x_component']);
                $logic     = $component . 'FieldLogic';

                if (is_null($this->{$logic})) {
                    [$code, $msg] = FormException::SCHEMA_JSON_DATA_FORMAT_ERROR;
                    $msg = sprintf($msg, $component);
                    throw new FormException($msg, $code);
                }

                // 获取卡片名称
                $moduleName              = $property['x-component-props']['title'] ?? ($property['title'] ?? '');
                $fieldInfo['card_title'] = $moduleName;

                // 解析筛选项
                $fieldInfo = $this->parseFilter($fieldInfo);

                // 解析主键
                $fieldInfo = $this->parseName($fieldInfo);

                // 1、schemaJson修改
//				$property['properties'][$field]['default'] = $fieldInfo['default'];

                $context  = new FormFieldContext($fieldInfo);
                $logicObj = get_class($this->{$logic});

                (new $logicObj)->handle($context);

                $fields[] = $context;

                if (isset($fieldInfo['x_component_props']) && is_array($fieldInfo['x_component_props']) && empty($fieldInfo['x_component_props'])) {
                    $property['properties'][$field]['x-component-props'] = new \stdClass();
                }

                if (isset($fieldInfo['x_decorator_props']) && is_array($fieldInfo['x_decorator_props']) && empty($fieldInfo['x_decorator_props'])) {
                    $property['properties'][$field]['x-decorator-props'] = new \stdClass();
                }
            }
        }

        if (empty($moduleName)) {
            [$code, $msg] = FormException::SCHEMA_MODULE_NAME_EMPTY;
            throw new FormException($msg, $code);
        }

        // 2、schemaJson修改
        $this->context->setParams([
            'formily_schema' => json_encode($this->_schema)
        ]);

        // 模块名称记录
        $this->menuCreateContext->setParams([
            'moduleName' => $moduleName
        ]);

        return $fields;
    }

    protected function parseFilter($fieldInfo)
    {
        // 暂时使用默认值做筛选策略
        $default = $fieldInfo['default'] ?? '';

        if (false !== stripos($default, '|')) {
            [$defaultReal, $filterField] = explode('|', $default);
        } elseif (false !== stripos($default, '｜')) {
            [$defaultReal, $filterField] = explode('｜', $default);
        } else {
            $defaultReal = $filterField = $default;
        }

        $filterFlag = ['筛选', 'filter'];

        if (in_array($defaultReal, $filterFlag)) {
            $defaultReal = '';
        }

        if (in_array($filterField, $filterFlag)) {
            $fieldInfo['is_filter_field'] = true;
        } else {
            $fieldInfo['is_filter_field'] = false;
        }

        $fieldInfo['default'] = $defaultReal;

        return $fieldInfo;
    }


    protected function parseName($fieldInfo)
    {
        $name = $fieldInfo['name'];

        if (false === stripos($name, '|')) {
            return $fieldInfo;
        }

        $nameArr = explode('|', $name);
        $name = $nameArr[0];

        // 解析是否是主键
        if (in_array('pk', $nameArr)) {
            $fieldInfo['is_pk'] = true;
        }

        // todo 扩展 解析别的属性


        $fieldInfo['name'] = $name;

        return $fieldInfo;
    }

    protected function formatComponent($component)
    {
        if (false !== strpos($component, '.')) {
            $component = str_replace('.', '', $component);
        }

        return ucfirst($component);
    }

    protected function formatFieldKey($data)
    {
        $rdata = [];

        foreach ($data as $key => $item) {
            if (false !== strpos($key, '-')) {
                $key = str_replace('-', '_', $key);
            }

            $rdata[$key] = $item;
        }

        return $rdata;
    }

    protected function createSchemaField($fields)
    {
        if (empty($fields)) {
            return;
        }

        $data  = $filter = [];
        $title = '';
        $pkField = '';

        foreach ($fields as $field) {
            $sysContext = $field->sysContext;

            $data[$sysContext->name] = [
                'type'      => $sysContext->type,
                'length'    => $sysContext->maxLength,
                'title'     => $sysContext->title,
                'default'   => $sysContext->default,
                'component' => $field->xComponent,     // 组件类型存储
            ];

            // 枚举
            if (!empty($sysContext->enum)) {
                $data[$sysContext->name]['enum'] = $sysContext->enum;
            }

            $title = $field->cardTitle;

            if ($field->isFilterField == true) {
                $filter[$sysContext->name] = ['x_component' => $field->xComponent];
            }

            if ($field->isPk == true) {
                $pkField = $field->name;
            }
        }

        $this->fileBuildContext->setParams([
            'table' => ['fields' => $data, 'title' => $title, 'pk' => $pkField],
            'list'  => [
                // 筛选
                'filter' => $this->getFileBuildListFilter($filter),

                // 列表字段 默认读取 table fields，处理关联关系使用
                'fields' => $this->getFileBuildListFields($filter),
            ]
        ]);
    }

    protected function getTemplateMap()
    {
        $maps = [
            // model
            'model'  => [
                'context' => [
                    'name'               => !empty($this->context->tableName) ? $this->context->tableName : $this->context->guid,
                    'prefix'             => GetNameLogic::MODEL_PREFIX,
                    'suffix'             => GetNameLogic::MODEL_SUFFIX,
                    'file_build_context' => $this->fileBuildContext,
                    'data'               => ['modelNamespace' => $this->context->model, 'tableName' => $this->context->tableName],
                ],
                'action'  => 'getModel',
            ],

            // schema
            'schema' => [
                'context' => [
                    'name'               => $this->context->guid,
                    'prefix'             => GetNameLogic::SCHEMA_PREFIX,
                    'suffix'             => GetNameLogic::SCHEMA_SUFFIX,
                    'file_build_context' => $this->fileBuildContext
                ],
                'action'  => 'getSchema'
            ],

            // todo lesscode 生成验证器
        ];

        return $maps;
    }

    protected function getFileBuildListFilter($filter)
    {
        if (empty($filter)) {
            return [];
        }

        $filterReal = [];

        foreach ($filter as $field => $item) {
            // 时间组件特殊处理
            if (HelperService::isTime($item['x_component'])) {
                $filterReal[] = [$field . '_' . FilterService::getDateSuffixStart(), 'egt'];
                $filterReal[] = [$field . '_' . FilterService::getDateSuffixEnd(), 'elt'];
            } else {
                $filterReal[] = [$field];
            }
        }

        return $filterReal;
    }

    protected function getFileBuildListFields($filter)
    {
        if (empty($filter)) {
            return [];
        }

        return [];
    }

    protected function validation()
    {
        if (!empty($this->context->model)) {
            if (!class_exists($this->context->model)) {
                [$code, $msg] = FormException::MODEL_FILE_NOT_EXIST_ERROR;
                throw new FormException(sprintf($msg, $this->context->model), $code);
            }

//            if (!(new $this->context->model) instanceof Model) {
//                [$code, $msg] = FormException::MODEL_FILE_INVALID_ERROR;
//                throw new FormException(sprintf($msg, $this->context->model), $code);
//            }
        }
    }
}