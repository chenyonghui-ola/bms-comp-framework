<?php

namespace Imee\Service\Lesscode\Logic\Menu;


use Imee\Service\Domain\Context\Auth\Modules\InfoContext;
use Imee\Service\Domain\Service\Auth\ModulesService;
use Imee\Service\Lesscode\Context\Menu\GetTopMenuContext;

class GetTopMenuLogic
{
    /**
     * @var GetTopMenuContext
     */
    private $context;

    /**
     * @var ModulesService
     */
    private $moduleService;

    public function __construct(GetTopMenuContext $context)
    {
        $this->context = $context;
        $this->moduleService = new ModulesService();
    }

    public function handle()
    {
        $info = $this->moduleService->getInfoById(new InfoContext(['module_id' => $this->context->moduleId]));

        for ($i = 1; $i <= 8; ++ $i)
        {
            if (!empty($info) && $info['parent_module_id'] > 0) {
                $info = $this->moduleService->getInfoById(new InfoContext(['module_id' => $info['parent_module_id']]));
            }

            if (empty($info) || $info['parent_module_id'] == 0) {
                break;
            }
        }

        return !empty($info) ? $info : [];
    }
}