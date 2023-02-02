<?php

namespace Imee\Models\Cms\Lesscode;

use Imee\Helper\Traits\ModelAutoTimeTrait;
use Imee\Helper\Traits\ModelCurdTrait;

class LesscodeSchemaPoint extends BaseModel
{
    use ModelCurdTrait, ModelAutoTimeTrait;

    protected $allowEmptyStringArr = ['logic'];

    const STATE_OPEN = 1;
    const STATE_CLOSE = 0;

    public static function getInfoByGuidAndType($guid, $type)
    {
        return self::findFirst([
            'conditions' => 'guid = :guid: and type = :type:',
            'bind' => ['guid' => $guid, 'type' => $type]
        ]);
    }
}