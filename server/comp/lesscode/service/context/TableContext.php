<?php


namespace Imee\Service\Lesscode\Context;


class TableContext extends GuidContext
{
    /**
     * @var string schema name
     */
    protected $schema;

    /**
     * @var string schema namespace
     */
    protected $schemaNamespace;

    /**
     * @var string model name
     */
    protected $model;

    /**
     * @var string model namespace
     */
    protected $modelNamespace;

    /**
     * @var string 数据库连接
     */
    protected $schemaLink;

    /**
     * @var bool 是否执行sql语句
     */
    protected $execSql = true;
}