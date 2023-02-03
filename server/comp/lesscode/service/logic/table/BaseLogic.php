<?php


namespace Imee\Service\Lesscode\Logic\Table;


use Imee\Schema\AdapterSchema;
use Imee\Schema\BaseSchema;
use Imee\Service\Lesscode\Constant\FieldTypeConstant;
use Imee\Service\Lesscode\Context\TableContext;
use Phalcon\Mvc\Model;

abstract class BaseLogic
{
    /**
     * @var TableContext
     */
    protected $context;

    /**
     * @var Model
     */
    protected $model;       // 模型

    /**
     * @var Model
     */
    protected $modelName;

    /**
     * @var BaseSchema
     */
    protected $schema;
    protected $fields = []; // 字段
    protected $pk;
    protected $indexs;
    protected $uks;
    protected $comment;

    protected $charset; // 字符集
    protected $engine;  // 存储引擎

    protected $sql; // 最终生成的sql

    public function __construct(TableContext $context)
    {
        $this->context = $context;

        $this->schema    = new AdapterSchema($this->context->guid);
        $this->model     = $this->context->modelNamespace;
        $this->modelName = $this->context->model;
    }

    public function setCharset($charset = FieldTypeConstant::DEFAULT_CHARSET)
    {
        $this->charset = $charset;

        return $this;
    }

    public function setEngine($engine = FieldTypeConstant::DEFAULT_ENGINE)
    {
        $this->engine = $engine;

        return $this;
    }

    public function getTableName()
    {
        $tableName = uncamelize($this->modelName);

        // 根据设置的前缀拼表名
        if (substr($this->model::SCHEMA, 0, 3) === 'cms') {
            $tableName = defined('DATABASE_TABLE_PREFIX') ? DATABASE_TABLE_PREFIX . $tableName : $tableName;
        } elseif (substr($this->model::SCHEMA, 0, 3) === 'bms') {
            $tableName = defined('DATABASE_BMS_TABLE_PREFIX') ? DATABASE_BMS_TABLE_PREFIX . $tableName : $tableName;
        } elseif (substr($this->model::SCHEMA, 0, 4) === 'xsst') {
            $tableName = defined('DATABASE_XSST_TABLE_PREFIX') ? DATABASE_XSST_TABLE_PREFIX . $tableName : $tableName;
        }

        return $tableName;
    }

    protected function isTypeNumber($type)
    {
        return in_array($type, [FieldTypeConstant::DEFAULT_TYPE_INT, FieldTypeConstant::DEFAULT_TYPE_FLOAT, FieldTypeConstant::DEFAULT_TYPE_DECIMAL]);
    }

    protected function isTypeText($type)
    {
        return $type == FieldTypeConstant::DEFAULT_TYPE_TEXT;
    }

    protected function getFieldValueDefault($type)
    {
        $default = '';

        switch (strtolower($type)) {
            case FieldTypeConstant::DEFAULT_TYPE_TEXT:
                $default = FieldTypeConstant::DEFAULT_TEXT_VALUE;
                break;

            case FieldTypeConstant::DEFAULT_TYPE_INT:
            case FieldTypeConstant::DEFAULT_TYPE_BIGINT:
            case FieldTypeConstant::DEFAULT_TYPE_FLOAT:
            case FieldTypeConstant::DEFAULT_TYPE_DECIMAL:
            case FieldTypeConstant::TYPE_TINYINT:
            case FieldTypeConstant::TYPE_SMALLINT:
                $default = FieldTypeConstant::DEFAULT_INT_VALUE;
                break;

            case FieldTypeConstant::DEFAULT_TYPE_CHAR:
            case FieldTypeConstant::DEFAULT_TYPE_VARCHAR:
                $default = FieldTypeConstant::DEFAULT_VARCHAR_VALUE;
                break;
        }

        return $default;
    }

    protected function addApostrophe($field, $str = '`')
    {
        if (!is_array($field)) {
            return "{$str}{$field}{$str}";
        }

        foreach ($field as &$item) {
            is_string($item) && $item = "{$str}{$item}{$str}";
        }

        return $field;
    }
}