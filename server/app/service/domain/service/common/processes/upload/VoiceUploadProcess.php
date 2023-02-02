<?php

namespace Imee\Service\Domain\Service\Common\Processes\Upload;

use Imee\Service\Domain\Context\Common\Upload\VoiceUploadContext;
use Imee\Libs\Utility;

/**
 * 音频上传
 */
class VoiceUploadProcess extends AbstractUploadProcess
{
    protected $allowExt = ['amr', 'm4a', 'mp3'];
    protected $allowMimeType = ['audio/amr', 'audio/mp4a-latm', 'audio/mpeg'];

    public function __construct(VoiceUploadContext $context)
    {
        parent::__construct($context);
    }

    protected function getRemoteName()
    {
        $path = "voice/" . date("Ym") . "/";
        if (!empty($this->context->path)) {
            $path = $this->context->path;
        }
        //兼容输入，去除两边反斜杠
        $path = trim($path, '/');
        $remoteName = date("ymdHis") . rand(10, 99) . "." . $this->context->file->getExtension();
        return $path . '/' . $remoteName;
    }

    protected function doing()
    {
        $fileName = $this->moveFile();

        return [
            'url'  => Utility::getHeadUrl($fileName),
            'name' => $fileName,
        ];
    }
}
