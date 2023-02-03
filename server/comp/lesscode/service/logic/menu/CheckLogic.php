<?php

namespace Imee\Service\Lesscode\Logic\Menu;


use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Service\Lesscode\Context\GuidContext;

class CheckLogic
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
     * @return bool
     */
    public function handle(): bool
    {
        $guid = $this->context->guid;

        $info = LesscodeMenu::findFirst([
            'conditions' => 'guid = :guid:',
            'bind'       => ['guid' => $guid]
        ]);

        return $info ? false : true;
    }
}