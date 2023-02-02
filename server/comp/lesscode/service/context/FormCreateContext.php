<?php

namespace Imee\Service\Lesscode\Context;

use Imee\Schema\AdapterSchema;

class FormCreateContext extends BaseContext
{
    /**
     * @var string $guid 功能标识 生成文件使用
     */
    protected $guid;

    /**
     * @var string 功能父级菜单ID
     */
    protected $parentId;

    /**
     * @var string formily Schema json
     */
    protected $formilySchema;

    /**
     * @var \Phalcon\Mvc\Model 模型文件
     */
    protected $modelNamespace;

    /**
     * @var \BaseSchema schema
     */
//    protected $schemaNamespace;

    /**
     * @var object logic文件
     */
//    protected $logicNamespace;

    /**
     * @var AdapterSchema
     */
    protected $schemaClass;

    /**
     * @var string 功能模型
     */
    protected $model;

    /**
     * @var string 表名
     */
    protected $tableName;
}