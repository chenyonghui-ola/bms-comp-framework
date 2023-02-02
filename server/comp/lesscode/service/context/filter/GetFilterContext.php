<?php

namespace Imee\Service\Lesscode\Context\Filter;

use Imee\Schema\BaseSchema;
use Imee\Service\Lesscode\Context\BaseContext;
use Phalcon\Mvc\Model;

class GetFilterContext extends BaseContext
{
    /**
     * @var Model model
     */
   protected $model;

    /**
     * @var BaseSchema
     */
   protected $schema;

    /**
     * @var array 接收到到参数
     */
    protected $params;
}
