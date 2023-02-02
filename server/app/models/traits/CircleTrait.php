<?php

namespace Imee\Models\Traits;

use Imee\Comp\Common\Log\LoggerProxy;
use Imee\Models\Ots\OTSXsCircle;

trait CircleTrait
{
    /**
     * 根据联合主键 uid,topic 查询ots朋友圈信息
     * @param $topicList
     * @param string $tableName
     * @return array
     */
    private function getCircleTopicBatchRow($topicList, string $tableName = 'xs_circle_topic'): array
    {
        //组装请求
        $tables = [];
        $primaryKeys = [];
        foreach ($topicList as $topic) {
            $primaryKeys[] = [
                ['uid', (int)$topic['uid']],
                ['topic_id', (int)$topic['topic_id']]
            ];
        }
        $request = [
            'table_name'   => $tableName,
            'primary_keys' => $primaryKeys,
            'max_versions' => 1,
        ];
        $tables[] = $request;
        //请求
        $otsClient = OTSXsCircle::getClient();
        $result = $otsClient->batchGetRow(['tables' => $tables]);
        if (empty($result['tables'])) {
            return [];
        }

        $data = [];
        foreach ($result['tables'] as $tableData) {
            foreach ($tableData['rows'] as $rowData) {
                if ($rowData['is_ok']) {
                    $otsData = [];
                    if (empty($rowData['attribute_columns'])) {
                        continue;
                    }
                    foreach ($rowData['attribute_columns'] as $v) {
                        $otsData[$v[0]] = $v[1];
                    }
                    $rowKeys = [];
                    if (empty($rowData['primary_key'])) {
                        continue;
                    }
                    foreach ($rowData['primary_key'] as $vv) {
                        $rowKeys[] = $vv[1];
                    }
                    $rowKey = implode('', $rowKeys);
                    $data[$rowKey] = $otsData;
                } else {
                    //处理出错
                    LoggerProxy::instance()->warning("getCircleTopicBatchRow error_code:{$rowData['error']['code']} {$rowData['error']['message']}");
                }
            }
        }
        return $data;
    }
}