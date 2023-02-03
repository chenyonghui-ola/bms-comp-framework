<?php


namespace Imee\Service\Lesscode\Logic;


use Imee\Helper\Traits\FactoryServiceTrait;

use Imee\Helper\Traits\ResponseTrait;
use Imee\Service\Lesscode\Context\FormCheckContext;
use Imee\Service\Lesscode\Exception\FormException;
use Imee\Service\Lesscode\FactoryService;

use Imee\Service\Lesscode\Data\SchemaConfigData;
use Imee\Service\Lesscode\Schema\SchemaService;
use Imee\Service\Lesscode\Schema\TableService;
use Phalcon\Di;


/**
 * Class FormCreateLogic
 * @package Imee\Service\Lesscode\Logic
 * @property SchemaConfigData schemaConfigData
 */
class FormCheckLogic
{
    use FactoryServiceTrait, ResponseTrait;

    /**
     * 工厂映射
     */
    protected $factorys = [
        FactoryService::class
    ];

    /**
     * @var FormCheckContext
     */
    protected $context;

    public function __construct(FormCheckContext $context)
    {
        $this->context = $context;
    }

    /**
     * 生成必要文件
     */
    public function handle()
    {
        $bool = $this->schemaConfigData->checkGuid($this->context);

        if (false === $bool) {
            throw new FormException(FormException::DATA_EXSITS_ERROR[1], FormException::DATA_EXSITS_ERROR[0]);
        }

        // 查询是否存在表并且解析表
       $res = (new TableService())->parse(Di::getDefault()->getShared('request')->getPost());

        if (isset($res['table']) && !empty($res['table'])) {
            $res = (new SchemaService())->convertSchemaJson($res['table']);
        }

        return $res;
    }

}