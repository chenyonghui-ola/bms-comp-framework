<?php

namespace Imee\Service\Lesscode;

use Imee\Schema\AdapterSchema;

class ExportService
{
    public static function getListFields()
    {
        $header = [];

        // 获取当前列表字段
        $table = AdapterSchema::getInstance([])->getTable();
        $fields = $table['fields'] ?? [];

        if (empty($fields)) {
            return $header;
        }

        $listConfig = AdapterSchema::getInstance([])->getListConfig();

        foreach ($fields as $key => $field)
        {
            // 隐藏的字段不展示
            if (isset($listConfig[$key]) && isset($listConfig[$key]['hidden']) && $listConfig[$key]['hidden'] == 1) {
                continue;
            }

            // 上传组件也不导出
            if (isset($listConfig[$key]) && isset($listConfig[$key]['component']) && HelperService::isUpload($listConfig[$key]['component'])) {
                continue;
            }

            $header[$key] = $field['comment'] ?? $key;
        }

        return $header;
    }
}