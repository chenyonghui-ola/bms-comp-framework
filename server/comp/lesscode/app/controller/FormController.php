<?php


namespace Imee\Controller\Lesscode;

use Imee\Controller\AdminBaseController;

use Imee\Controller\BaseController;
use Imee\Service\Lesscode\Context\FormCheckContext;
use Imee\Service\Lesscode\Context\FormCreateContext;

use Imee\Service\Lesscode\Validations\FormCreateValidation;
use Imee\Service\Lesscode\Validations\FormCreateCheckValidation;

use Imee\Service\Lesscode\Schema\FormCreateService;

/**
 * @property FormCreateService formCreateService
 */
class FormController extends BaseController
{
    /**
     * @var array $classMap
     */
    protected $classMap = [
        'formCreateService' => ['class' => FormCreateService::class]
    ];

    protected function onConstruct()
    {
        $this->allowSort = array();
        parent::onConstruct();
    }

    /**
     * @page  form
     * @name 低代码模块-表单设计器
     */
    public function mainAction()
    {

    }

    /**
     * @page  form
     * @name 低代码模块-表单设计器
     * @point 创建表单
     */
    public function createAction()
    {
        FormCreateValidation::make()->validators($this->request->getPost());

        $context = new FormCreateContext($this->request->getPost());
        $service = new FormCreateService($context);

        return $this->outputSuccess($service->handle());
    }

    /**
     * @page  form
     * @name 低代码模块-表单设计器
     * @point 更新表单
     */
    public function updateAction()
    {
        FormCreateValidation::make()->validators($this->request->getPost());

        $context = new FormCreateContext($this->request->getPost());
        $service = new FormCreateService($context);

        return $this->outputSuccess($service->update());
    }

    /**
     * @page  form
     * @point 校验GUID
     */
    public function checkAction()
    {
        FormCreateCheckValidation::make()->validators($this->request->getPost());

        $context = new FormCheckContext($this->request->getPost());
        $service = new FormCreateService($context);

        return $this->outputSuccess($service->check());
    }
}
