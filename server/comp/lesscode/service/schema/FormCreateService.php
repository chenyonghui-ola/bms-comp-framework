<?php


namespace Imee\Service\Lesscode\Schema;


use Imee\Service\BaseService;
use Imee\Service\Lesscode\FactoryService;

use Imee\Service\Lesscode\Context\BaseContext;
use Imee\Service\Lesscode\Context\FormCreateContext;

/**
 * @property \Imee\Service\Lesscode\Logic\FormCheckLogic formCheckLogic
 */
class FormCreateService extends BaseService
{
    /**
     * 工厂映射
     */
    protected $factorys = [
        FactoryService::class
    ];

    /**
     * @var FormCreateContext
     */
    protected $context;

    public function __construct(BaseContext $context)
    {
        parent::__construct();

        $this->context = $context;
    }

    /**
     * 生成必要文件
     */
    public function handle()
    {
        return FactoryService::get('formCreateLogic', $this->context)->handle();
    }

    /**
     * 生成必要文件
     */
    public function update()
    {
        return FactoryService::get('formUpdateLogic', $this->context)->handle();
    }

    public function check()
    {
        return FactoryService::get('formCheckLogic', $this->context)->handle();
    }
}