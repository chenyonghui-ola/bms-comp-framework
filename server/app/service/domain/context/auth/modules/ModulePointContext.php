<?php

namespace Imee\Service\Domain\Context\Auth\Modules;

use Imee\Service\Domain\Context\BaseContext;

/**
 * 子模块
 */
class ModulePointContext extends BaseContext
{
    /**
     * 模块名称
     * @var string
     */
    protected $moduleName;

    /**
     * controller
     * @var string
     */
    protected $controller;

    /**
     * action
     * @var string
     */
    protected $action;

    /**
     * @var int 是否删除
     */
    protected $isDeleted;
}
