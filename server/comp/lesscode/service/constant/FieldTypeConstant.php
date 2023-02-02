<?php

namespace Imee\Service\Lesscode\Constant;

class FieldTypeConstant
{
    // 默认字段类型
    const DEFAULT_TYPE = 'varchar';
    const DEFAULT_TYPE_LENGTH = 128;

    // int类型默认无符号
    const DEFAULT_UNSIGNED = true;

    // varchar类型默认空字符串
    const DEFAULT_VARCHAR_VALUE = '';

    // int类型默认0
    const DEFAULT_INT_VALUE = 0;

    // text类型默认null
    const DEFAULT_TEXT_VALUE = null;

    // 默认字符类型
    const DEFAULT_CHARSET = 'utf8mb4';

    // 默认引擎
    const DEFAULT_ENGINE = 'InnoDB';

    const DEFAULT_TYPE_INT = 'int';
    const DEFAULT_TYPE_INT_LENGTH = 10;
    const DEFAULT_TYPE_BIGINT = 'bigint';
    const DEFAULT_TYPE_BIGINT_LENGTH = 20;
    const DEFAULT_TYPE_FLOAT = 'float';
    const DEFAULT_TYPE_DECIMAL = 'decimal';
    const DEFAULT_TYPE_VARCHAR = 'varchar';
    const DEFAULT_TYPE_CHAR = 'char';
    const DEFAULT_TYPE_TEXT = 'text';

    const DEFAULT_CONDITION_SYMBOL = 'eq';
    const CONDITION_EQ  = 'eq';
    const CONDITION_EGT = 'egt';
    const CONDITION_ELT = 'elt';
    const CONDITION_GT  = 'gt';
    const CONDITION_LT  = 'lt';

    const TYPE_CHAR = 'char';
    const TYPE_VARCHAR = 'varchar';

    const TYPE_TINYINT = 'tinyint';
    const TYPE_SMALLINT = 'smallint';
    const TYPE_INT = 'int';
    const TYPE_BIGINT = 'bigint';
}