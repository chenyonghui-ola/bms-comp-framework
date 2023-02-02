<?php

namespace Imee\Service\Domain\Context\Auth\Login;

use Imee\Service\Domain\Context\BaseContext;

class LoginContext extends BaseContext
{
    /**
     * 用户名
     * @var string
     */
    protected $username;

    /**
     * 用户密码
     * @var string
     */
    protected $password;

    /**
     * 二次验证
     * @var string
     */
    protected $repassword;
}
