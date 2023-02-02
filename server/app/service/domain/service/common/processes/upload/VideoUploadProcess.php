<?php

namespace Imee\Service\Domain\Service\Common\Processes\Upload;

use Imee\Libs\MtsUtility;
use Imee\Service\Domain\Context\Common\Upload\VideoUploadContext;
use Imee\Exception\Common\UploadException;
use Imee\Libs\Utility;

/**
 * 视频上传
 */
class VideoUploadProcess extends AbstractUploadProcess
{
    protected $allowExt = ['mp4', 'm4v', 'riv'];
    protected $allowMimeType = ['video/mp4', 'video/x-m4v', 'application/octet-stream'];
    protected $allowFileSize = 20480;

    public function __construct(VideoUploadContext $context)
    {
        parent::__construct($context);
    }

    protected function doing()
    {
        $fileName = $this->moveFile();

        if (ENV != 'dev') {
            $isCreateCoverSucc = MtsUtility::createCoverXs($fileName, $fileName . '.jpg');
            if (!$isCreateCoverSucc) {
                list($code, $msg) = UploadException::VIDEO_SCREENSHOT_ERROR;
                throw new UploadException($msg, $code);
            }
        }

        return [
            'url'  => Utility::getHeadUrl($fileName),
            'name' => $fileName,
        ];
    }
}
