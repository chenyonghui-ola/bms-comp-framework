<?php

namespace Imee\Service\Domain\Context\Auth\Staff;

use Imee\Service\Domain\Context\BaseContext;

class UserInfoContext extends BaseContext
{
    /**
     * 用户名
     * @var string
     */
    protected $userEmail;

    /**
     * 用户id
     * @var int
     */
    protected $userId;
}
