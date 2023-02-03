<?php

namespace Imee\Service\Lesscode\Logic;

use Imee\Models\Cms\Lesscode\BaseModel;
use Imee\Models\Cms\Lesscode\BmsBaseModel;
use Imee\Models\Cms\Lesscode\XsstBaseModel;
use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Context\FileCreateContext;
use Imee\Service\Lesscode\Context\ModelDiffContext;
use Imee\Service\Lesscode\Context\Schema\SchemaContext;
use Imee\Service\Lesscode\Context\Schema\SchemaDiffContext;
use Imee\Service\Lesscode\Exception\FormException;
use Imee\Service\Lesscode\Context\ModelContext;
use Imee\Service\Lesscode\Context\TemplateContext;
use Imee\Service\Lesscode\FileService;
use Imee\Service\Lesscode\Schema\FieldService;

class TemplateLogic
{
    /**
     * @var TemplateContext
     */
    private $context;

    /**
     * @var string 类型 model,schema
     */
    private $type;

    /**
     * @var string 路径
     */
    private $path;

    /**
     * @var string 文件名
     */
    private $name;

    /**
     * @var string 命名空间
     */
    private $namespace;

    /**
     * @var string 文件后缀
     */
    private $ext = '.php';

    /**
     * @var string 文件内容
     */
    private $fileContent;

    /**
     * @var array 变动后的数据
     */
    private $afterData;

    public function __construct(TemplateContext $context)
    {
        $this->context = $context;
    }

    /**
     * 生成model文件
     */
    public function getModel()
    {
        $this->type = 'model';

        if ($this->context->opType == 'update') {
            $schema = new AdapterSchema($this->context->name);
            $this->namespace = $schema->model;
            $namespaceArr = explode('\\', $this->namespace);
            $this->name = end($namespaceArr);
        } else {
            $data = $this->context->data;
            $modelParam = $data['modelNamespace'] ?? '';
            $tableNameParam = $data['tableName'] ?? '';
            $modelName = '';

            if (false !== strpos($this->context->name, '.')) {
                [$schemaLink, $name] = explode('.', $this->context->name);
            } else {
                $schemaLink = 'cms';
                $name = $this->context->name;
            }

            $this->path      = '/models/cms/auto/';
            $this->name      = GetNameLogic::getModel($name, $this->context->prefix, $this->context->suffix);
            $this->namespace =  !empty($modelParam) ? '\\' . ltrim($modelParam, '\\') : '\\Imee\\Models\\Cms\\Auto\\' . $this->name;

            if (empty($modelParam)) {
                [$modelExtendClass, $modelExtendName, $modelName] = $this->getModelExtend($tableNameParam);

                // 默认都使用 bms
                $str = <<<Model
<?php

namespace Imee\Models\Cms\Auto;

use %s;

class %s extends %s
{

}
Model;

                $this->fileContent = sprintf($str, $modelExtendClass, (!empty($modelName) ? $modelName : $this->name), $modelExtendName);

                $this->createFile();
            }
        }

        // 设置参数
        $this->context->fileBuildContext->setParams([
            'modelContext' => new ModelContext([
                'name'      => $this->name,
                'namespace' => $this->namespace,
            ])
        ]);
    }

    public function getSchema()
    {
        $fields = $this->formatTableConfig();
        $lists  = $this->formatListConfig();
        $filter = $this->formatListFilterConfig();
        $pkField = $this->formatPkConfig();

        $table = [
//            'fields'  => array_merge(FieldService::getPkField(), $fields, FieldService::getAttachFields()),
            'fields'  => !empty($pkField) ? $fields : array_merge(FieldService::getPkField(), $fields),
            'pk'      => $pkField ? $pkField : 'id',
            'comment' => $this->context->fileBuildContext->table['title'],
        ];

        $this->context->fileBuildContext->schemaClass->table = $table;
        $this->context->fileBuildContext->schemaClass->list = $lists;
        $this->context->fileBuildContext->schemaClass->listFilter = $filter;

        // TODO listFields
//        print_r($this->context->fileBuildContext->schemaClass);exit;

        // TODO lesscode schema 在更新的情况下 还需要比对上次数据 得出一个 SchemaDiffContext 用于表数据变更
    }

    /**
     * todo lesscode 生成验证文件
     */
    public function getValidation()
    {

    }

    private function createFile()
    {
        $fileName = $this->name . $this->ext;
        $path     = APP_PATH . $this->path;
        $file     = $path . $fileName;

        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }

