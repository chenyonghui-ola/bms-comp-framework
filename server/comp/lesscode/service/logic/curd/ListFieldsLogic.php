<?php


namespace Imee\Service\Lesscode\Logic\Curd;


use Imee\Helper\Traits\FactoryServiceTrait;

use Imee\Helper\Traits\ResponseInside;
use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Context\GuidContext;
use Imee\Service\Lesscode\FactoryService;
use Imee\Service\Lesscode\Data\SchemaConfigData;
use Imee\Service\Lesscode\FilterService;


/**
 * @property SchemaConfigData schemaConfigData
 * @property FilterService    FilterService
 */
class ListFieldsLogic
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

    protected $schema;

    protected $table;

    protected $filter;

    public function __construct(GuidContext $context)
    {
        $this->context = $context;
    }

    /**
     * 列表配置
     */
    public function handle()
    {
//        $info = $this->schemaConfigData->getInfoByGuid($this->context);
//
//        if (empty($info)) {
//            return self::error('数据异常');
//        }

        $this->schema = new AdapterSchema($this->context->guid);

        $this->table = $this->schema->getTable();
        if (!empty($this->table)) {
            $this->formatTable();
        }

        return $this->table;
    }


    /**
     * 格式化筛选
     * @param $filter
     */
    public function formatTable(): void
    {
        $list = [];

        foreach ($this->table['fields'] as $name => $field) {
            $list[] = [
                'name'      => $name,
                'default'   => $field['default'],
                'comment'   => $field['comment'],
                'component' => $field['component'] ?? '',
            ];
        }

        $this->table = $list;
    }
}