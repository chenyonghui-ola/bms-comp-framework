<?php

namespace Imee\Service\Domain\Context\Auth\Staff;

use Imee\Service\Domain\Context\BaseContext;

class ModuleContext extends BaseContext
{
    /**
     * 父模块id
     * @var
     */
    protected $parentModuleId;

    /**
     * @var string 语言
     */
    protected $lang;
}
