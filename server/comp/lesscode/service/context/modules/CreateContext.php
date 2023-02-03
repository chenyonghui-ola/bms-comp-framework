<?php

namespace Imee\Service\Lesscode\Context\Modules;

use Imee\Service\Domain\Context\Auth\Modules\CreateContext as AuthModulesCreateContext;

/**
 * 模块创建
 */
class CreateContext extends AuthModulesCreateContext
{
    /**
     * @var string lesscode
     */
    protected $flag = 'lesscode';

    /**
     * controller
     * @var string
     */
    protected $controller;

    /**
     * @var bool 是否更新菜单
     */
    protected $updateMenu;
}
