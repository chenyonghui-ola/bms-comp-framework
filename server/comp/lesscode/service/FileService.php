<?php

namespace Imee\Service\Lesscode;

use \Imee\Service\BaseService;
use Imee\Service\Lesscode\Context\DiffContext;
use Imee\Service\Lesscode\Context\FileCreateContext;
use Imee\Service\Lesscode\Logic\FileCreateLogic;
use Imee\Service\Lesscode\Logic\FileDiffLogic;


/**
 * @property \Imee\Service\Lesscode\Logic\TemplateLogic templateLogic
 */
class FileService extends BaseService
{
    protected $factorys = [
        FactoryService::class
    ];

    public function createRecord(FileCreateContext $context)
    {
        $logic = new FileCreateLogic($context);

        return $logic->handle();
    }

    public function getRecord()
    {
        $context = FactoryService::get('fileCreateContext', []);

        return $context->files;
    }

    public function setDiff(DiffContext $context)
    {
        $logic = new FileDiffLogic($context);

        return $logic->set();
    }

    public function getDiff()
    {
        $logic = new FileDiffLogic();

        return $logic->get();
    }
}