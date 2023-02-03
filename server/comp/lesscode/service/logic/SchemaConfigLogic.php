<?php


namespace Imee\Service\Lesscode\Logic;


use Imee\Helper\Traits\FactoryServiceTrait;

use Imee\Helper\Traits\ResponseInside;
use Imee\Service\Lesscode\Context\GuidContext;
use Imee\Service\Lesscode\FactoryService;
use Imee\Service\Lesscode\Data\SchemaConfigData;


/**
 * @property SchemaConfigData schemaConfigData
 */
class SchemaConfigLogic
{
    use FactoryServiceTrait, ResponseInside;

    /**
     * 工厂映射
     */
    protected $factorys = [
        FactoryService::class
    ];

    /**
     * @var GuidContext
     */
    protected $context;

    public function __construct(GuidContext $context)
    {
        $this->context = $context;
    }

    /**
     * 生成必要文件
     */
    public function handle()
    {
        $info = $this->schemaConfigData->getInfoByGuid($this->context);

        $json = $info['schema_json'];
        $arr  = json_decode($json, true);

        foreach ($arr['schema']['properties'] as &$item) {
            if(!isset($item['properties']) || !is_array($item['properties'])){
                continue;
            }

            foreach ($item['properties'] as &$value) {
                if (isset($value['x-component-props']) && is_array($value['x-component-props']) && empty($value['x-component-props'])) {
                    $value['x-component-props'] = new \stdClass();
                }

                if (isset($value['x-decorator-props']) && is_array($value['x-decorator-props']) && empty($value['x-decorator-props'])) {
                    $value['x-decorator-props'] = new \stdClass();
                }
            }
        }

        return self::success($arr);
    }
}