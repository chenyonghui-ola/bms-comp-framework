<?php

namespace Imee\Service\Domain\Service\Common\Processes\Upload;

use Imee\Service\Domain\Context\Common\Upload\FileUploadContext;
use Imee\Libs\Utility;
use Imee\Exception\Common\UploadException;

/**
 * 文件上传
 */
class FileUploadProcess extends AbstractUploadProcess
{
    protected $allowExt = [
        'gif', 'jpg', 'jpeg', 'png', 'webp',
        'mp4', 'm4v',
        'amr', 'm4a', 'mp3',
        'xls', 'xlsx', 'csv',
		'zip', 'json',
    ];
    protected $allowMimeType = [
        'image/gif', 'image/jpeg', 'image/png', 'image/webp',
        'video/mp4', 'video/x-m4v',
        'audio/amr', 'audio/mp4a-latm', 'audio/mpeg',
        'application/csv', 'text/csv', 'text/plain', 'application/zip',
		'text/json'
    ];
    protected $allowFileSize = 20480;

    public function __construct(FileUploadContext $context)
    {
        parent::__construct($context);
    }

    protected function getRemoteName()
    {
        if (!empty($this->context->type)) {
            $type = $this->context->type;
            $tmp = explode(':', $type);
            $function = $tmp[0];
            $type = $tmp[1] ?? '';
            return $this->$function($type);
        }
        $path = "file/" . date("Ym") . "/";
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

    protected function commodity($type): string
    {
        //校验文件扩展名
        $allowMimeType = ['image/jpeg', 'image/png', 'image/webp'];
        $mimeType = mime_content_type($this->context->file->getTempName());
        if (!$mimeType || !in_array($mimeType, $allowMimeType)) {
            //抛错
            list($code, $msg) = UploadException::MIME_NOALLOW_ERROR;
            throw new UploadException($msg . $mimeType, $code);
        }
        //校验文件大小
        if ($mimeType == 'image/webp') {
            $maxSize = 10240;
        } else {
            $maxSize = 2048;
        }
        if ($maxSize < bcdiv($this->context->file->getSize(), 1024)) {
            //抛错
            list($code, $msg) = UploadException::FILE_SIZE_LARGE_ERROR;
            throw new UploadException($msg . '2M', $code);
        }

        //最后返回文件path
        switch ($type) {
            case 'header':
            case 'union_header':
                $remoteFile = "h" . date("ymdHis") . rand(10, 99);
                $remoteName = "static/effect/" . $remoteFile . "." . $this->context->file->getExtension();
                break;
            case 'bubble':
            case 'ring':
            case 'decorate':
            case 'effect':
                if (in_array($this->context->file->getExtension(), ['webp', 'mp4'])) {
                    $head = 'h';
                } else {
                    $head = 'c';
                }
                $remoteFile = $head . date("ymdHis") . rand(10, 99);
                $remoteName = "static/commodity/" . $remoteFile . "." . $this->context->file->getExtension();
                break;
            default:
                $remoteFile = "c" . date("ymdHis") . rand(10, 99);
                $remoteName = "static/commodity/" . $remoteFile . "." . $this->context->file->getExtension();
        }

        return $remoteName;
    }

    protected function gift($type)
    {
		//type gift_id
		$type = explode('_', $type);
		if (count($type) !== 2) {
			UploadException::throwException(UploadException::GIFT_UPLOAD_PARAMS_ERROR);
		}
		$id = (int)$type[1];
		$uploadType = $type[0];
		if ($id < 1 || !in_array($uploadType, ['list', 'start', 'end', 'zip', 'webp', 'head', 'mp4', 'json', 'preview', 'android', 'ios', 'bg'])) {
			UploadException::throwException(UploadException::GIFT_UPLOAD_PARAMS_ERROR);
		}
		if ($uploadType == 'list') {
			$fname = $id.'.png';
		} else if ($uploadType == 'start') {
			$fname = $id.'.s.png';
		} else if ($uploadType == 'end') {
			$fname = $id.'.e.png';
		} else if ($uploadType == 'zip') {
			$fname = $id.'.zip';
		} else if ($uploadType == 'webp') {
			$fname = $id.'.webp';
		} else if ($uploadType == 'head') {
			$fname = $id.'.h.png';
		} else if ($uploadType == 'json') {
			$fname = $id.'.json';
		} else if ($uploadType == 'mp4') {
			$fname = $id.'.mp4';
		} else if ($uploadType == 'preview') {
			$fname = $id.'_diy_preview.mp4';
		} else if ($uploadType == 'bg') {
			$fname = 'diy_'.time().mt_rand(100000, 999999).'_bg.mp4';
		} else if ($uploadType == 'android') {
			$zip = new \ZipArchive();
			if ($zip->open($this->context->file->getTempName())) {
				$zip->extractTo('/tmp/diy_android');
				$zip->close();
			}
			$origin = '/tmp/diy_android/'.$id;
			@copy($origin, $this->context->file->getTempName());
			$fname = 'android/'.$id;
		} else if ($uploadType == 'ios') {
			$zip = new \ZipArchive();
			if ($zip->open($this->context->file->getTempName())) {
				$zip->extractTo('/tmp/diy_ios');
				$zip->close();
			}
			$origin = '/tmp/diy_ios/'.$id;
			@copy($origin, $this->context->file->getTempName());
			$fname = 'ios/'.$id;
		}
		$remoteName = "static/gift_big/" . $fname;
		return $remoteName;
    }

    protected function gamehotrenewal($type)
    {
        $prePath = md5(file_get_contents($this->context->file->getTempName()));
        return "game/room/zip/" . $prePath . '/' . $this->context->file->getName();
    }
}
