<?php

namespace Imee\Service\Domain\Context\Auth\Staff;

use Imee\Service\Domain\Context\PageContext;

/**
 * 后台用户列表
 */
class ListContext extends PageContext
{
    protected $sort = 'user_id';

    protected $dir = 'desc';

    /**
     * 用户名
     * @var string
     */
    protected $userName;

    /**
     * 用户id
     * @var int
     */
    protected $userId;

    /**
     * 用户有效状态
     * @var int
     */
    protected $userStatus;

    /**
     * 是否有二次验证
     * @var int
     */
    protected $isSalt;
}
