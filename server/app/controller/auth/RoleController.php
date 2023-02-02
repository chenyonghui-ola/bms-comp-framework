<?php

namespace Imee\Controller\Auth;

use Imee\Controller\BaseController;
use Imee\Controller\Validation\Auth\RoleListValidation;
use Imee\Controller\Validation\Auth\RoleInfoValidation;
use Imee\Controller\Validation\Auth\RoleCreateValidation;
use Imee\Controller\Validation\Auth\RoleModifyValidation;

use Imee\Service\Domain\Context\Auth\Role\ListContext;
use Imee\Service\Domain\Context\Auth\Role\InfoContext;
use Imee\Service\Domain\Context\Auth\Role\CreateContext;
use Imee\Service\Domain\Context\Auth\Role\ModifyContext;
use Imee\Service\Domain\Service\Auth\RoleService;

class RoleController extends BaseController
{
    /**
     * @page role
     * @name 权限系统-角色管理
     * @point 角色列表
     */
    public function indexAction()
    {
        RoleListValidation::make()->validators($this->request->get());
        $context = new ListContext($this->request->get());
        $service = new RoleService();

        $result = $service->getList($context);
        return $this->outputSuccess($result['data'], array('total' => $result['total']));
    }

    /**
     * @page role
     * @point 角色详情
     */
    public function infoAction()
    {
        RoleInfoValidation::make()->validators($this->request->get());
        $context = new InfoContext($this->request->get());
        $service = new RoleService();

        $data = $service->getInfo($context);
        return $this->outputSuccess($data);
    }

    /**
     * @page role
     * @point 角色创建
     */
    public function createAction()
    {
        RoleCreateValidation::make()->validators($this->request->getPost());
        $context = new CreateContext($this->request->getPost());
        $service = new RoleService();

        $service->create($context);
        return $this->outputSuccess();
    }

    /**
     * @page role
     * @point 角色修改
     */
    public function modifyAction()
    {
        RoleModifyValidation::make()->validators($this->request->getPost());
        $context = new ModifyContext($this->request->getPost());
        $service = new RoleService();
        $service->modify($context);
        return $this->outputSuccess();
    }

    /**
     * @page role
     * @point 获取所有角色
     */
    public function allAction()
    {
        $service = new RoleService();
        $data = $service->getAll();
        return $this->outputSuccess($data);
    }
}
