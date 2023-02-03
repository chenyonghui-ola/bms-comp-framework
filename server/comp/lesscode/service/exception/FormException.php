<?php

namespace Imee\Service\Lesscode\Exception;

class FormException extends BaseException
{
    protected $serviceCode = '99';

    const EMPTY_FILE_ERROR = ['01', '文件内容为空'];

    const MODEL_FILE_EXIST_ERROR = ['11', 'model [%s] 文件已存在'];
    const SCHEMA_FILE_EXIST_ERROR = ['12', 'schema [%s] 文件已存在'];
    const VALIDATION_FILE_EXIST_ERROR = ['13', 'validation [%s] 文件已存在'];
    const MODEL_FILE_NOT_EXIST_ERROR = ['14', 'model [%s] 文件不存在'];
    const MODEL_FILE_INVALID_ERROR = ['15', '%s 不是一个model文件'];

    const SCHEMA_JSON_DATA_ERROR = ['20', 'formily-schema解析json错误'];
    const SCHEMA_JSON_DATA_FORMAT_ERROR = ['21', '暂不支持 %s 字段'];
    const DATA_EXSITS_ERROR = ['22', '数据已存在'];
    const SCHEMA_MODULE_NAME_EMPTY = ['23', '模块名称不能为空'];
    const SCHEMA_JSON_FIELD_NAME_EMPTY = ['24', '[%s] 字段标识需要填写'];

    const FILE_EXISTS_CLASS_NO_EXISTS = ['25', '[%s] 文件存在，类不存在'];

    const MENU_MAIN_NOT_EXIST = ['40', 'main菜单未找到'];

    const TABLE_NOT_PARSE_ERROR = ['50', '无需解析'];
    const TABLE_PARSE_MODEL_NO_EXIST_ERROR = ['51', 'model不存在'];
    const TABLE_PARSE_ERROR = ['52', '解析失败'];
    const TABLE_PARSE_SCHEMA_NOT_EXIST_ERROR = ['53', 'schema不存在'];
    const TABLE_PARSE_DATABASE_NOT_EXIST_ERROR = ['54', 'database配置不存在'];
    const TABLE_PARSE_MYSQL_GET_INFO_ERROR = ['55', 'mysql结构获取失败'];
}
