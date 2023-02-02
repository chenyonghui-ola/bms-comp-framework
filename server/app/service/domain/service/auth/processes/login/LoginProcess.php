<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Login;

use Imee\Libs\Google2FA;
use Imee\Service\Domain\Context\Auth\Login\LoginContext;
use Imee\Service\Domain\Service\Auth\StaffService;
use Imee\Service\Domain\Context\Auth\Staff\UserInfoContext;
use Imee\Exception\Auth\LoginException;
use Phalcon\Di;

/**
 * 获取用户对应项目的所有权限
 */
class LoginProcess
{
    private $context;
    private $staffService;
    public function __construct(LoginContext $context)
    {
        $this->context = $context;
        $this->staffService = new StaffService();
    }

    private function verify($userInfo)
    {
        if (empty($userInfo) || strtolower($userInfo['password']) !== strtolower(md5($this->context->password))) {
            list($code, $errmsg) = LoginException::ACCOUNT_ERROR;
            throw new LoginException(
                $errmsg,
                $code
            );
        }

        if (ENV == 'prod' && intval($userInfo['is_salt']) > 0 &&
            !Google2FA::verify_key($userInfo['salt'], $this->context->repassword)) {
            list($code, $errmsg) = LoginException::REPASSWORD_ERROR;
            throw new LoginException(
                $errmsg,
                $code
            );
        }

        if (intval($userInfo['user_status']) == 0) {
            list($code, $errmsg) = LoginException::FORBIDDEN_ERROR;
            throw new LoginException(
                $errmsg,
                $code
            );
        }
    }

    private function getUserInfo()
    {
        $userInfoContext = new UserInfoContext([
            'user_email' => $this->context->username
        ]);

        return $this->staffService->getUserInfo($userInfoContext);
    }

    public function handle()
    {
        $session = Di::getDefault()->getShared('session');
        $uid = $session->get('uid');
        if ($uid > 0) {
            return;
        }
        
        $userInfo = $this->getUserInfo();
        $this->verify($userInfo);
       
        $purview = $this->staffService->getUserAllAction($userInfo['user_id']);
        $session->set('uid', $userInfo['user_id']);
        
        $session->set('purview', $purview);
        $session->set('userinfo', $userInfo);
        $this->staffService->modifyLogin();
    }
}
