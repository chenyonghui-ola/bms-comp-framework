<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Modules;

use Imee\Service\Domain\Context\Auth\Modules\SearchContext;
use Imee\Helper\MenuConfig;
use Imee\Service\Domain\Service\Auth\Processes\Modules\Traits\ParseControllerRouteTrait;

/**
 * 模块搜索
 */
class SearchProcess
{
    use ParseControllerRouteTrait;

    private $context;

    public function __construct(SearchContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        $format = [];
        $menuConfig = MenuConfig::getConfig();

        $pagePath = '';
        if (isset($menuConfig[$this->context->page])) {
            $pagePath = $menuConfig[$this->context->page];
        }
        
        $realDir = $this->context->dir;

        return $this->getResult($realDir, $pagePath);
    }
}
