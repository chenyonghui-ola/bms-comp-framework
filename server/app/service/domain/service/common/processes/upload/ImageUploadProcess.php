<?php

namespace Imee\Service\Domain\Service\Common\Processes\Upload;

use Imee\Service\Domain\Context\Common\Upload\ImageUploadContext;
use Imee\Libs\Utility;

/**
 * 图片上传
 */
class ImageUploadProcess extends AbstractUploadProcess
{
    protected $allowExt = ['gif', 'jpg', 'jpeg', 'png','webp'];
    protected $allowMimeType = ['image/gif', 'image/jpeg', 'image/png', 'image/webp'];
    protected $allowFileSize = 2048;

    public function __construct(ImageUploadContext $context)
    {
        parent::__construct($context);
    }

    protected function doing()
    {
        list($width, $height, $type, $attr) = @getimagesize($this->context->file->getTempName());
        $fileName = $this->moveFile();
        //暂不考虑特殊的，如需特殊的需要扩展
        return [
            'url'    => Utility::getHeadUrl($fileName),
            'name'   => $fileName,
            'width'  => $width,
            'height' => $height,
        ];
    }
}
