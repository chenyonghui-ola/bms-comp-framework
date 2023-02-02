<?php

/**
 * 构造 model/schema 所用到的一些参数
 */

namespace Imee\Service\Lesscode\Context;


use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Context\Schema\SchemaContext;

class FileBuildContext extends BaseContext
{
    /**
     * @var ModelContext model
     */
    protected $modelContext;

    /**
     * @var SchemaContext schema
     */
    protected $schemaContext;

    /**
     * @var AdapterSchema
     */
    protected $schemaClass;

    /**
     * @var array 表字段信息
     */
    protected $table;

    /**
     * @var array 表单验证等
     * @see \DemoSchema
     */
    protected $form = [
        // 表单验证
        'validations' => [
            // 验证规则
            'rule'  => [],
            // 验证场景
            'scene' => [],
        ]
    ];

    /**
     * @var array 筛选
     */
    protected $list = [
        // 列表字段属性
        'list'   => [],
        // 筛选
        'filter' => [],

        // 列表字段 默认读取 table fields，处理关联关系使用
        'fields' => [],
    ];
}