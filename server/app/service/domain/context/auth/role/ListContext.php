<?php

namespace Imee\Service\Domain\Context\Auth\Role;

use Imee\Service\Domain\Context\PageContext;

/**
 * 角色列表
 */
class ListContext extends PageContext
{
    protected $sort = 'role_id';

    protected $dir = 'desc';
}
