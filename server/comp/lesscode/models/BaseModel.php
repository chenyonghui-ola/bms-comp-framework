<?php

namespace Imee\Models\Cms\Lesscode;

use Imee\Models\CacheModel;
use Imee\Service\Lesscode\Exception\ModelException;

class BaseModel extends LesscodeBase
{
    const SCHEMA = 'cms';

    protected $allowEmptyStringArr = [];

    public function initialize()
    {
        if (defined('DATABASE_TABLE_PREFIX') || defined('DATABASE_TABLE_LESSCODE_PREFIX')) {
            $this->setSource($this->getRealTableName());
        }
        parent::initialize();

        $schema = defined('LESSCODE_BASE_DATABASE_SCHEMA') ? LESSCODE_BASE_DATABASE_SCHEMA : static::SCHEMA;

        $this->setConnectionService($schema);
        $this->allowEmptyStringValues($this->allowEmptyStringArr);
    }

    protected function getRealTableName()
    {
        $prefix = defined('DATABASE_TABLE_LESSCODE_PREFIX') ? DATABASE_TABLE_LESSCODE_PREFIX : DATABASE_TABLE_PREFIX;
        return $prefix . $this->getSource();
    }
}
