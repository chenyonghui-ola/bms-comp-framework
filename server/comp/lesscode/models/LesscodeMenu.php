<?php

namespace Imee\Models\Cms\Lesscode;

class LesscodeMenu extends BaseModel
{
    public static function getInfoByMenu($menuId)
    {
        return self::findFirstByMenuId($menuId);
    }
}