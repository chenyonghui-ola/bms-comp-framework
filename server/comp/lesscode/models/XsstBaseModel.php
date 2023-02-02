<?php

namespace Imee\Models\Cms\Lesscode;

class XsstBaseModel extends LesscodeBase
{
    const SCHEMA = 'xsstdb';
    const SCHEMA_READ = 'xsstdbs2';

    protected $allowEmptyStringArr = [];

    public function initialize()
    {
        if (defined('DATABASE_XSST_TABLE_PREFIX')) {
            $this->setSource($this->getRealTableName());
        }
        parent::initialize();

        $schema = defined('LESSCODE_XSST_DATABASE_SCHEMA') ? LESSCODE_XSST_DATABASE_SCHEMA : static::SCHEMA;
        $schemaRead = defined('LESSCODE_XSST_SLAVE_DATABASE_SCHEMA') ? LESSCODE_XSST_SLAVE_DATABASE_SCHEMA : static::SCHEMA_READ;

//        $this->setConnectionService($schema);
        $this->setReadConnectionService($schema);
        $this->setWriteConnectionService($schemaRead);
        $this->allowEmptyStringValues($this->allowEmptyStringArr);
    }

    protected function getRealTableName()
    {
        return DATABASE_XSST_TABLE_PREFIX . $this->getSource();
    }
}
