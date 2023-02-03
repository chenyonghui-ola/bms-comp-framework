<?php


namespace Imee\Service\Lesscode\Context;


use Phalcon\Mvc\Model;

class ModelDiffContext extends DiffContext
{
    /**
     * @var string 命名空间
     */
    protected $namespace;

    /**
     * @var Model 修改之前
     */
    protected $beforeClass;

    /**
     * @var Model 修改之后
     */
    protected $afterClass;
}