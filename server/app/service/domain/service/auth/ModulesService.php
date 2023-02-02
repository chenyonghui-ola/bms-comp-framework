<?php

namespace Imee\Service\Domain\Service\Auth;

use Imee\Service\Domain\Context\Auth\Modules\GetInfoContext;
use Imee\Service\Domain\Service\Auth\Processes\Modules\GetInfoByIdProcess;
use Imee\Service\Domain\Service\Auth\Processes\Modules\GetInfoProcess;
use Imee\Service\Domain\Service\Auth\Processes\Modules\ListProcess;
use Imee\Service\Domain\Context\Auth\Modules\InfoContext;
use Imee\Service\Domain\Service\Auth\Processes\Modules\InfoProcess;
use Imee\Service\Domain\Context\Auth\Modules\CreateContext;
use Imee\Service\Domain\Service\Auth\Processes\Modules\CreateProcess;
use Imee\Service\Domain\Context\Auth\Modules\ModifyContext;
use Imee\Service\Domain\Service\Auth\Processes\Modules\ModifyProcess;
use Imee\Service\Domain\Context\Auth\Modules\RemoveContext;
use Imee\Service\Domain\Service\Auth\Processes\Modules\RemoveProcess;
use Imee\Service\Domain\Context\Auth\Modules\SearchContext;
use Imee\Service\Domain\Service\Auth\Processes\Modules\SearchProcess;
use Imee\Service\Domain\Context\Auth\Modules\PointListContext;
use Imee\Service\Domain\Service\Auth\Processes\Modules\PointListProcess;

/**
 * 模块服务
 */
class ModulesService
{
    /**
     * 列表
     */
    public function getList()
    {
        $process = new ListProcess();
        return $process->handle();
    }

    public function getPointList(PointListContext $context)
    {
        $process = new PointListProcess($context);
        return $process->handle();
    }

    public function search(SearchContext $context)
    {
        $process = new SearchProcess($context);
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

    public function remove(RemoveContext $context)
    {
        $process = new RemoveProcess($context);
        return $process->handle();
    }

	public function getInfoBy(GetInfoContext $context)
	{
		$process = new GetInfoProcess($context);
		return $process->handle();
	}

	public function getInfoById(InfoContext $context)
	{
		$process = new GetInfoByIdProcess($context);
		return $process->handle();
	}
}
