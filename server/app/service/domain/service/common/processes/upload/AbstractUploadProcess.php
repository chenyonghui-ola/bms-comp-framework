<?php

namespace Imee\Service\Domain\Service\Common\Processes\Upload;

use Imee\Service\Domain\Context\Common\Upload\UploadBaseContext;
use OSS\OssUpload;
use Imee\Exception\Common\UploadException;
use Imee\Service\Helper;

/**
 * 上传
 */
abstract class AbstractUploadProcess
{
    protected $context;
    /**
     * @var OssUpload $uploadClient
     */
    protected $uploadClient;
    protected $allowMimeType = [];
    /**
     * 允许的文件大小，单位K
     */
    protected $allowFileSize;
    protected $allowExt;

    public function __construct(UploadBaseContext $context)
    {
        $this->context = $context;
        $this->allowFileSize = $context->allowFileSize ?: 0;
        if ($context->allowExt) {
            $this->allowExt = explode(',', $context->allowExt);
        }
        $this->getUploadClient();
    }

    private function getUploadClient()
    {
        $bucket = (ENV == 'dev') ? OssUpload::BUCKET_DEV : OssUpload::XS_IMAGE;
        $this->uploadClient = new OssUpload($bucket);
    }

    protected function before()
    {
        $ext = $this->context->file->getExtension();
        if (is_null($ext)) {
            list($code, $msg) = UploadException::UPLOAD_ERROR;
            throw new UploadException('文件扩展名不能为空', $code);
        }
        $mimeType = mime_content_type($this->context->file->getTempName());
        if (!$mimeType || !in_array($mimeType, $this->allowMimeType)) {
            //抛错
            list($code, $msg) = UploadException::MIME_NOALLOW_ERROR;
            throw new UploadException($msg, $code);
        }
        $ext = $this->context->file->getExtension();
        if (!in_array($ext, $this->allowExt)) {
            //抛错
            list($code, $msg) = UploadException::EXTENSION_NOALLOW_ERROR;
            throw new UploadException($msg, $code);
        }

        if ($this->allowFileSize > 0 && $this->allowFileSize < bcdiv($this->context->file->getSize(), 1024)) {
            //抛错
            list($code, $msg) = UploadException::FILE_SIZE_LARGE_ERROR;
            throw new UploadException($msg, $code);
        }
    }

    abstract protected function doing();

    public function handle()
    {
        $this->before();
        return $this->doing();
    }

    protected function getRemoteName()
    {
        $path = date('Ym/d/');
        if (!empty($this->context->path)) {
            $path = $this->context->path;
        }
        //兼容输入，去除两边反斜杠
        $path = trim($path, '/');
        $prefix = ip2long($_SERVER['SERVER_ADDR']);
        $ext = $this->context->file->getExtension();
        return $path . '/' . uniqid($prefix, true) . '.' . $ext;
    }

    protected function remoteFile()
    {
        $remoteName = $this->getRemoteName();
        $hasUploadSuccess = $this->uploadClient->moveFileTo($this->context->file->getTempName(), $remoteName);
        if (!$hasUploadSuccess) {
            //抛错
            Helper::debugger()->error($this->uploadClient->getError());
            list($code, $_) = UploadException::UPLOAD_ERROR;
            throw new UploadException($this->uploadClient->getError(), $code);
        }
        return $remoteName;
    }

    protected function moveFile()
    {
        return $this->remoteFile();
    }
}
