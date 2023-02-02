<?php

namespace Imee\Service\Domain\Context\Auth\Modules;

use Imee\Service\Domain\Context\BaseContext;

/**
 * 模块查询
 */
class GetInfoContext extends BaseContext
{
    /**
     * 控制器
     * @var string
     */
    protected $controller;

    /**
     * 方法
     * @var string
     */
    protected $action;
}
