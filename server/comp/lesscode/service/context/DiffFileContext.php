<?php


namespace Imee\Service\Lesscode\Context;


use Imee\Service\Lesscode\Context\Schema\SchemaDiffContext;

class DiffFileContext extends BaseContext
{
    /**
     * @var ModelDiffContext
     */
    protected $modelDiffContext;

    /**
     * @var SchemaDiffContext
     */
    protected $schemaDiffContext;
}