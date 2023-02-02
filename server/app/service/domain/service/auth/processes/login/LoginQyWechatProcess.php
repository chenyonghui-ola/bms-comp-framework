<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Login;

use Imee\Service\Domain\Context\Auth\Login\LoginQyWechatContext;
use Imee\Service\Sdk\SdkCommonLogin;

class LoginQyWechatProcess
{
    /**
     * @var LoginQyWechatContext
     */
    private $context;

    public function __construct(LoginQyWechatContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        $sdk = new SdkCommonLogin();
        
        $url = $sdk->login();
        

        return ['url' => $url];
    }
}
