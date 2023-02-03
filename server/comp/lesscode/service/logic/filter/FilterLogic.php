<?php

namespace Imee\Service\Lesscode\Logic\Filter;

use Imee\Schema\BaseSchema;
use Imee\Service\Lesscode\Constant\FieldTypeConstant;
use Imee\Service\Lesscode\Context\Filter\GetFilterContext;
use Imee\Service\Lesscode\FileService;
use Imee\Service\Lesscode\FilterService;
use Imee\Service\Lesscode\HelperService;
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

    public function handle()
    {
        // 筛选字段
        $filterFields = $this->schema->getListFilter();

        if (empty($filterFields)) {
            return [];
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
            } elseif (isset($item['component']) && HelperService::isRangeComponent($item['component'])) {
                [$startField, $endField] = FilterService::getRangeFields($field);
                if (!isset($this->params[$startField]) && !isset($this->params[$endField]) && !isset($this->params[$field])) {
                    continue;
                }
            } elseif (isset($item['component']) && HelperService::isMultiple($item['component'])) {
                $symbol = 'ins';
            } else {
                if (!isset($this->params[$field])) {
                    continue;
                }
            }

            empty($symbol) && $symbol = FieldService::getDefaultSymbol();

            if (($symbol == FieldTypeConstant::CONDITION_EQ || $symbol == '') && (!isset($item['component']) || !HelperService::isRangeComponent($item['component']))) {
                $condition[$dataField] = $this->params[$field];
            } else {
                if (isset($this->params[$field]) && FilterService::isDate($this->params[$field])) {
                    $this->params[$field] = strtotime($this->params[$field]);
                }

                if (isset($item['component']) && HelperService::isRangeComponent($item['component'])) {
                    // 范围查询
                    if (isset($this->params[$startField])) {
                        $condition[$dataField] = [FieldTypeConstant::CONDITION_EGT, $this->params[$startField]];
                    }

                    if (isset($this->params[$endField])) {
                        $condition[$dataField] = isset($condition[$dataField])
                            ? [$condition[$dataField], [FieldTypeConstant::CONDITION_ELT, $this->params[$endField]]]
                            : [FieldTypeConstant::CONDITION_ELT, $this->params[$endField]];
                    }
                } else {
                    // 时间范围查询
                    if (isset($condition[$dataField]) && is_array($condition[$dataField])) {
                        if (isset($this->params[$field])) {
                            $condition[$dataField] = [$condition[$dataField], [$symbol, $this->params[$field]]];
                        }
                    } else {
                        if (isset($this->params[$field])) {
                            // in not in 查询 兼容导出等可能是逗号分割字符串情况
                            if (in_array(strtolower($symbol), ['ins', 'in', 'nin', 'not in']) && !is_array($this->params[$field])) {
                                $condition[$dataField] = [$symbol, explode(',', $this->params[$field])];
                            } else {
                                $condition[$dataField] = [$symbol, $this->params[$field]];
                            }
                        }
                    }
                }
            }
        }

        return $condition;
    }
}