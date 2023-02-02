<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Role\Traits;

use Imee\Models\Cms\CmsRoles;
use Phalcon\Di;

trait ExractTreeTrait
{
    private function getAllModuleIds()
    {
        $treeData = @json_decode($this->context->tree, true);
        $allIds = [];
        foreach ($treeData as $treeItem) {
            $this->exractTreeItem($treeItem, $allIds);
        }
        return $allIds;
    }

    /**
     * 获取所有id
     */
    private function exractTreeItem($item, &$ids)
    {
        if (empty($item['checked'])) {
            return;
        }
        $ids[] = $item['id'];
        if (empty($item['children'])) {
            return;
        }
        foreach ($item['children'] as $row) {
            $this->exractTreeItem($row, $ids);
        }
        return;
    }
}
