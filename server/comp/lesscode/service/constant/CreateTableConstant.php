<?php

namespace Imee\Service\Lesscode\Constant;

class CreateTableConstant
{
    // 默认主键名称
    const PK_NAME = 'id';

    // 默认创建时间字段名称
    const CREATE_TIME_FIELD = 'create_time';

    // 默认编辑时间字段名称
    const UPDATE_TIME_FIELD = 'update_time';

    const PK_FIELD = [
        self::PK_NAME => ['type' => 'int', 'length' => 10, 'default' => 0, 'unsigned' => true, 'comment' => self::PK_NAME],
    ];

    // 默认附加字段
    const ATTACH_FIELDS = [
        self::CREATE_TIME_FIELD => ['type' => 'int', 'length' => 10, 'default' => 0, 'comment' => '创建时间'],
        self::UPDATE_TIME_FIELD => ['type' => 'int', 'length' => 10, 'default' => 0, 'comment' => '更新时间'],
    ];
}