        // todo lesscode 验证文件存在 可能需要对比文件 生成表修改sql等

        $before = $after = '';

        // 直接覆盖 不验证文件是否存在了
        if (file_exists($file)) {
            if (class_exists($this->namespace)) {
                false === AdapterSchema::isApiDriveFunc() && $before = new $this->namespace;
            } else {
                [$code, $msg] = FormException::FILE_EXISTS_CLASS_NO_EXISTS;
                $msg = sprintf($msg, $this->name);
                throw new FormException($msg, $code);
            }

            $fileExist = true;
        }

        if (empty(trim($this->fileContent))) {

            [$code, $msg] = FormException::EMPTY_FILE_ERROR;

            throw new FormException($msg, $code);
        }

        file_put_contents($file, $this->fileContent);

        $fileService = new FileService();

        if ($this->type == 'model') {
            // 只是记录，暂时没用上
            // 判断是否首次创建 如果首次创建 这个after则需要在创建表后才能 new
            false === AdapterSchema::isApiDriveFunc() && isset($fileExist) && $after = new $this->namespace;

            $diffContext = new ModelDiffContext([
                'namespace'    => $this->namespace,
                'before_class' => $before,
                'after_class'  => $after,
            ]);
        }

        if ($this->type == 'schema') {
            /**
             * @see 已废弃，已经不在生成文件，使用 AdapterSchema 处理
             */
            $after = new $this->namespace;

            // 重写after数据，因为不能重载文件，导致重写文件后重新new不生效
            foreach ($this->afterData as $type => $value) {
                $after->{$type} = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
            }

            $diffContext = new SchemaDiffContext([
                'namespace'    => $this->namespace,
                'before_class' => $before,
                'after_class'  => $after,
            ]);
        }

        isset($diffContext) && $fileService->setDiff($diffContext);

        // 生成文件后记录生成文件的路径
        $fileService->createRecord(new FileCreateContext([
            'add_file_type' => $this->type,
            'add_file_path' => $file,
        ]));
    }

    /**
     * 字段转化成 schema 里定义的fields字段名
     * @param $fields
     * @return array
     */
    private function formatSchemaFields()
    {
        $fields = [];

        foreach ((array) $this->context->fileBuildContext->table['fields'] as $k => $field) {
            $fields[$k]            = $field;
            $fields[$k]['comment'] = $field['title'];
            unset($fields[$k]['title']);
        }

        return $fields;
    }

    private function formatTableConfig()
    {
        $fields = [];

        foreach ((array) $this->context->fileBuildContext->table['fields'] as $k => $field) {
            $fields[$k]            = $field;
            $fields[$k]['comment'] = $field['title'];
            unset($fields[$k]['title'], $fields[$k]['component']);
        }

        return $fields;
    }

    private function formatListConfig()
    {
        $fields = [];

        foreach ((array) $this->context->fileBuildContext->table['fields'] as $k => $field) {
            $fields[$k]['component'] = $field['component'];

            if (isset($field['enum'])) {
                $fields[$k]['enum'] = $field['enum'];
            }
        }

        return $fields;
    }

    private function formatListFilterConfig()
    {
        $fields = [];

        foreach ((array) $this->context->fileBuildContext->list['filter'] as $k => $field) {
            $tmpVal = [];

            if (count($field) == 1) {
                $tmpField = current($field);
            } else {
                [$tmpField, $symbol] = $field;
                $tmpVal['symbol'] = $symbol;
            }

            $fields[$tmpField] = $tmpVal;
        }

        return $fields;
    }

    private function formatPkConfig()
    {
        return $this->context->fileBuildContext->table['pk'] ?? '';
    }

    private function getModelExtend($tableName)
    {
        if (false !== strpos($tableName, '.')) {
            [$schema, $tableName] = explode('.', $tableName);
        } else {
            $schema = 'cms';
            $tableName = '';
        }

        $map = [
            'cms'  => [BaseModel::class, 'BaseModel'],
            'bms'  => [BmsBaseModel::class, 'BmsBaseModel'],
            'xsst' => [XsstBaseModel::class, 'XsstBaseModel'],
        ];

        // 默认走cms
        [$modelClass, $modelName] = $map[$schema] ?? ['Imee\Models\Cms\Lesscode\BaseModel', 'BaseModel'];

        return [$modelClass, $modelName, ucfirst(camel_case($tableName))];
    }
}