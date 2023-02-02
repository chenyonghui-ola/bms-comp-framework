<?php

namespace Imee\Service\Lesscode\Data;

use Imee\Helper\Traits\FactoryServiceTrait;
use Imee\Service\Lesscode\Context\BaseContext;
use Imee\Service\Lesscode\Context\FormCheckContext;
use Imee\Service\Lesscode\Context\FormCreateContext;
use Imee\Service\Lesscode\Context\GuidContext;
use Imee\Service\Lesscode\FactoryService;

use Imee\Service\Lesscode\Context\Schema\CreateContext as SchemaCreateContext;
use Imee\Service\Lesscode\Schema\SchemaPointService;
use Imee\Service\Lesscode\Schema\SchemaService;

/**
 * @property \Imee\Models\Cms\Lesscode\LesscodeSchemaConfig schemaConfigModel
 * @method  \Imee\Models\Cms\Lesscode\LesscodeSchemaConfig findFirstByGuid($params)
 */
class SchemaConfigData
{
    use FactoryServiceTrait;

    protected $factorys = [
        FactoryService::class
    ];

    public function save(FormCreateContext $context)
    {
        $info = $this->schemaConfigModel::findFirstByGuid($context->guid);

        $isCreate = false;

        if (empty($info)) {
            $info       = $this->schemaConfigModel;
            $info->guid = $context->guid;

            $isCreate = true;
        }

        $info->schema_json = $context->formilySchema;

        if (true === $isCreate) {
            !empty($context->modelNamespace) && $info->model = $context->modelNamespace;
        }

//        !empty($context->schemaNamespace) && $info->schema = $context->schemaNamespace;
//        !empty($context->logicNamespace) && $info->logic = $context->logicNamespace;
        $context->schemaClass->table && $info->table_config = json_encode($context->schemaClass->table, JSON_UNESCAPED_UNICODE);

        $info->save();

        $schemaCreateContext = new SchemaCreateContext([
            'guid'         => $context->guid,
            'config_id'    => $info->id,
            'schema_class' => $context->schemaClass
        ]);
        $service = new SchemaPointService();

        if (true === $isCreate && $info->id > 0) {
            // 创建功能点
            $service->create($schemaCreateContext);
        }

        if (false === $isCreate && $info->id > 0) {
            $service->modify($schemaCreateContext);
        }
    }

    public function checkGuid(FormCheckContext $context)
    {
        $info = $this->schemaConfigModel::findFirstByGuid($context->guid);

        return $info ? false : true;
    }

    public function getInfoByGuid(BaseContext $context)
    {
        $info = $this->schemaConfigModel::findFirstByGuid($context->guid);

        return $info ? $info->toArray() : [];
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([(new self())->schemaConfigModel, $name], $arguments);
    }
}