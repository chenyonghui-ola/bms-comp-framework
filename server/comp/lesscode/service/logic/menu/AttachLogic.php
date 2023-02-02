<?php

namespace Imee\Service\Lesscode\Logic\Menu;


use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Service\Lesscode\Context\Common\DataContext;

class AttachLogic
{
    /**
     * @var DataContext
     */
    private $context;

    /**
     * @var LesscodeMenu
     */
    private $masterModel = LesscodeMenu::class;

    private $list;

    public function __construct(DataContext $context)
    {
        $this->context = $context;
    }


    public function handle()
    {
        $this->getList();

        $role = $this->context->data;

        if (!isset($role['pages']) || empty($role['pages'])) {
            return [];
        }

        foreach ($role['pages'] as $k => $v) {
            if (isset($this->list[$v['module_id']])) {
                $role['pages'][$k]['flag'] = 1;
                $role['pages'][$k]['guid'] = $this->list[$v['module_id']]['guid'];
                // 前端通用页面 /lesscode/common
                $role['pages'][$k]['path'] = '/lesscode/common?guid=' . $this->list[$v['module_id']]['guid'];
            } else {
                $role['pages'][$k]['flag'] = 0;
                $role['pages'][$k]['guid'] = '';
            }
        }

        return $role;
    }

    protected function getList()
    {
        $list = $this->masterModel::find()->toArray();

        $this->list = array_column($list, null, 'menu_id');
    }
}