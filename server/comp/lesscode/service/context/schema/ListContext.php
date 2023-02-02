<?php


namespace Imee\Service\Lesscode\Context\Schema;

use Imee\Service\Lesscode\Context\GuidContext;

class ListContext extends GuidContext
{
    /**
     * @var string type
     */
    protected $type;

    /**
     * @var string drive
     */
    protected $drive;

    /**
     * @var int state
     */
    protected $state;

    /**
     * @var int is_system
     */
    protected $isSystem;
}