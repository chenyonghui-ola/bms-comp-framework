<?php

namespace Imee\Service\Domain\Service\Auth;

use Imee\Service\Domain\Context\Auth\Role\ListContext;
use Imee\Service\Domain\Service\Auth\Processes\Role\ListProcess;
use Imee\Service\Domain\Context\Auth\Role\InfoContext;
use Imee\Service\Domain\Service\Auth\Processes\Role\InfoProcess;
use Imee\Service\Domain\Context\Auth\Role\CreateContext;
use Imee\Service\Domain\Service\Auth\Processes\Role\CreateProcess;
use Imee\Service\Domain\Context\Auth\Role\ModifyContext;
use Imee\Service\Domain\Service\Auth\Processes\Role\ModifyProcess;
use Imee\Service\Domain\Service\Auth\Processes\Role\AllProcess;

/**
 * 角色服务
 */
class RoleService
{
    /**
     * 列表
     */
    public function getList(ListContext $context)
    {
        $process = new ListProcess($context);
        return $process->handle();
    }

    public function getInfo(InfoContext $context)
    {
        $process = new InfoProcess($context);
        return $process->handle();
    }

    public function create(CreateContext $context)
    {
        $process = new CreateProcess($context);
        return $process->handle();
    }

    public function modify(ModifyContext $context)
    {
        $process = new ModifyProcess($context);
        return $process->handle();
    }

    public function getAll()
    {
        $process = new AllProcess();
        return $process->handle();
    }
}
