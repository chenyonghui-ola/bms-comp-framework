<?php

namespace Imee\Service\Domain\Service\Common;

use Imee\Service\Domain\Context\Common\Upload\UploadBaseContext;
use Imee\Service\Domain\Service\Common\Processes\Upload\ImageUploadProcess;
use Imee\Service\Domain\Service\Common\Processes\Upload\VideoUploadProcess;
use Imee\Service\Domain\Service\Common\Processes\Upload\VoiceUploadProcess;
use Imee\Service\Domain\Service\Common\Processes\Upload\FileUploadProcess;
use Imee\Exception\Common\UploadException;

/**
 * 上传服务
 */
class UploadService
{
    private $context;

    public function __construct(UploadBaseContext $context)
    {
        $this->context = $context;
    }

    public const ACTION_IMAGE = 'image';
    public const ACTION_VIDEO = 'video';
    public const ACTION_VOICE = 'voice';
    public const ACTION_FILE = 'file';
    private $displayAction = [
        self::ACTION_IMAGE => ImageUploadProcess::class,
        self::ACTION_VIDEO => VideoUploadProcess::class,
        self::ACTION_VOICE => VoiceUploadProcess::class,
        self::ACTION_FILE  => FileUploadProcess::class,
    ];

    public function handle()
    {
        $this->verify();
        $className = $this->displayAction[$this->context->action];
        $processClass = (new \ReflectionClass($className))->newInstanceArgs(['context' => $this->context]);
        return $processClass->handle();
    }

    private function verify()
    {
        if (!isset($this->displayAction[$this->context->action])) {
            list($code, $msg) = UploadException::ACTION_NOEXIST_ERROR;
            throw new UploadException($msg, $code);
        }

        if (!$this->context->request->hasFiles()) {
            list($code, $msg) = UploadException::NO_UPLOAD_ERROR;
            throw new UploadException($msg, $code);
        }

        $files = $this->context->request->getUploadedFiles();
        $file = $files[0];

        if (!$file->isUploadedFile()) {
            list($code, $msg) = UploadException::SOURCE_UNIDENTIFIED_ERROR;
            throw new UploadException($msg, $code);
        }

        $this->context->setParams([
            'file' => $file
        ]);
    }
}
