<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Login;

use Imee\Exception\Auth\LoginException;
use Imee\Service\Domain\Context\Auth\Login\LoginCallbackContext;
use Imee\Service\Domain\Context\Auth\Staff\UserInfoContext;
use Imee\Service\Domain\Service\Auth\StaffService;
use Imee\Service\Helper;
use Imee\Service\Sdk\SdkCommonLogin;
use Phalcon\Di;

class LoginCallbackProcess
{
    /**
     * @var LoginCallbackContext
     */
    private $context;

    private $staffService;

    private $session;

    public function __construct(LoginCallbackContext $context)
    {
        $this->context      = $context;
        $this->staffService = new StaffService();
        $this->session      = Di::getDefault()->getShared('session');
    }

    private function verify($userInfo)
    {
        if (intval($userInfo['user_status']) == 0) {
            list($code, $errmsg) = LoginException::FORBIDDEN_ERROR;
            throw new LoginException(
                $errmsg,
                $code
            );
        }
    }

    public function handle()
    {
        $this->validation();

        $sdk  = new SdkCommonLogin();
        $data = $sdk->getLoginUserInfoByCode($this->context->ucToken);

        if (empty($data)) {
            [$code, $msg] = LoginException::LOGIN_GET_INFO_ERROR;
            throw new LoginException($msg, $code);
        }

        $data = [
            'user_id' => $data['job_num'],
            'name'    => $data['realname'],
            'email' => $data['company_email'],
        ];
        $user = $this->staffService->saveWechatUser($data);
        if (empty($user)) {
            [$code, $msg] = LoginException::LOGIN_SAVE_ERROR;
            throw new LoginException($msg, $code);
        }

        // 记录企业微信用户登录日志
        $log = [
            'act'  => 'qiye wechat login',
            'time' => date('Y-m-d H:i:s'),
            'ip'   => Helper::ip(),
            'user' => $user,
        ];
        $this->importantLog($log);

        if (isset($user['state']) && $user['state'] == 'guest') {
            [$code, $msg] = LoginException::LOGIN_TOURIST_NO_LOGIN_ERROR;
            throw new LoginException($msg, $code);
        }

        $this->verify($user);

        $purview = $this->staffService->getUserAllAction($user['user_id']);

        $this->session->set('uid', $user['user_id']);
        $this->session->set('purview', $purview);
        $this->session->set('userinfo', $user);
        $this->staffService->modifyLogin();
    }

    
    private function validation()
    {
        if (empty($this->context->ucToken)) {
            [$code, $msg] = LoginException::LOGIN_PLEASE_AGAIN_ERROR;
            throw new LoginException($msg, $code);
        }
    }

    private function importantLog($str)
    {
        Di::getDefault()->getShared('logger')->warning(is_scalar($str) ? $str : json_encode($str));
    }

}
