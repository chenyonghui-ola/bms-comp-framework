<?php

namespace Imee\Service\Event;

use Imee\Exception\ApiException;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Imee\Service\Helper;
use Imee\Helper\Traits\ResponseTrait;

class DispatchEventListener extends EventListener
{
    use ResponseTrait;

    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $config = $this->getActionConfig($dispatcher);
        return true;
    }

    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $config = $this->getActionConfig($dispatcher);

        $di = $dispatcher->getDi();
        //$user = $di->getShared('user');
        $response = $di->getShared('response');
        $request = $di->getShared('request');

        if (isset($_SERVER['HTTP_ORIGIN']) && $this->isSafeOrigin($_SERVER['HTTP_ORIGIN'])) {
            // 严格的需要解析$_SERVER['HTTP_ORIGIN'],然后判断域名
            $response->setHeader('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN']);
            $response->setHeader('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, HEAD, OPTIONS');
            $response->setHeader('Access-Control-Max-Age', '600');
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
            $response->setHeader('Access-Control-Allow-Headers', 'x-requested-with,content-type,user-token,User-Language');
            $response->setHeader('Access-Control-Expose-Headers', 'date,CMS-LOGIN');
        }

        if ($config === false) {
            return true;
        }
        return true;
    }

    public function beforeException(Event $event, Dispatcher $dispatcher, $e)
    {
        if ($e instanceof ApiException) {
            echo $this->outputError($e->getCode(), $e->getMsg(), ['detail' => $e->getData()]);
            return false;
        }

        Helper::debugger()->error("[beforeException]" . ($e ? $e->getMessage() : 'no'));
        Helper::debugger()->error("[beforeException]" . ($e ? $e->getTraceAsString() : 'no'));
        if ($e && $e->getCode() == Dispatcher::EXCEPTION_HANDLER_NOT_FOUND) {
            throw new ApiException(ApiException::NO_FOUND_ERROR);
        }
        return false;
    }

    public function beforeNotFoundAction(Event $event, Dispatcher $dispatcher)
    {
        throw new ApiException(ApiException::NO_FOUND_ERROR);
    }

    private function isSafeOrigin($url)
    {
        if (defined('ENV') && ENV == 'dev') {
            return true;
        }

        if (!$url) {
            return false;
        }

        $safe_urls = [
            "/\.iambanban\.com/i",
            "/\.yinjietd\.com/i",
        ];

        foreach ($safe_urls as $safe_url) {
            if (preg_match($safe_url, $url)) {
                return true;
            }
        }

        return false;
    }
}
