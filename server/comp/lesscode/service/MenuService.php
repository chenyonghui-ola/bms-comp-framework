<?php

namespace Imee\Service\Lesscode;

use Imee\Service\Lesscode\Context\Common\DataContext;
use Imee\Service\Lesscode\Context\GuidContext;
use Imee\Service\Lesscode\Context\Menu\GetTopMenuContext;
use Imee\Service\Lesscode\Context\Modules\CreateContext;
use Imee\Service\Lesscode\Logic\Menu\AttachLogic;
use Imee\Service\Lesscode\Logic\Menu\CheckLogic;
use Imee\Service\Lesscode\Logic\Menu\CreateLogic;
use Imee\Service\Lesscode\Logic\Menu\GetInfoLogic;
use Imee\Service\Lesscode\Logic\Menu\GetListLogic;
use Imee\Service\Lesscode\Logic\Menu\GetTopMenuLogic;
use Imee\Service\Lesscode\Logic\Menu\InitLogic;
use Imee\Service\Lesscode\Logic\Menu\GetMainMenuLogic;
use Imee\Service\Lesscode\Context\Menu\GetMainMenuContext;
use Imee\Service\Lesscode\Logic\Menu\GetAllMenuLogic;

class MenuService
{
    /**
     * 获取菜单初始化数据 context
     */
    public function init(): CreateContext
    {
        $logic = new InitLogic();

        return $logic->handle();
    }

    public function checkCreate(GuidContext $context): bool
    {
        $logic = new CheckLogic($context);

        return $logic->handle();
    }

    /**
     * 创建菜单
     * @param  CreateContext  $context
     */
    public function create(CreateContext $context)
    {
        $logic = new CreateLogic($context);

        return $logic->handle();
    }

    public function attach(DataContext $context)
    {
        $logic = new AttachLogic($context);

        return $logic->handle();
    }

    public function getInfo(GuidContext $context): array
    {
        $logic = new GetInfoLogic($context);

        return $logic->handle();
    }

    public function getListById(GuidContext $context): array
    {
        $logic = new GetListLogic($context);

        return $logic->handle();
    }

    public function getMainMenu($params = [])
    {
        $context = new GetMainMenuContext($params);
        $logic = new GetMainMenuLogic($context);

        return $logic->handle();
    }

    public function getTopParentMenu($params)
    {
        $context = new GetTopMenuContext($params);
        $logic   = new GetTopMenuLogic($context);
        return $logic->handle();
    }

    public function getAllMenu(): array
    {
        $logic = new GetAllMenuLogic();

        return $logic->handle();
    }
}