<?php

namespace Imee\Models\Cms;

class CmsRoles extends BaseModel
{
    protected $allowEmptyStringArr = [
        'modify_username',
    ];
    public static $typesArr = [['-1', '大功能'], ['-2', '部门'], ['-3', '小功能']];
}
