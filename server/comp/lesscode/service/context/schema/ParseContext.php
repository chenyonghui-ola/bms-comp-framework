<?php


namespace Imee\Service\Lesscode\Context\Schema;

use Imee\Service\Lesscode\Context\BaseContext;

class ParseContext extends BaseContext
{
    /**
     * @var string 表名
     */
    protected $tableName;

    /**
     * @var string 表备注
     */
    protected $comment;

    /**
     * @var string 表引擎
     */
    protected $engine;

    /**
     * @var string schema
     */
    protected $schema;

    /**
     * @var array 字段
     */
    protected $fields;
}