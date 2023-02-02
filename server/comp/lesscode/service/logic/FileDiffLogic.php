<?php

namespace Imee\Service\Lesscode\Logic;

use Imee\Service\Lesscode\Context\DiffContext;
use Imee\Service\Lesscode\Context\DiffFileContext;
use Imee\Service\Lesscode\Context\ModelDiffContext;
use Imee\Service\Lesscode\Context\Schema\SchemaDiffContext;
use Imee\Service\Lesscode\FactoryService;

class FileDiffLogic
{
    /**
     * @var DiffContext
     */
    private $context;


    public function __construct(DiffContext $context = null)
    {
        $this->context = $context;
    }

    public function set()
    {
        /**
         * @var $context DiffFileContext
         */
        $context = FactoryService::get('diffFileContext', []);

        if ($this->context instanceof ModelDiffContext) {
            $context->setParams([
                'model_diff_context' => $this->context
            ]);
        }

        if ($this->context instanceof SchemaDiffContext) {
            $context->setParams([
                'schema_diff_context' => $this->context
            ]);
        }
    }

    public function get()
    {
        return FactoryService::get('diffFileContext', []);
    }
}