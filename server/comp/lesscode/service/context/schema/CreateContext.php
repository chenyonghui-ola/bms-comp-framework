<?php


namespace Imee\Service\Lesscode\Context\Schema;

use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Context\GuidContext;

class CreateContext extends GuidContext
{
    /**
     * @var int config id
     */
    protected $configId;

    /**
     * @var AdapterSchema
     */
    protected $schemaClass;
}