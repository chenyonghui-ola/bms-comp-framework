<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Models\Cms\CmsUser;
use Imee\Models\Xs\XsBigarea;
use Imee\Service\Helper;

/**
 * 用户查询配置信息
 */
class ConfigProcess
{
    public function handle()
    {
        $format = [];
        foreach (CmsUser::$userStatusDisplay as $k => $v) {
            $tmp['label'] = $v;
            $tmp['value'] = $k;
            $format['user_status'][] = $tmp;
        }

        foreach (CmsUser::$isSaltDisplay as $k => $v) {
            $tmp['label'] = $v;
            $tmp['value'] = $k;
            $format['is_salt'][] = $tmp;
        }

        foreach (Helper::getLanguageArr() as $k => $v) {
            $tmp['label'] = $v;
            $tmp['value'] = $k;
            $format['language'][] = $tmp;
        }

        foreach (XsBigarea::getAllNewBigArea() as $k => $v) {
            $tmp['label'] = $v;
            $tmp['value'] = (string)$k;
            $format['bigarea'][] = $tmp;
        }


        return $format;
    }
}
