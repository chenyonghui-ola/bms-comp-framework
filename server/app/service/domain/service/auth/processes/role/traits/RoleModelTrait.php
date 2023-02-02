<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Role\Traits;

use Imee\Models\Cms\CmsRoles;
use Phalcon\Di;

/**
 * 角色列表
 */
trait RoleModelTrait
{
    protected function formatList($items)
    {
        $format = [];
        if (empty($items)) {
            return $format;
        }

        foreach (CmsRoles::$typesArr as $v) {
            $typesTmp[$v[0]] = $v[1];
        }

        foreach ($items as $item) {
            $tmp = $item->toArray();
            $tmp['types'] = isset($typesTmp[$tmp['types']]) ? $typesTmp[$tmp['types']] : '未知角色';
            $format[] = $tmp;
        }
        
        return $format;
    }
}
