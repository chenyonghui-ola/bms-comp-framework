<?php

namespace Imee\Service\Lesscode\Logic\Filter\Mongo;

use Imee\Schema\BaseSchema;
use Imee\Service\Lesscode\Constant\FieldTypeConstant;
use Imee\Service\Lesscode\Context\Filter\GetFilterContext;
use Imee\Service\Lesscode\FilterService;
use Imee\Service\Lesscode\Schema\FieldService;
use Phalcon\Mvc\Model;

class FilterLogic
{
    /**
     * @var GetFilterContext
     */
    protected $context;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var BaseSchema
     */
    protected $schema;

    /**
     * @var array 接收到到参数
     */
    protected $params;

    public function __construct(GetFilterContext $context)
    {
        $this->context = $context;

        $this->model  = $context->model;
        $this->schema = $context->schema;
        $this->params = $context->params;
    }

    /**
     * todo mongo强类型字段转化
     * @return array
     */
    public function handle()
    {
        // 筛选字段
        $filterFields = $this->schema->getListFilter();

        if (empty($filterFields)) {
            return [];
        }

        if (method_exists($this->model, 'fieldConvert')) {
			$this->params = $this->model->fieldConvert($this->params);
		}

        $condition = [];

        foreach ($filterFields as $filterField => $item) {
            if (isset($item['symbol'])) {
                $field = $filterField;
                $symbol = $item['symbol'];
            } else {
                $field  = $filterField;
                $symbol = '';
            }

            $dataField = $field;

            $isDateField = FilterService::isDateTimeField($field);

            if (true === $isDateField && isset($this->params[$field])) {
                $dataField = FilterService::getDateTimeField($field);
            } else {
                if (!isset($this->params[$field])) {
                    continue;
                }
            }

            empty($symbol) && $symbol = FieldService::getDefaultSymbol();

            if ($symbol == FieldTypeConstant::CONDITION_EQ || $symbol == '') {
                $condition[$dataField] = $this->params[$field];
            } else {
                if (isset($condition[$dataField]) && is_array($condition[$dataField])) {

                    $condition[$dataField] = array_merge($condition[$dataField], [$symbol => $this->params[$field]]);
                } else {
                    $condition[$dataField] = [$symbol => $this->params[$field]];
                }
            }
        }

        return $condition;
    }
}