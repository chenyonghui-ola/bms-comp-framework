<?php

namespace Imee\Service\Lesscode\Logic\Schema;


use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\Traits\Curd\SaveTrait;

class GuidModifyLogic
{
    use SaveTrait;

    public function onBeforeSave(&$params)
    {
        $config = json_encode(json_decode($params['table_config'], true), JSON_UNESCAPED_UNICODE);

        if (!empty($params['table_config']) && !in_array($params['table_config'], ['[]', '{}']) && (empty($config) || strtolower($config) == 'null')) {
            [$code, $msg] = CurdException::FIELD_JSON_FORMAT_ERROR;
            throw new CurdException($msg, $code);
        }

        $config = json_decode($params['table_config'], true);
        $config['comment'] = $params['title'] ?? $config['comment'];
        $config = json_encode($config, JSON_UNESCAPED_UNICODE);

        $params['table_config'] = $config;
    }

    public function onAfterSave($params, $model)
    {
        // TODO: Implement onAfterCreate() method.
    }
}