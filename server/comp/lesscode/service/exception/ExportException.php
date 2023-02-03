<?php

namespace Imee\Service\Lesscode\Exception;

class ExportException extends BaseException
{
    protected $serviceCode = '94';

    const EXPORT_CONSTANT_MUST  = ['00', '常量 %s 必须设置'];
}
