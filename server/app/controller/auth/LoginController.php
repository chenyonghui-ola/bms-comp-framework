<?php

namespace Imee\Controller\Auth;

use Imee\Controller\Validation\Auth\LoginValidation;
use Imee\Service\Domain\Context\Auth\Login\LoginContext;
use Imee\Service\Domain\Service\Auth\LoginService;
use Imee\Controller\BaseController;

class LoginController extends BaseController
{
    public function indexAction()
    {
        LoginValidation::make()->validators($this->request->getPost());
        
        $context = new LoginContext($this->request->getPost());
        $service = new LoginService();
        $service->login($context);
        
        return $this->outputSuccess();
    }

    public function qwindexAction()
    {
        $params  = $this->request->getPost();
        $service = new LoginService();
        $res     = $service->loginQyWechat($params);

        return $this->redirect($res['url']);
    }

    // 登录回调
    public function callbackAction()
    {
        $params = array_merge($this->request->getQuery(), $this->request->getPost());

        $service = new LoginService();
        $service->loginCallback($params);

        return $this->redirect('/new/');
    }

    public function logoutAction()
    {
        $this->session->remove('uid');
        $this->session->remove('purview');
        $this->session->remove('userinfo');
        return $this->outputSuccess();
    }
}
