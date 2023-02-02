<?php

namespace Imee\Models\Cms;

use Imee\Models\Traits\ModelManagerTrait;
use Imee\Models\BaseModel as BModel;
use Imee\Models\Traits\MysqlCollectionTrait;

class BaseModel extends BModel
{
    use MysqlCollectionTrait;
    use ModelManagerTrait;

    const SCHEMA = 'cms';
    const SCHEMA_READ = 'cms';
    protected $isReadWriteSeparation = false;
}
