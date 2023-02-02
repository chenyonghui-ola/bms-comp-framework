<?php

namespace Imee\Controller\Auth;

use Imee\Controller\BaseController;
use Imee\Controller\Validation\Auth\StaffListValidation;
use Imee\Controller\Validation\Auth\StaffInfoValidation;
use Imee\Controller\Validation\Auth\StaffCreateValidation;
use Imee\Controller\Validation\Auth\StaffModifyValidation;
use Imee\Controller\Validation\Auth\StaffShowSaltValidation;

use Imee\Service\Domain\Context\Auth\Staff\ListContext;
use Imee\Service\Domain\Context\Auth\Staff\InfoContext;
use Imee\Service\Domain\Context\Auth\Staff\CreateContext;
use Imee\Service\Domain\Context\Auth\Staff\ModifyContext;
use Imee\Service\Domain\Context\Auth\Staff\ModuleContext;
use Imee\Service\Domain\Context\Auth\Staff\ShowSaltContext;
use Imee\Service\Domain\Service\Auth\StaffService;

use Imee\Service\Lesscode\Context\Common\DataContext;
use Imee\Service\Lesscode\MenuService;

class StaffController extends BaseController
{
    /**
     * @page staff
     * @name 权限系统-用户管理
     * @point 后台用户列表
     */
    public function indexAction()
    {
        $service = new StaffService();
        $c = $this->request->get('c');
        if ($c && $c == 'config') {
            return $this->outputSuccess($service->getConfig());
        }
        StaffListValidation::make()->validators($this->request->get());
        $context = new ListContext($this->request->get());

        $result = $service->getList($context);
        return $this->outputSuccess($result['data'], array('total' => $result['total']));
    }

    /**
     * @page staff
     * @point 后台用户详情
     */
    public function infoAction()
    {
        StaffInfoValidation::make()->validators($this->request->get());
        $context = new InfoContext($this->request->get());
        $service = new StaffService();

        $data = $service->getInfo($context);
        return $this->outputSuccess($data);
    }

    /**
     * @page staff
     * @point 后台用户创建
     */
    public function createAction()
    {
        StaffCreateValidation::make()->validators($this->request->getPost());
        $context = new CreateContext($this->request->getPost());
        $service = new StaffService();

        $service->create($context);
        return $this->outputSuccess();
    }

    /**
     * @page staff
     * @point 后台用户修改
     */
    public function modifyAction()
    {
        StaffModifyValidation::make()->validators($this->request->getPost());
        $context = new ModifyContext($this->request->getPost());
        $service = new StaffService();
        $service->modify($context);
        return $this->outputSuccess();
    }

    /**
     * @page staff
     * @point 二次验证
     */
    public function showSaltAction()
    {
        StaffShowSaltValidation::make()->validators($this->request->get());
        $context = new ShowSaltContext($this->request->get());
        $service = new StaffService();
        $service->showSalt($context);
    }

    public function menuAction()
    {
        $context = new ModuleContext(array_merge($this->request->get(), ['lang' => $this->lang]));
        $service = new StaffService();
        $role = $service->getMenu($context);

        // 增加低代码标识
        if (is_file(ROOT . DS . 'comp/lesscode/service/MenuService.php')) {
            $menuService = new MenuService();
            $role        = $menuService->attach(new DataContext(['data' => $role]));
        }

        $userinfo = $this->session->get('userinfo');
        $result   = array(
            'user_id'     => $userinfo['user_id'] ?? '',
            'user_name'   => $userinfo['user_name'] ?? '',
            'user_email'  => $userinfo['user_email'] ?? '',
            'user_status' => $userinfo['user_status'] ?? '',
        );

        return $this->outputSuccess(array_merge($role, $result));
    }

    public function permissionAction()
    {
        $service    = new StaffService();
        $permission = $service->getPermission();
        return $this->outputSuccess($permission);
    }
}
