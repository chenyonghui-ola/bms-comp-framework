<?php


namespace Imee\Service\Lesscode\Context\Schema;

use Imee\Service\Lesscode\Context\DiffContext;

class SchemaDiffContext extends DiffContext
{
    /**
     * @var string 命名空间
     */
    protected $namespace;

    /**
     * @var \BaseSchema 修改之前
     */
    protected $beforeClass;

    /**
     * @var \BaseSchema 修改之后
     */
    protected $afterClass;
}