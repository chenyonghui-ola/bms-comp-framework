<?php

namespace Imee\Service\Domain\Context\Auth\Role;

use Imee\Service\Domain\Context\BaseContext;

/**
 * 角色创建
 */
class CreateContext extends BaseContext
{
    /**
     * 角色名称
     * @var string
     */
    protected $roleName;

    /**
     * 模块ids
     * @var array
     */
    protected $moduleIds;

    /**
     * @var string
     */
    protected $tree;
}
