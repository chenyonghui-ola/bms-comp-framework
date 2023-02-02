<?php

namespace Imee\Service\Lesscode\Exception;

use Imee\Exception\ReportException;

class BaseException extends ReportException
{
    protected $moduleCode = '99';
    protected $serviceCode = '00';
}
