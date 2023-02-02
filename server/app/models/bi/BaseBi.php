<?php

namespace Imee\Model\Bi;

use OSS\OssClient;
use OSS\OssUpload;

/**
 * bi oss数据下载
 */
class BaseBi
{
    public static $lineSeparator = "\001";
    public static $tmpDir = "/tmp/";
    public static $isCheckSuccessFile = true;
    public static $loopTimes = 3;
    private static $useTimes = 0;

    public static function setLineSeparator($str){
        self::$lineSeparator = $str;
    }

    public static function downLoadPredict($date, $path, string $bucket = OssUpload::BUCKET_DEV): array
    {
        if (date('Y-m-d', strtotime($date)) != $date) {
            $date = date('Y-m-d', strtotime($date));
        }
        $dir = $path . "{$date}/";
        self::console('开始链接oss');
        $nupload = new OssUpload($bucket);
        $client = $nupload->client();
        self::console('链接oss成功');

        $data = $client->listObjects($bucket, ['prefix' => $dir]);
        $listData = $data->getObjectList();

        if (!empty($listData)) {
            $isExist = true;
        } else {
            $isExist = false;
        }

        if (!$isExist) {
            sleep(2);
            self::$useTimes += 1;
            $useTimes = self::$useTimes;
            self::console("oss文件第 {$useTimes} 次未获取到，文件路径->" . $dir);

            if ($useTimes >= self::$loopTimes) {
                self::$useTimes = 0;
                return [];
            }

            self::downLoadPredict($date, $path, $bucket);
            return [];
        }
        self::console('文件获取成功，开始保存到本地>>>');
        $fileNum = 0;
        $tmpFiles = [];
        foreach ($listData as $v) {
            $tmpPreName = $v->getKey();
            $tmpE = explode("/", $tmpPreName);
            if (count($tmpE) < 5 || $tmpE[5] == "") {
                continue;
            }
            $fileNum += 1;
            $tmpDirDown = self::$tmpDir . $date . "." . $fileNum . ".log1." . md5($path);
            self::console('保存文件->' . $tmpDirDown);
            $client->getObject($bucket, $dir . $tmpE[5], [OssClient::OSS_FILE_DOWNLOAD => $tmpDirDown]);
            $tmpFiles[] = $tmpDirDown;
        }
        self::console('成功保存文件数->' . $fileNum);
        return $tmpFiles;
    }

    /**
     * 下载的文件，返回本地文件路径数组
     * @param $date
     * @param $path
     * @param string $bucket
     * @return array
     */
    public static function downLoad($date, $path, string $bucket = OssUpload::EMR): array
    {
        if (date('Y-m-d', strtotime($date)) != $date) {
            $date = date('Y-m-d', strtotime($date));
        }
        $dir = $path . "dt={$date}/";

        self::console('开始链接oss');
        $nupload = new OssUpload($bucket);
        $client = $nupload->client();
        self::console('链接oss成功');

        if (self::$isCheckSuccessFile) {
            $isExist = $client->doesObjectExist($bucket, $dir . "_SUCCESS");
        } else {
            //因为koc bi数据那边不能生成_SUCCESS标识文件
            $isExist = $client->doesObjectExist($bucket, $dir . "000000_0");
        }

        if (!$isExist) {
            sleep(2);
            self::$useTimes += 1;
            $useTimes = self::$useTimes;
            self::console("oss文件第 {$useTimes} 次未获取到，文件路径->" . $dir);

            if ($useTimes >= self::$loopTimes) {
                self::$useTimes = 0;
                return [];
            }

            self::downLoad($date, $path, $bucket);
            return [];
        }

        self::console('文件获取成功，开始保存到本地>>>');
        $data = $client->listObjects($bucket, ['prefix' => $dir]);
        $listData = $data->getObjectList();

        $fileNum = 0;
        $tmpFiles = [];
        foreach ($listData as $v) {
            $tmpPreName = $v->getKey();
            $tmpE = explode("/", $tmpPreName);
            if (count($tmpE) < 5 || $tmpE[4] == "" || $tmpE[4] == "_SUCCESS") {
                continue;
            }
            $fileNum += 1;
            $tmpDirDown = self::$tmpDir . $date . "." . $fileNum . ".log1." . md5($path);
            self::console('保存文件->' . $tmpDirDown);
            $client->getObject($bucket, $dir . $tmpE[4], [OssClient::OSS_FILE_DOWNLOAD => $tmpDirDown]);
            $tmpFiles[] = $tmpDirDown;
        }

        self::console('成功保存文件数->' . $fileNum);
        return $tmpFiles;
    }

    /**
     * 迭代获取下载的文件数据
     * @param $tmpFiles
     * @param $columns
     * @return \Generator
     */
    public static function readData($tmpFiles, $columns): \Generator
    {
        foreach ($tmpFiles as $tmpFile) {
            if (!file_exists($tmpFile)) {
                continue;
            }
            $generatorList = self::readLineData($tmpFile, $columns);
            static $dataList = [];
            foreach ($generatorList as $val) {
                $dataList[] = $val;
                if (count($dataList) == 500) {
                    yield $dataList;
                    $dataList = [];
                }
            }
            yield $dataList;
            $dataList = [];
        }
    }

    private static function readLineData($fileName, $columns): \Generator
    {
        self::console('读取临时文件>>>');
        $file = fopen($fileName, "r");
        while (!feof($file)) {
            $fileLine = fgets($file);
            $data = self::packLineData($fileLine, $columns);
            if (!$data) {
                continue;
            }
            yield $data;
        }

        fclose($file);
        self::console('读取完成，删除临时文件');
        @unlink($fileName);
    }

    private static function packLineData($fileLine, $columns): array
    {
        if (!trim($fileLine)) {
            return [];
        }
        //hive里默认分隔符\001，不可见字符
        if ("\001" == self::$lineSeparator) {
            $fileLine = str_replace(",", "_", $fileLine);
        }
        $fileLine = addslashes($fileLine);
        $data = explode(self::$lineSeparator, $fileLine);
        $lineData = [];
        foreach ($data as $k => $v) {
            if (!isset($columns[$k])) {
                continue;
            }
            $lineData[$columns[$k]] = str_replace("'", "\"", addslashes($v));
        }
        return $lineData;
    }

    private static function console($msg)
    {
        if (PHP_SAPI != 'cli') {
            return;
        }

        echo $msg . PHP_EOL;
    }
}