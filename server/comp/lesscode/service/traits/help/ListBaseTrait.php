<?php

namespace Imee\Service\Lesscode\Traits\Help;

use Imee\Service\Domain\Context\Auth\Staff\InfoContext;
use Imee\Service\Domain\Service\Auth\StaffService;
use Imee\Service\StatusService;

trait ListBaseTrait
{
    public function formatListBase($item)
    {
        if (isset($item['op_uid'])) {
            // 创建人
            $staffService    = new StaffService();
            $context         = new InfoContext(['user_id' => $item['op_uid']]);
            $user            = $staffService->getInfo($context);
            $item['op_name'] = $user ? $user['user_name'] : '';
        }

        return $item;
    }

    public function formatLabelValue($list, $key = null)
    {
        if (empty($list)) {
            return $list;
        }

        $formatRes = [];

        foreach ($list as $k => $v) {
            $formatRes[] = [
                'label' => is_null($key) ? $v : $v[$key],
                'value' => $k,
            ];
        }

        return $formatRes;
    }


    protected function mergeKeyList($list, $merge)
    {
        foreach ($list as $k => $value) {
            $list[$k] = $value + $merge[$k];
        }

        return $list;
    }

    protected function p($data, $isExit = true)
    {
        echo '<pre>';
        print_r($data);
        if (true === $isExit) exit;
    }
}