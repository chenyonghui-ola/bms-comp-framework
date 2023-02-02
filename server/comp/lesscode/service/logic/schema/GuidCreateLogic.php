<?php

namespace Imee\Service\Lesscode\Logic\Schema;


use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\Schema\SchemaPointService;
use Imee\Service\Lesscode\Traits\Curd\CreateTrait;
use Imee\Service\Lesscode\Context\Schema\CreateContext as SchemaCreateContext;
use Phalcon\Di;

class GuidCreateLogic
{
    use CreateTrait;

    private $guid;

    public function onBeforeCreate(&$params)
    {
        $config = json_encode(json_decode($params['table_config'], true), JSON_UNESCAPED_UNICODE);

        if (!empty($params['table_config']) && !in_array($params['table_config'], ['[]', '{}']) && (empty($config) || strtolower($config) == 'null')) {
            [$code, $msg] = CurdException::FIELD_JSON_FORMAT_ERROR;
            throw new CurdException($msg, $code);
        }

        $params['table_config'] = $config;

        $request = Di::getDefault()->getShared('request');
        $params['guid'] = $request->getPost('guid');
        $this->guid = $params['guid'];
    }

    public function onAfterCreate($params, $model)
    {
        // 创建功能后，创建关联功能
//        $service = new SchemaPointService;
//        $context = new SchemaCreateContext([
//            'guid'         => $this->guid,
//            'config_id'    => $model->id,
//            'schema_class' => new AdapterSchema($this->guid)
//        ]);
//        $service->create($context);
    }
}