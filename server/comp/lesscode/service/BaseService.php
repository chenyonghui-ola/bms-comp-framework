<?php

namespace Imee\Service\Lesscode;

use Imee\Schema\BaseSchema;
use Phalcon\Mvc\Model;

abstract class BaseService
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var BaseSchema
     */
    protected $schema;

    public function __construct($model, BaseSchema $schema)
    {
        $this->model  = $model;
        $this->schema = $schema;
    }
}