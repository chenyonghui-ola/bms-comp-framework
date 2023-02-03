<?php

namespace Imee\Service\Lesscode\Logic\Menu;


use Imee\Service\Lesscode\Context\Modules\CreateContext;

class InitLogic
{
    /**
     * 默认生成菜单的名称
     */
    const DEFAULT_ACTION = 'main';

    public function handle()
    {
        return new CreateContext([
            'action' => InitLogic::DEFAULT_ACTION,
            'type'   => 'page',
        ]);
    }
}