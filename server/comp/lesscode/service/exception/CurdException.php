<?php

namespace Imee\Service\Lesscode\Exception;

class CurdException extends BaseException
{
    protected $serviceCode = '98';

    const ILLEGAL_ERROR = ['01', '非法操作'];   // 10999801
    const NO_DATA_ERROR = ['02', '数据不存在']; // 10999802
    const SAVE_ERROR = ['03', '编辑失败'];   // 10999803
    const CREATE_ERROR = ['04', '添加失败'];   // 10999804
    const DELETE_ERROR = ['05', '删除失败'];   // 10999805
    const ENUM_LIST_ERROR = ['06', '%s 枚举值数据错误'];   // 10999806
    const ILLEGAL_GUID_ERROR = ['07', '非法操作'];   // 10999807
    const SCHEMA_LACK_GUID_ERROR = ['08', 'schema缺少guid']; // 10999808
    const FIELD_NO_DATA_ERROR = ['09', '%s 不能为空']; // 10999809
    const FIELD_DATA_EXISTS_ERROR = ['10', '%s 已存在']; // 10999810
    const FIELD_LEN_MAX_ERROR = ['11', '%s 最大长度是 %s']; // 10999811
    const FIELD_JSON_FORMAT_ERROR = ['12', 'JSON格式化错误']; // 10999812
    const SYSTEM_POINT_DELETE_ERROR = ['13', '系统级功能不可以删除']; // 10999813
    const SYSTEM_SCHEMA_MODEL_NO_FOUND = ['14', 'Model not found %s']; // 10999814
    const FIELD_NOT_SUPPORT_SORT = ['15', '字段 %s 不支持排序']; // 10999815
    const HOOK_LOGIC_FILE_NOT_EXIST = ['16', '钩子文件不存在 %s ']; // 10999816
}
