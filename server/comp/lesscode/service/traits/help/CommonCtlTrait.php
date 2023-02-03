<?php


namespace Imee\Service\Lesscode\Traits\Help;

use Imee\Libs\Beanstalkd\Client;
use Imee\Libs\Redis\RedisBase;
use Imee\Libs\Redis\RedisHash;
use Imee\Service\Lesscode\HelperService;

trait CommonCtlTrait
{
    protected function syncExportWork($filePrefix, $cmdStr, $paramData = [])
    {
        $tmpTimeInt = time();
        $adminUid = $this->uid;
        $redis = new RedisHash(RedisBase::REDIS_CACHE);
        if ($this->request->isAjax()) {
            $name = $redis->get(HelperService::getExportRedisKey($cmdStr), $adminUid);
            $isOk = file_exists(ROOT . DS . 'public' . DS . $name . '.csv');
            $fileNamePrefix = defined('LESSCODE_API_PREFIX') ? '/' . LESSCODE_API_PREFIX : '/api';
            if (ENV == 'dev') {
                $fileNamePrefix = '';
            }
            return $this->outputSuccess(['is_ok' => $isOk, 'file_name' => $fileNamePrefix . '/public/' . $name . '.csv']);
        } else {
            $oldName = $redis->get(HelperService::getExportRedisKey($cmdStr), $adminUid);      //先检查30s内是否有刷新
            $oldTime = !empty($oldName) ? substr($oldName, (strlen($filePrefix) + 1), 10) : 0;
            if ($tmpTimeInt - $oldTime <= 30) {
                $randName = md5($adminUid . $oldTime);
                $fileTime = $oldTime;
            } else {
                $randName = md5($adminUid . $tmpTimeInt);
                $fileTime = $tmpTimeInt;
            }
            $fullName = $filePrefix . '_' . $fileTime . '_' . $randName;
            $redis->set(HelperService::getExportRedisKey($cmdStr), [$adminUid => $fullName]);
            if ($tmpTimeInt - $oldTime > 30) {
                $client = new Client();
                $client->choose(HelperService::getExportQueueName());    //使用同一个队列记录,不同导出逻辑根据cmd字符串确定，30s只投递一次
                $mergeData = array_merge($paramData, ['admin_uid' => $adminUid, 'time_int' => $fileTime]);
                $res = $client->set([
                    'cmd' => $cmdStr,
                    'data' => $mergeData,
                ]);
                $client->close();
            }
            $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>导出excel</title>
    <script src="https://xs-image.yinjietd.com/admin/public/jquery-2.1.1.min.js"></script>
</head>
<body>
<p id="loading_id">导出任务正在进行中，请耐心等待，任务完成后会自动显示下载链接...</p>
</body>
<script>
    $(document).ready(function () {
        function update() {
            $.ajax({
                type: 'GET',
                url: window.location.href,
                dataType: "json",
                success: function (jsondata) {
                    if (jsondata.data.is_ok == 1) {
                        str = '导出任务已完成，请尽快下载：' + "<a href='" + jsondata.data.file_name + "' target='_blank'>点此下载</a>";
                        $("#loading_id").html(str);
                    }
                },
            });
        }

        setInterval(function () {
            update();
        }, 5000);
        update();
    });
</script>
</html>
HTML;
            echo $html;
        }
    }
}