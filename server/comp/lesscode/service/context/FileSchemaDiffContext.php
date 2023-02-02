<?php


namespace Imee\Service\Lesscode\Context;

// todo lesscode schema对比后得出的数据，用于表修改操作

class FileSchemaDiffContext extends BaseContext
{
    /**
     * @var array schema 修改之前数据
     */
    protected $before;

    /**
     * @var array schema 修改之后数据
     */
    protected $after;

    /**
     * @var string 对比后可能得出的sql
     */
    protected $alterSql;
}