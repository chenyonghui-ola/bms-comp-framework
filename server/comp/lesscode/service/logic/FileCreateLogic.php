<?php

namespace Imee\Service\Lesscode\Logic;

use Imee\Service\Lesscode\Context\FileCreateContext;
use Imee\Service\Lesscode\FactoryService;

class FileCreateLogic
{
    /**
     * @var FileCreateContext
     */
    private $context;

    public function __construct(FileCreateContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        $context = FactoryService::get('fileCreateContext', []);

        $data = array_merge($context->files, [$this->context->addFileType => $this->context->addFilePath]);

        $context->setParams([
            'files' => $data,
            'mode'  => $this->context->mode // 标记文件是创建还是修改
        ]);

        return true;
    }
}