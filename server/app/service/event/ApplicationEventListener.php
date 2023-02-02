<?php

namespace Imee\Service\Event;

use Phalcon\Events\Event;
use Phalcon\Mvc\Application;

class ApplicationEventListener extends EventListener
{
    public function viewRender(Event $event, Application $application)
    {
        // $di = $application->getDi();
        // $user = $di->getShared('user');
        // $config = $di->getShared('config');
        // $application->view->cfg = $config->define;
        // $application->view->userInfo = $user->getUser();
        // $application->view->isLogined = $user->isLogined();
        // $application->view->controllerName = strtolower($application->dispatcher->getControllerName());
        // $application->view->actionName = strtolower($application->dispatcher->getActionName());
        return true;
    }

    public function beforeSendResponse(Event $event, Application $application)
    {
        $di = $application->getDi();
        $uuid = $di->getShared('uuid');
        $response = $application->response;
        if ($response->getHeaders()->get('Content-Type') === false) {
            $response->setHeader('Content-Type', 'text/html; charset=utf-8');
        }

        $response->setHeader('TRACE_ID', $uuid);

        $config = $this->getActionConfig($application->dispatcher);
        if ($config === false) {
            $this->headerNoCache($response);
            return true;
        }

        //将内容html处理成js模式
        // if(isset($config['ui'])){
        // 	$response->setHeader('Content-Type', 'application/x-javascript');
        // 	$content = $application->response->getContent();
        // 	$content = 'document.write(' . json_encode(preg_replace("/\t/", "", $content)) . ');';
        // 	$application->response->setContent($content);
        // }

        // if(!isset($config['login']) && isset($config['cache'])){
        // 	//对于需要缓存的控制器
        // 	$this->headerCache($response, $config['cache']);
        // 	$content = $application->response->getContent();
        // 	if(!empty($content)) $application->responseCache->set($content, $config['cache']);
        // }
    }
}
