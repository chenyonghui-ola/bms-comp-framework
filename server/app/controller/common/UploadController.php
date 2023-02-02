<?php

namespace Imee\Controller\Common;

use Imee\Controller\BaseController;
use Imee\Service\Domain\Context\Common\Upload\ImageUploadContext;
use Imee\Service\Domain\Context\Common\Upload\VideoUploadContext;
use Imee\Service\Domain\Context\Common\Upload\VoiceUploadContext;
use Imee\Service\Domain\Context\Common\Upload\FileUploadContext;
use Imee\Service\Domain\Service\Common\UploadService;

/**
 * 上传类
 */
class UploadController extends BaseController
{
    public $params;

    public function onConstruct()
    {
        parent::onConstruct();
        $get = $this->request->getQuery();
        $post = $this->request->getPost();
        $this->params = array_merge(
            ['request' => $this->request, 'admin_id' => $this->uid, 'app_id' => APP_ID],
            $get,
            $post
        );
    }

    public function imageAction()
    {
        $params = array_merge(
            $this->params,
            ['action' => UploadService::ACTION_IMAGE]
        );
        $context = new ImageUploadContext($params);
        $service = new UploadService($context);
        return $this->outputSuccess($service->handle());
    }

    public function videoAction()
    {
        $params = array_merge(
            $this->params,
            ['action' => UploadService::ACTION_VIDEO]
        );
        $context = new VideoUploadContext($params);
        $service = new UploadService($context);

        return $this->outputSuccess($service->handle());
    }

    public function voiceAction()
    {
        $params = array_merge(
            $this->params,
            ['action' => UploadService::ACTION_VOICE]
        );

        $context = new VoiceUploadContext($params);
        $service = new UploadService($context);

        return $this->outputSuccess($service->handle());
    }

    public function fileAction()
    {
        $params = array_merge(
            $this->params,
            ['action' => UploadService::ACTION_FILE]
        );
        $context = new FileUploadContext($params);
        $service = new UploadService($context);

        return $this->outputSuccess($service->handle());
    }
}
