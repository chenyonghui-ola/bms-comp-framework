<?php

namespace Imee\Controller\Auth;

use Imee\Controller\BaseController;

use Imee\Controller\Validation\Auth\ModulesListValidation;
use Imee\Controller\Validation\Auth\ModulesSearchValidation;
use Imee\Controller\Validation\Auth\ModulesInfoValidation;
use Imee\Controller\Validation\Auth\ModulesCreateValidation;
use Imee\Controller\Validation\Auth\ModulesModifyValidation;
use Imee\Controller\Validation\Auth\ModulesRemoveValidation;
use Imee\Controller\Validation\Auth\ModulesPointListValidation;

use Imee\Service\Domain\Context\Auth\Modules\SearchContext;
use Imee\Service\Domain\Context\Auth\Modules\PointListContext;
use Imee\Service\Domain\Context\Auth\Modules\InfoContext;
use Imee\Service\Domain\Context\Auth\Modules\CreateContext;
use Imee\Service\Domain\Context\Auth\Modules\ModifyContext;
use Imee\Service\Domain\Context\Auth\Modules\RemoveContext;
use Imee\Service\Domain\Context\Auth\Modules\ModulePointContext;
use Imee\Service\Domain\Service\Auth\ModulesService;

class ModulesController extends BaseController
{
    /**
     * @page modules
     * @name 权限系统-系统模块
     * @point 列表
     */
    public function indexAction()
    {
        ModulesListValidation::make()->validators($this->request->get());
        $service = new ModulesService();

        $data = $service->getList();
        return $this->outputSuccess($data);
    }

    /**
     * @page modules
     * @point point列表
     */
    public function pointAction()
    {
        ModulesPointListValidation::make()->validators($this->request->get());
        $service = new ModulesService();
        $context = new PointListContext($this->request->get());

        $data = $service->getPointList($context);
        return $this->outputSuccess($data);
    }

    /**
     * @page modules
     * @point 搜索
     */
    public function searchAction()
    {
        ModulesSearchValidation::make()->validators($this->request->get());
        $service = new ModulesService();
        $context = new SearchContext([
            'dir' => dirname(__DIR__),
            'page' => $this->request->get('page'),
        ]);


        $data = $service->search($context);
        return $this->outputSuccess($data);
    }

    /**
     * @page modules
     * @point 详情
     */
    public function infoAction()
    {
        ModulesInfoValidation::make()->validators($this->request->get());
        $context = new InfoContext(
            array_merge($this->request->get(), ['dir' => dirname(__DIR__)])
        );
        $service = new ModulesService();

        $data = $service->getInfo($context);
        return $this->outputSuccess($data);
    }

    /**
     * @page modules
     * @point 创建
     */
    public function createAction()
    {
        ModulesCreateValidation::make()->validators($this->request->getPost());
        $modulePointContexts = [];
        if (!empty($this->request->getPost('points'))) {
            foreach ($this->request->getPost('points') as $point) {
                $modulePointContexts[] = new ModulePointContext($point);
            }
        }

        $context = new CreateContext(
            array_merge(
                $this->request->getPost(),
                [
                    'modulePointContexts' => $modulePointContexts,
                    'dir' => dirname(__DIR__),
                ]
            )
        );
        $service = new ModulesService();

        $service->create($context);
        return $this->outputSuccess();
    }

    /**
     * @page modules
     * @point 修改
     */
    public function modifyAction()
    {
        ModulesModifyValidation::make()->validators($this->request->getPost());
        $modulePointContexts = [];
        if (!empty($this->request->getPost('points'))) {
            foreach ($this->request->getPost('points') as $point) {
                $modulePointContexts[] = new ModulePointContext($point);
            }
        }

        $context = new ModifyContext(
            array_merge(
                $this->request->getPost(),
                [
                    'modulePointContexts' => $modulePointContexts,
                    'dir' => dirname(__DIR__),
                ]
            )
        );
        $service = new ModulesService();
        $service->modify($context);
        return $this->outputSuccess();
    }

    /**
     * @page modules
     * @point 移除
     */
    public function removeAction()
    {
        ModulesRemoveValidation::make()->validators($this->request->getPost());
        $context = new RemoveContext($this->request->getPost());
        $service = new ModulesService();
        $service->remove($context);
        return $this->outputSuccess();
    }
}
