<?php

namespace Imee\Models\Cms\Lesscode;

use Imee\Models\CacheModel;
use Imee\Service\Lesscode\Exception\ModelException;
use Imee\Schema\AdapterSchema;

class AdapterModel extends CacheModel
{
    const SCHEMA = 'cms';

    protected $allowEmptyStringArr = [];

    /**
     * @var AdapterSchema
     */
    protected static $adapterSchema;

    public function initialize()
    {
        if (empty(static::$adapterSchema)) {
            ModelException::throwException(ModelException::SYSTEM_ADAPTER_MODEL_SET_SCHEMA);
        }

        $this->setSource($this->getRealTableName());
        parent::initialize();


        $this->setConnectionService($this->getRealTableSchema());
        $this->allowEmptyStringValues($this->allowEmptyStringArr);
    }

    protected function getRealTableName()
    {
        if (empty(static::$adapterSchema->getTableConfigName())) {
            ModelException::throwException(ModelException::SYSTEM_ADAPTER_MODEL_SET_TABLE_NAME);
        }

        return static::$adapterSchema->getTableConfigName();
    }

    protected function getRealTableSchema()
    {
        if (empty(static::$adapterSchema->getTableConfigSchema())) {
            ModelException::throwException(ModelException::SYSTEM_ADAPTER_MODEL_SET_TABLE_SCHEMA);
        }

        return static::$adapterSchema->getTableConfigSchema();
    }

    public static function setAdapterSchema(AdapterSchema $schema)
    {
        self::$adapterSchema = $schema;
    }
}