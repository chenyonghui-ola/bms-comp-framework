<?php

namespace Imee\Service\Domain\Context\Auth\Role;

use Imee\Service\Domain\Context\BaseContext;

/**
 * 角色修改
 */
class ModifyContext extends CreateContext
{
    /**
     * 角色ID
     * @var int
     */
    protected $roleId;
}
