<?php

namespace Imee\Service\Domain\Service\Auth;

use Imee\Service\Domain\Context\Auth\Staff\ModuleContext;
use Imee\Service\Domain\Service\Auth\Processes\Staff\MenuProcess;
use Imee\Service\Domain\Service\Auth\Processes\Staff\UserAllActionProcess;
use Imee\Service\Domain\Service\Auth\Processes\Staff\UserInfoProcess;
use Imee\Service\Domain\Context\Auth\Staff\UserInfoContext;
use Imee\Service\Domain\Context\Auth\Staff\ListContext;
use Imee\Service\Domain\Service\Auth\Processes\Staff\ListProcess;

use Imee\Service\Domain\Context\Auth\Staff\InfoContext;
use Imee\Service\Domain\Service\Auth\Processes\Staff\InfoProcess;

use Imee\Service\Domain\Context\Auth\Staff\CreateContext;
use Imee\Service\Domain\Service\Auth\Processes\Staff\CreateProcess;

use Imee\Service\Domain\Context\Auth\Staff\ModifyContext;
use Imee\Service\Domain\Service\Auth\Processes\Staff\ModifyProcess;

use Imee\Service\Domain\Context\Auth\Staff\ShowSaltContext;
use Imee\Service\Domain\Service\Auth\Processes\Staff\ShowSaltProcess;

use Imee\Service\Domain\Service\Auth\Processes\Staff\ConfigProcess;

use Imee\Service\Domain\Context\Auth\Staff\BaseInfosContext;
use Imee\Service\Domain\Service\Auth\Processes\Staff\BaseInfosProcess;

use Imee\Service\Domain\Service\Auth\Processes\Staff\UserInfoByPermissionProcess;

use Imee\Service\Domain\Service\Auth\Processes\Staff\AllStaffPorcess;

use Imee\Service\Domain\Service\Auth\Processes\Staff\ModifyLoginProcess;

use Imee\Service\Domain\Context\Auth\Staff\SaveWechatUserContext;
use Imee\Service\Domain\Service\Auth\Processes\Staff\SaveWechatUserProcess;

/**
 * 权限服务，需考虑后续迁移
 */
class StaffService
{
    /**
     * 获取用户对应项目的所有权限
     */
    public function getUserAllAction($userId)
    {
        $userInfoContext = new UserInfoContext([
            'userId' => $userId,
        ]);
        
        $process = new UserAllActionProcess();
        return $process->handle($this->getUserInfo($userInfoContext));
    }

    /**
     * 获取用户基本信息
     */
    public function getUserInfo(UserInfoContext $context)
    {
        $process = new UserInfoProcess($context);
        return $process->handle();
    }

    public function getList(ListContext $context)
    {
        $process = new ListProcess($context);
        return $process->handle();
    }

    public function getAllStaff()
    {
        $process = new AllStaffPorcess();
        return $process->handle();
    }

    public function getInfo(InfoContext $context)
    {
        $userInfoContext = new UserInfoContext($context->toArray());
        $process = new InfoProcess($this->getUserInfo($userInfoContext));
        return $process->handle();
    }

    /**
     * 创建
     */
    public function create(CreateContext $context)
    {
        $process = new CreateProcess($context);
        return $process->handle();
    }

    public function modify(ModifyContext $context)
    {
        $process = new ModifyProcess($context);
        return $process->handle();
    }

    public function modifyLogin()
    {
        $process = new ModifyLoginProcess();
        return $process->handle();
    }

    public function showSalt(ShowSaltContext $context)
    {
        $userInfoContext = new UserInfoContext($context->toArray());
        $process = new ShowSaltProcess($this->getUserInfo($userInfoContext));

        return $process->handle();
    }

    /** 获取用户权限
     * @param ModuleContext $moduleContext
     * @return array[]
     */
    public function getMenu(ModuleContext $moduleContext): array
    {
        $process = new MenuProcess();
        return $process->handleMenu($moduleContext);
    }

    public function getPermission(): array
    {
        $process = new MenuProcess();
        return $process->handPermission();
    }

    public function getConfig()
    {
        $process = new ConfigProcess();

        return $process->handle();
    }

    public function getStaffBaseInfos(BaseInfosContext $context)
    {
        $process = new BaseInfosProcess($context);

        return $process->handle();
    }

    /**
     * 获取指定权限的用户
     */
    public function getUserInfoByPermission($controller, $action)
    {
        $process = new UserInfoByPermissionProcess($controller, $action);
        return $process->handle();
    }

    /**
     * 保存企业微信登录用户
     * @param $params
     * @return mixed
     */
    public function saveWechatUser($params)
    {
        $context = new SaveWechatUserContext($params);
        $process = new SaveWechatUserProcess($context);
        return $process->handle();
    }

    public function getInfoByUid($uid)
    {
        $userInfoContext = new UserInfoContext(['user_id' => $uid]);
        $process = new InfoProcess($this->getUserInfo($userInfoContext));
        return $process->handle();
    }
}
