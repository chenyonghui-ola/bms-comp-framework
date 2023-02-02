<?php

namespace Imee\Service\Domain\Context\Auth\Staff;

use Imee\Service\Domain\Context\BaseContext;

class SaveWechatUserContext extends BaseContext
{
    /**
     * 用户ids
     * @var string
     */
    protected $userId;

    /**
     * 用户名称
     * @var string
     */
    protected $name;

    /**
     * @var string 用户邮箱
     */
    protected $email;
}
