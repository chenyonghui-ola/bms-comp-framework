<?php

namespace Imee\Models\Cms\Lesscode;


class BmsBaseModel extends LesscodeBase
{
    const SCHEMA = 'bmsdb';

    protected $allowEmptyStringArr = [];

    public function initialize()
    {
        if (defined('DATABASE_BMS_TABLE_PREFIX')) {
            $this->setSource($this->getRealTableName());
        }
        parent::initialize();

        $schema = defined('LESSCODE_BMS_DATABASE_SCHEMA') ? LESSCODE_BMS_DATABASE_SCHEMA : static::SCHEMA;

        $this->setConnectionService($schema);
        $this->allowEmptyStringValues($this->allowEmptyStringArr);
    }

    protected function getRealTableName()
    {
        return DATABASE_BMS_TABLE_PREFIX . $this->getSource();
    }
}
