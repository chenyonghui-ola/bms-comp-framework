<?php

namespace Imee\Service\Lesscode\Logic\Schema;

use Imee\Schema\AdapterSchema;
use Imee\Service\Domain\Service\Auth\ModulesService;
use Imee\Service\Helper;
use Imee\Service\Domain\Context\Auth\Modules\InfoContext;

class GuidPointBaseLogic
{
    protected function saveModule($info)
    {
        $action = $info->type;

        if (!in_array($action, (new AdapterSchema)->pointArr)) {
            return;
        }

        $list = $this->schemaMenuModel::find([
            'conditions' => 'guid = :guid:',
            'bind'       => ['guid' => $info->guid]
        ]);

        if (!$list->valid()) {
            return;
        }

        $delete = $info->state == 1 ? 0 : 1;

        try {
            $service = new ModulesService();
            foreach ($list as $item) {
                $context = new InfoContext(['module_id' => $item->menu_id, 'format' => 'object']);
                $info    = $service->getInfoById($context);

                if (empty($info)) {
                    continue;
                }

                if ($info->action != $action) {
                    if ($info->action != AdapterSchema::POINT_MAIN || $action != AdapterSchema::POINT_LIST) {
                        continue;
                    }
                }

                if (isset($info->deleted) && $info->deleted != $delete) {
                    $info->deleted = $delete;
                }

                if (isset($info->is_deleted) && $info->is_deleted != $delete) {
                    $info->is_deleted = $delete;
                }

                if ($info->getChangedFields()) {
                    isset($info->modify_time) && $info->modify_time = time();
                    $info->save();
//                    break;
                }
            }
        } catch (\Exception $e) {

        }

    }

    protected function checkUpdatePointConfig()
    {
        try {
            $tableName = (new $this->pointConfigModel)->getSource();
            $sql       = "select id from {$tableName} where id <> point_id";
            $list      = Helper::fetch($sql, null, $this->pointConfigModel::SCHEMA);

            if (empty($list)) {
                return;
            }

            $ids = implode(',', array_column($list, 'id'));
            $sql = "update {$tableName} set point_id = id where id IN ({$ids})";
            Helper::exec($sql, $this->pointConfigModel::SCHEMA);

        } catch (\Exception $e) {

        }
    }
}