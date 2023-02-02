<?php

namespace Imee\Service\Lesscode\Logic\Schema;


use Imee\Service\Lesscode\Traits\Curd\ListTrait;
use Phalcon\Di;

class GuidListLogic
{
    use ListTrait;

    public function onGetFilter(&$filter)
    {
        // 只能看到不是系统级的功能
//        $filter['is_system'] = 0;
        $getGuid  = Di::getDefault()->getShared('request')->getQuery('guid');
        $postGuid = Di::getDefault()->getShared('request')->getPost('guid');

        if (!empty($getGuid) && !empty($postGuid) && $getGuid !== $postGuid) {
            $filter['guid'] = $postGuid;
        }
    }

    public function onListFormat(&$item)
    {
        $config = $item['table_config'];
        $config = json_decode($config, true);

        if (empty($config)) {
            return;
        }

        $item['title'] = $config['comment'];
        $item['table_config'] = (string) json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function onAfterList($list): array
    {
        return $list;
    }

    /**
     * 处理用户排序-连表查询
     * @param $orderBy
     */
    public function onOrderBy(&$orderBy): void
    {
        $orderBy = 'id desc';
    }
}