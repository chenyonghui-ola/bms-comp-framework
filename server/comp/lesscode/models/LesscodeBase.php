<?php

namespace Imee\Models\Cms\Lesscode;

use Imee\Models\CacheModel;

class LesscodeBase extends CacheModel
{
    public static function getTableFields(): array
    {
        return array_keys((new static())->toArray());
    }

    public static function hasTableField($field): bool
    {
        $fields = static::getTableFields();
        return in_array($field, $fields);
    }
}