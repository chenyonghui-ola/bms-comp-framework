<?php

namespace Imee\Models\Bi;

use OSS\OssUpload;

/**
 * 非hive类数据下载
 */
class Demo2Bi extends BaseBi
{
    /**
     * 下载文件
     * @param $date
     * @return array
     * @throws \OSS\Core\OssException
     */
    public static function downFile($date): array
    {
        $path = 'recommend/offline_user_prediction_veeka/svip_predict/svip_prediction_results/';

        $bucket = OssUpload::EMR;
        if (ENV == 'dev') {
            $bucket = OssUpload::BUCKET_DEV;
        }
        //下载文件
        self::setLineSeparator("\t");
        $tmpFiles = self::downLoadNotHive($date, $path, $bucket);

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
            'uid', 'predict_type', 'predict_time'
        ];
        //读取文件内容
        return self::readData($tmpFiles, $columns);
    }
}