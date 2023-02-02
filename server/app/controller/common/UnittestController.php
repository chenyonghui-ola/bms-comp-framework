<?php

namespace Imee\Controller\Common;

use Imee\Controller\BaseController;
use Imee\Service\Helper;

class UnittestController extends BaseController
{
    protected function onConstruct()
    {
        $this->allowSort = array(
            "id"
        );
        parent::onConstruct();
    }

    public function opAction()
    {
        set_time_limit(60);

        $op = $this->request->getQuery('op', 'trim', '');

        if (method_exists($this, $op)) {
            $this->$op();
        } else {
            exit('error this op');
        }
    }

    public function payPredict()
    {
        $date = $this->request->getQuery('date', 'trim', '');
        if (empty($date)) {
            $date = date("Y-m-d", strtotime("-1 day"));
        }
        $service = new PayPredictService();
        $service->downAndSavePayPredictData($date);
    }

    private function execSql()
    {
        if (ENV != 'dev') {
            dd('只有测试环境才行');
        }

        $action = $this->request->getQuery('action', 'trim', '');
        if ($action == 'run') {
            $dbName = $this->request->getPost('db', 'trim', '');//xs
            $sql = $this->request->getPost('sql', 'trim', '');
            if (!$dbName || !$sql) {
                dd('参数不全');
            }
            $result = Helper::fetchOne($sql, null, $dbName);

            dd($result);
        }

        echo <<<EOF
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <form method="post" action="/api/common/unittest/op?op=execSql&action=run">
        输入数据库名：<br/>
        <input type="text" name="db"/><br/>
        输入sql：<br/>
        <textarea rows="20" cols="60" name="sql"></textarea><br/>
        <input type="submit" value="提交">
    </form>
</body>
</html>
EOF;
    }

    private function showTable()
    {
        if (ENV != 'dev') {
            dd('只有测试环境才行');
        }

        $dbName = $this->request->getQuery('db', 'trim', '');//xs
        $tableName = $this->request->getQuery('tb', 'trim', '');//cms_user
        $sql = 'show create table ' . $tableName;
        $result = Helper::fetchOne($sql, null, $dbName);

        dd($result);
    }

    private function cacheLog()
    {
        $pre = ROOT . DS . 'cache/log/';
        $fileName = $this->request->getQuery('file_name', 'trim', '');//admin_debug_2022-11-29.log
        $n = $this->request->getQuery('limit', 'trim', '200');
        $data = $this->_readFileLastLines($pre . $fileName, $n);
        krsort($data);
        echo nl2br(implode("\n", $data));
    }

    private function _readFileLastLines($filename, $n = 200): array
    {
        if (!$fp = fopen($filename, 'r')) {
            dd("打开文件失败，请检查文件路径是否正确，路径和文件名不要包含中文");
        }
        $pos = -2;
        $eof = "";
        $arrStr = [];
        while ($n > 0) {
            while ($eof != "\n") {
                if (!fseek($fp, $pos, SEEK_END)) {
                    $eof = fgetc($fp);
                    $pos--;
                } else {
                    break;
                }
            }
            $arrStr[] = fgets($fp);
            $eof = "";
            $n--;
        }
        return $arrStr;
    }
}