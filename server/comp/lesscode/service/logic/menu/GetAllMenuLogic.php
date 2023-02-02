<?php

namespace Imee\Service\Lesscode\Logic\Menu;


use Imee\Models\Cms\CmsModules;

class GetAllMenuLogic
{
    public function handle(): array
    {
        return CmsModules::find([
            'order' => 'module_id desc',
        ])->toArray();
    }
}