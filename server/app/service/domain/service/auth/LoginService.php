<?php

namespace Imee\Service\Domain\Service\Auth;

use Imee\Service\Domain\Context\Auth\Login\LoginCallbackContext;
use Imee\Service\Domain\Context\Auth\Login\LoginContext;
use Imee\Service\Domain\Context\Auth\Login\LoginQyWechatContext;
use Imee\Service\Domain\Service\Auth\Processes\Login\LoginCallbackProcess;
use Imee\Service\Domain\Service\Auth\Processes\Login\LoginProcess;
use Imee\Service\Domain\Service\Auth\Processes\Login\LoginQyWechatProcess;

/**
 * 权限服务，需考虑后续迁移
 */
class LoginService
{
    public function login(LoginContext $context)
    {
        $process = new LoginProcess($context);
        return $process->handle();
    }

    public function loginQyWechat($params)
    {
        $context = new LoginQyWechatContext($params);
        $process = new LoginQyWechatProcess($context);
        return $process->handle();
    }

    public function loginCallback($params)
    {
        $context = new LoginCallbackContext($params);
        $process = new LoginCallbackProcess($context);
        return $process->handle();
    }
}
