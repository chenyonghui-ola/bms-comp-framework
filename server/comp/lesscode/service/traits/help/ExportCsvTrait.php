<?php

namespace Imee\Service\Lesscode\Traits\Help;

use Imee\Libs\Redis\RedisBase;
use Imee\Libs\Redis\RedisHash;
use Imee\Service\Lesscode\HelperService;

trait ExportCsvTrait
{
	/**
	 * @desc 根据hash和用户id获取需要生成的文件名
	 * @param  string  $hashKey   rediskey
	 * @param  string  $adminUid  用户uid
	 * @return string
	 */
	protected function getRandFile($hashKey, $adminUid)
	{
		//生成随机文件名
		$redis   = new RedisHash(RedisBase::REDIS_CACHE);
		$nameArr = $redis->get(HelperService::getExportRedisKey($hashKey), $adminUid);
		$file    = '';
		if (is_array($nameArr)) {
			foreach ($nameArr as $k => $v) {
				if ($k == $adminUid) {
					$file = ROOT . DS . 'public' . DS . $v . '.csv';
				}
			}
		} else {
			$file = ROOT . DS . 'public' . DS . $nameArr . '.csv';
		}
		//判断文件
		if (!empty($file)) {
			if (file_exists($file)) {
				@unlink($file);
			}
		}
		return $file;
	}

	/**
	 * @desc 输入一个数组，对数组中的字符串转码，先插入随机字符串，在合并，替换，返回符合csv格式的字符串。如果数组超大，需要拆分调用。
	 * @param  array  $dataArr  二维数组，每个数组的元素键名必须一致，元素若为空，用-替代
	 * @return string 返回格式化的字符串
	 */
	protected function formatCsvTextBatch($dataArr)
	{
		$tmpStr    = '';
		$timeInt   = time();  //生成两个随机字符串做分隔符，分隔符一定和不能和内容重合，不然分割就出问题了.
		$tmpSplit1 = rand(100001, 200000) . $timeInt;
		$tmpSplit2 = rand(200001, 300000) . $timeInt;
		foreach ($dataArr as $k1 => $v1) {
			$tmpStr .= implode("{$tmpSplit1}", $v1) . "{$tmpSplit2}";
		}
		$tmpStr = str_replace(",", "_", $tmpStr);       //csv格式是,分割的，须和内容的,分开.
		$tmpStr = str_replace('"', "_", $tmpStr);       //用户昵称如果有单双引号，会导致表格混乱，须替换
		$tmpStr = str_replace("'", "_", $tmpStr);
		$tmpStr = str_replace("\r", " ", $tmpStr);
		$tmpStr = str_replace(PHP_EOL, " ", $tmpStr);   //内容出现了换行符，须替换成空格，防止表格错乱；字符串中的\r也会被转义成空格，也不能少
		$tmpStr = str_replace("{$tmpSplit1}", ",", $tmpStr);
		$tmpStr = str_replace("{$tmpSplit2}", "\n", $tmpStr);
		$tmpStr = iconv("UTF-8", "gbk//TRANSLIT", $tmpStr);

		return $tmpStr;
	}

	/**
	 * mysql 超时处理
	 * @param $errorMessage
	 * @return bool
	 * @throws \Exception
	 */
	public function mysqlErrorException($errorMessage)
	{
		if (stripos($errorMessage, 'MySQL server has gone away') !== false
			|| stripos($errorMessage, 'Lost connection to MySQL server during query') !== false
			|| stripos($errorMessage, 'Can\'t connect to MySQL server') !== false
			|| stripos($errorMessage, 'Trying to call method exec on a non-object') !== false
		) {
			throw new \Exception($errorMessage);
		}

		return true;
	}
}