<?php

namespace Imee\Service\Domain\Context\Auth\Modules;

use Imee\Service\Domain\Context\BaseContext;

/**
 * 模块明细
 */
class InfoContext extends BaseContext
{
    /**
     * 模块ID
     * @var int
     */
    protected $moduleId;

    protected $dir;

    protected $format;
}
