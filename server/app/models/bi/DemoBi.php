<?php

namespace Imee\Models\Bi;

/**
 * hive类型数据下载
 */
class DemoBi extends BaseBi
{
    /**
     * 下载文件
     * @param $date
     * @return array
     * @throws \OSS\Core\OssException
     */
    public static function downFile($date): array
    {
        $path = 'dw/dws_out/xx/';

        //下载文件
        $tmpFiles = self::downLoad($date, $path);

        //失败
        if (!$tmpFiles) {
            return [];
        }

        return $tmpFiles;
    }

    /**
     * 读取文件解析数据
     * 迭代返回500条数据
     * @param $tmpFiles
     * @return \Generator
     */
    public static function readFile($tmpFiles): \Generator
    {
        //文件的字段
        $columns = [
            'uid', 'region_name', 'gid1', 'consume_money', 'consume_cnt'
        ];
        //读取文件内容
        return self::readData($tmpFiles, $columns);
    }
}