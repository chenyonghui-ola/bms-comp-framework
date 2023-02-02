<?php
/**
 * 导出服务守护进程
 */

use Imee\Cli\Libs\Worker;
use Phalcon\Di;

class ExportexlsTask extends CliApp
{
    const EXPORT_SERVICE_MAP = [
        'sensitive.export' => 'Imee\Export\SensitiveExport',// 敏感词导出
    ];

    private function getDbNames(): array
    {
        $databaseConfigs = Di::getDefault()->getShared('config')->database;
        $dbNames = [];
        foreach ($databaseConfigs as $dbName => $config) {
            $dbNames[] = $dbName;
        }
        return $dbNames;
    }

    public function mainAction(array $params = null)
    {
        $this->useStdout();
        $this->_worker = new Worker(EXPORTEXLS_QUEUE_NAME, array($this, '_format'), array(1, 60), false, false);
        $this->_worker->resetDbs($this->getDbNames());
        $this->_worker->setMessageError(array($this, '_onPing'));
        $this->_worker->init();
    }

    public function _onPing($errorMessage = '')
    {
        if (strpos($errorMessage, 'MySQL server has gone away') !== false) {
            $this->_pingMysql($this->getDbNames());
        }
    }

    /**
     * @desc 导出表格功能，多个不同的导出功能，通过cmd里面的参数来选择
     * @param array $data 查询参数
     * @param null $messageId
     * @param null $timestamp
     * @return bool
     */
    public function _format(array $data, $messageId = null, $timestamp = null): bool
    {
        $this->console("cmd " . json_encode($data, JSON_UNESCAPED_UNICODE));

        if (empty($data['cmd']) || empty($data['data'])) {
            return false;
        }

        $this->delFileOld(ROOT . DS . 'public', 3600);
        $cmd = $data['data']['hashkey'] = $data['cmd'];

        if (isset(self::EXPORT_SERVICE_MAP[$cmd])) {
            $class = self::EXPORT_SERVICE_MAP[$cmd];
            call_user_func([new $class, 'handle'], $data['data']);
        }
        return false;
    }

    /**
     * @desc 删除旧的csv文件，按文件夹来删除
     * @param array $dirName 存放csv文件的全路径
     * @param int $timeInt 默认是3600s
     * @return void
     */
    private function delFileOld($dirName = '.', int $timeInt = 3600)
    {
        $d = opendir($dirName);
        while (($file = readdir($d)) !== false) { //循环读出目录下的文件，跳过目录
            if ($file != '.' && $file != '..' && substr($file, -3) == 'csv') {
                $timeTmp = filectime($dirName . DS . $file);
                if ((time() - $timeTmp) >= $timeInt) {
                    @unlink($dirName . DS . $file);
                }
            }
        }
    }
}