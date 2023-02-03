<?php
namespace Imee\Service\Lesscode\Strategys;

class CurdStrategy extends Strategy
{
    public function getList()
    {
        return $this->_strategy->handle();
    }

    public function create()
    {
        return $this->_strategy->handle();
    }

    public function modify()
    {
        return $this->_strategy->handle();
    }

    public function delete()
    {
        return $this->_strategy->handle();
    }

    public function export()
    {
        return $this->_strategy->handle();
    }
}