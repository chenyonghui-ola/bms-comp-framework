<?php

namespace Imee\Service\Lesscode\Logic\Menu;


use Imee\Models\Cms\CmsModules;
use Imee\Service\Lesscode\Context\Menu\GetMainMenuContext;

class GetMainMenuLogic
{
    private $context;

    public function __construct(GetMainMenuContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        $info = CmsModules::findFirst();

        $condition = ['is_action = :is_action:'];
        $bind = ['is_action' => 0];

        if ($this->context->isAll !== true) {
            if (isset($info->deleted)) {
                $condition[] = 'deleted = :deleted:';
                $bind['deleted'] = 0;
            }

            if (isset($info->is_delete)) {
                $condition[] = 'is_delete = :is_delete:';
                $bind['is_delete'] = 0;
            }
        }

        $list = CmsModules::find([
            'conditions' => implode(' and ', $condition),
            'bind' => $bind,
            'order' => 'module_id desc',
        ])->toArray();

        return $list;
    }
}