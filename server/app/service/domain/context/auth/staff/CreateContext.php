<?php

namespace Imee\Service\Domain\Context\Auth\Staff;

use Imee\Service\Domain\Context\BaseContext;

class CreateContext extends BaseContext
{
    /**
     * 用户名称
     * @var string
     */
    protected $userName;

    /**
     * 用户邮箱地址
     * @var string
     */
    protected $userEmail;

    /**
     * 用户密码
     * @var string
     */
    protected $password;

    /**
     * 用户状态
     * @var int
     */
    protected $userStatus;

    /**
     * 角色
     * @var array
     */
    protected $roleIds;

    /**
     * appIds
     * @var array
     */
    protected $appIds;

    /**
     * 系统
     * @var array
     */
    protected $systemIds;

    /**
     * @var array []int大区
     */
    protected $bigarea;

    /**
     * @var array []string语言
     */
    protected $language;
}
