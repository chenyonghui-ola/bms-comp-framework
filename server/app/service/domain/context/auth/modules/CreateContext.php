<?php

namespace Imee\Service\Domain\Context\Auth\Modules;

use Imee\Service\Domain\Context\BaseContext;

/**
 * 模块创建
 */
class CreateContext extends BaseContext
{
    protected $dir;
    /**
     * 模块名称
     * @var string
     */
    protected $moduleName;

    /**
     * 图标
     * @var string
     */
    protected $icon;

    /**
     * 父级模块ID
     * @var int
     */
    protected $parentModuleId;

    /**
     * 导航类型
     * @var int
     */
    protected $type;

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
     * 子模块上下文数组
     * @var array []ModulePointContext
     */
    protected $modulePointContexts;
}
