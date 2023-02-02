<?php

namespace Imee\Service\Lesscode\Logic\Menu;


use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Schema\AdapterSchema;
use Imee\Service\Domain\Context\Auth\Modules\InfoContext;
use Imee\Service\Domain\Service\Auth\ModulesService;
use Imee\Service\Lesscode\Context\GuidContext;

class GetInfoLogic
{
    /**
     * @var GuidContext
     */
    protected $context;

    public function __construct(GuidContext $context)
    {
        $this->context = $context;
    }

    /**
     * 如果数据存在返回false
     * @return array
     */
    public function handle(): array
    {
        $guid = $this->context->guid;

        $list = LesscodeMenu::find([
            'conditions' => 'guid = :guid:',
            'bind'       => ['guid' => $guid]
        ]);

        $moduleInfo = [];

        if (!empty($list)) {
            // 查询菜单
            $moduleService = new ModulesService();

            foreach ($list as $item)
            {
                $moduleInfo = $moduleService->getInfoById(new InfoContext(['module_id' => $item->menu_id]));
                if ($moduleInfo['action'] === AdapterSchema::POINT_LIST) {
                    break;
                }
            }
        }

        return $moduleInfo;
    }
}