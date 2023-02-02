<?php
namespace Imee\Service\Lesscode\Strategys;

use Imee\Service\Lesscode\Interfaces\HandleInterface;

abstract class Strategy
{
    protected $_strategy;

    public function __construct(HandleInterface $strategy)
    {
        $this->_strategy = $strategy;
    }
}