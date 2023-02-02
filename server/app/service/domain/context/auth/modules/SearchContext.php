<?php

namespace Imee\Service\Domain\Context\Auth\Modules;

use Imee\Service\Domain\Context\BaseContext;

/**
 * 模块搜索
 */
class SearchContext extends BaseContext
{
    /**
     * 模块名称
     * @var string
     */
    protected $page;

    /**
     * 工作路径
     */
    protected $dir;
}
