<?php

namespace Imee\Service\Lesscode\Exception;

class ModelException extends BaseException
{
    protected $serviceCode = '96';

    const SYSTEM_TABLE_PREFIX_REQUIRE  = ['00', 'system err, 必须配置数据表前缀'];
    const SYSTEM_ADAPTER_MODEL_SET_SCHEMA  = ['01', '使用adapterModel必须设置schema'];
    const SYSTEM_ADAPTER_MODEL_SET_TABLE_NAME  = ['02', '使用adapterModel必须设置table_name'];
    const SYSTEM_ADAPTER_MODEL_SET_TABLE_SCHEMA  = ['03', '使用adapterModel必须设置table_schema'];
}
