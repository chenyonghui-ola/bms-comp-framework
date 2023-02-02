<?php

namespace Imee\Service;

use Imee\Comp\Common\Log\LoggerProxy;
use Phalcon\Di;

class Helper
{
    public static function getSystemUid()
    {
        $session = Di::getDefault()->getShared('session');

        return $session->get('uid');
    }

    public static function getSystemUidPurview()
    {
        $session = Di::getDefault()->getShared('session');

        return $session->get('purview');
    }

    public static function getSystemUserInfo()
    {
        $session = Di::getDefault()->getShared('session');
        return $session->get('userinfo');
    }

    public static function debugger()
    {
        return LoggerProxy::instance();
    }

    public static function ip($trustForwardedHeader = true)
    {
        if (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^[\d\.]{7,15}$/', $_SERVER['HTTP_X_REAL_IP'], $match)) {
            return $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $ip = Di::getDefault()->getShared('request')->getClientAddress($trustForwardedHeader);
        }
        if (self::isIntranet($ip) && isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //如果是内网ip，这使用代理ip
            $clientIp = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            $realIp = array_shift($clientIp);
            if (preg_match('/^[\d\.]{7,15}$/', $realIp, $match)) {
                return $realIp;
            }
        }
        return $ip;
    }

    public static function isIntranet($ip)
    {
        $ipLong = ip2long($ip);
        if (!$ipLong) {
            return false;
        }

        if (($ipLong & 0xff000000) === 0x0a000000) { //10.0.0.0 - 10.255.255.255
            return true;
        } elseif (($ipLong & 0xfff00000) === 0xac100000) { //172.16.0.0 - 172.31.255.255
            return true;
        } elseif (($ipLong & 0xffff0000) === 0xc0a80000) { //192.168.0.0 - 192.168.255.255
            return true;
        } elseif (strpos($ip, '127.') == 0) {
            return true;
        }

        return false;
    }

    public static function now($time = 0)
    {
        return date('Y-m-d H:i:s', $time ?: time());
    }

    public static function objectFilter($model, $fields)
    {
        if (empty($model) || empty($fields)) {
            return array();
        }
        $array = array();
        foreach ($fields as $key) {
            $array[$key] = $model->$key;
        }
        return $array;
    }

    public static function fetch($sql, array $bind = null, $schema = 'db')
    {
        $conn = Di::getDefault()->getShared($schema);
        return $conn->fetchAll($sql, \Phalcon\Db::FETCH_ASSOC, $bind);
    }

    public static function fetchOne($sql, array $bind = null, $schema = 'db')
    {
        $conn = Di::getDefault()->getShared($schema);
        return $conn->fetchOne($sql, \Phalcon\Db::FETCH_ASSOC, $bind);
    }

    public static function fetchColumn($sql, $schema = 'db')
    {
        $conn = Di::getDefault()->getShared($schema);
        return $conn->fetchColumn($sql);
    }

    public static function exec($sql, $schema = 'cms')
    {
        $conn = Di::getDefault()->getShared($schema);
        if ($conn->execute($sql)) {
            return $conn->affectedRows();
        }
        return 0;
    }

    // 根据appid获取appname
    public static function getAppName($appid, $onlyMap = false)
    {
        $appMap = array(
            '0'  => '汇总',
            '1'  => '伴伴',
            '5'  => 'Partying',
            '9'  => 'Party Star',
            '10' => 'Alloo',
            '11' => 'Veeka'
        );
        if ($onlyMap) {
            return $appMap;
        }

        return $appMap[$appid] ?? '';
    }

    public static function getOtherDateWeekDur($date)
    {
        $w = date('w', $date);
        $t = strtotime(date("Y-m-d", $date));

        $start = 0;
        $end = 0;

        if ($w == 0) {
            $start = $t - (86400 * 4);
            $end = $t + (86400 * 3);
        } elseif ($w == 1) {
            $start = $t - (86400 * 5);
            $end = $t + (86400 * 2);
        } elseif ($w == 2) {
            $start = $t - (86400 * 6);
            $end = $t + (86400 * 1);
        } elseif ($w == 3) {
            $start = $t - (86400 * 7);
            $end = $t;
        } elseif ($w == 4) {
            $start = $t - (86400 * 1);
            $end = $t + (86400 * 6);
        } elseif ($w == 5) {
            $start = $t - (86400 * 2);
            $end = $t + (86400 * 5);
        } elseif ($w == 6) {
            $start = $t - (86400 * 3);
            $end = $t + (86400 * 4);
        }

        return array(
            "start" => $start,
            "end"   => $end
        );
    }

    /**
     * @desc 根据日期获取周时间戳范围
     * @param string $date 日期
     * @return array
     */
    public static function getDateWeekDur($date)
    {
        $w = date('w', $date);
        $t = strtotime(date("Y-m-d", $date));

        $start = 0;
        $end = 0;

        if ($w <= 6 && $w > 1) {
            $start = $t - (($w - 1) * 86400);
            $end = $t + ((7 - $w) * 86400);
        } elseif ($w == 1) {
            $start = $t;
            $end = $t + (86400 * 7);
        } elseif ($w == 0) {
            $start = $t - (86400 * 6);
            $end = $t;
        }
        return array(
            "start" => $start,
            "end"   => $end
        );
    }

    /**
     * @desc 根据日期获取月时间戳范围
     * @param string $date 日期
     * @return array
     */
    public static function getDateMonthDur($date)
    {
        $startd = date('Y-m-01', $date);
        $endd = date('Y-m-d', strtotime("$startd +1 month -1 day"));

        $start = strtotime($startd);
        $end = strtotime($endd);

        return array(
            "start" => $start,
            "end"   => $end
        );
    }

    public static function arrayFilter($arr, $key)
    {
        if (empty($arr)) {
            return [];
        }
        return array_values(array_filter(array_unique(array_column($arr, $key))));
    }


    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 数据脱敏
     * @param string $string 需要脱敏值
     * @param int $start 开始
     * @param int $length 结束
     * @param string $re 脱敏替代符号
     * @return bool|string
     * 例子:
     * dataDesensitization('18811113683', 3, 4); //188****3683
     * dataDesensitization('乐杨俊', 0, -1); //**俊
     */
    public static function dataDesensitization($string, $start = 0, $length = 0, $re = '*')
    {
        if (empty($string)) {
            return '';
        }
        $strarr = [];
        $mbStrlen = mb_strlen($string);
        while ($mbStrlen) {//循环把字符串变为数组
            $strarr[] = mb_substr($string, 0, 1, 'utf8');
            $string = mb_substr($string, 1, $mbStrlen, 'utf8');
            $mbStrlen = mb_strlen($string);
        }
        $strlen = count($strarr);
        $begin = $start >= 0 ? $start : ($strlen - abs($start));
        $end = $last = $strlen - 1;
        if ($length > 0) {
            $end = $begin + $length - 1;
        } elseif ($length < 0) {
            $end -= abs($length);
        }
        for ($i = $begin; $i <= $end; $i++) {
            $strarr[$i] = $re;
        }
        if ($begin > $end || $begin > $last || $end > $last) {
            return false;
        }
        return implode('', $strarr);
    }

    /**
     * 格式化金额数字
     * @param float $number
     * @param int $point
     * @return string
     */
    public static function formatMoney($number, $point = 2)
    {
        return sprintf("%0.{$point}f", $number);
    }

    /**
     * 组装oss访问url
     * @param $url
     * @param bool $isLocal 是否使用内网地址
     * @return mixed|string
     */
    public static function getHeadUrl($url, bool $isLocal = false)
    {
        if (!$url) {
            return '';
        }
        if (preg_match('/(http|https):\/\/.*/is', $url)) {
            return $url;
        }

        $url = ltrim($url, '/');
        if (ENV == 'prod') {
            if ($isLocal) {
                $online = OSS_IMAGE_URL_LOCAL;
            } else {
                $online = OSS_IMAGE_URL_WEB;
            }
            return $online . '/' . $url;
        }

        return OSS_IMAGE_URL_TEST . '/' . $url;
    }

    /**
     * 输出百分比数据
     * @param float $number
     * @param int $point
     * @return string
     */
    public static function percentData($number, $point = 2)
    {
        return sprintf("%0.{$point}f%%", 100 * $number);
    }

    /** 组装sql方便调试
     * @return string
     */
    public static function getSql()
    {
        $args = func_get_args();
        if (!$args) {
            return '';
        }
        $query = $args[0];
        if (!$query) {
            return '';
        }

        $conditions = isset($query['conditions']) ? $query['conditions'] : $query[0];
        $bind = !empty($query['bind']) ? $query['bind'] : [];
        $columns = !empty($query['columns']) ? $query['columns'] : '*';
        $order_by = !empty($query['order']) ? $query['order'] : '';
        $limit = !empty($query['limit']) ? 'limit ' . $query['limit'] : '';
        $offset = !empty($query['offset']) ? 'offset ' . $query['offset'] : '';
        $tbl = !empty($args[1]) && method_exists($args[1], 'getSource') ? $args[1]->getSource() : '__';

        $sql = "select $columns from $tbl" . ($conditions ? ' where' : '');

        if ($bind) {
            // 找出变量，前面添加空格
            $expr = preg_replace('/:([\w-_]+):/', ' :$1:', $conditions);
            $expr = preg_replace('/\({([\w-_]+):array}\)/', ' ({$1:array})', $expr);
            $words = explode(' ', $expr);
            foreach ($words as $word) {
                // 变量替换
                if (preg_match('/^:(\d+):(.*)$/', $word, $match)) { // 时间特殊处理
                    $var_key = $match[1];
                    $var_value = array_key_exists($var_key, $bind) ? $bind[$var_key] : null;
                    $sql .= ' ' . $word;
                } elseif (preg_match('/^:([\w-_]+):(.*)$/', $word, $match)) {
                    $var_key = $match[1];
                    $var_value = array_key_exists($var_key, $bind) ? $bind[$var_key] : null;
                    $sql .= is_null($var_value) ? " is null" : ("'" . trim($var_value, " '") . "'");
                    $sql .= $match[2];
                } elseif (preg_match('/^\(\{([\w-_]+):array\}\)(.*)$/i', $word, $match)) {
                    $var_key = $match[1];
                    $var_value = array_key_exists($var_key, $bind) ? $bind[$var_key] : null;
                    $sql .= is_null($var_value) ? "is null" : ("('" . implode("','", is_array($var_value) ? $var_value : []) . "')");
                    $sql .= $match[2];
                } else {
                    $sql .= ' ' . $word;
                }
            }
        } else {
            $sql .= $conditions;
        }

        if ($order_by) {
            $sql .= " order by $order_by";
        }
        if ($limit) {
            $sql .= " $limit";
        }
        if ($offset) {
            $sql .= " $offset";
        }
        $sql .= ";";
        return $sql;
    }

    public static function debugQuery($query = [], $obj = null, $log_id = '-')
    {
        if (!defined('DEBUG') || !DEBUG) {
            return;
        }
        $sql = is_array($query) ? self::getSql($query, $obj) : $query;
        $sql = str_replace("\n", "", $sql);
        $traces = debug_backtrace();
        $trace_str = '';
        if (!empty($traces[1])) {
            $trace_str = $traces[1]['class'] . $traces[1]['type'] . $traces[1]['function'];
        }
        unset($traces);
        self::debugger()->info('====[' . $log_id . '] ' . $trace_str . ' debugQuery====' . $sql);
    }

    public static function debugInfo($msg = '', $log_id = '')
    {
        if (!defined('DEBUG') || !DEBUG) {
            return;
        }
        $traces = debug_backtrace();
        $trace_str = '';
        if (!empty($traces[1])) {
            $trace_str = $traces[1]['class'] . $traces[1]['type'] . $traces[1]['function'];
        }
        unset($traces);
        self::debugger()->info('====[' . $log_id . '] ' . $trace_str . ' debugInfo====' . $msg);
    }

    /**
     * console
     * @param $data
     * @param bool $isShowMemory
     */
    public static function console($data, bool $isShowMemory = false)
    {
        if (PHP_SAPI != 'cli') {
            return;
        }

        $uuid = Di::getDefault()->getShared('uuid');
        if (true === $isShowMemory) {
            echo '[' . date('Y-m-d H:i:s') . "][$uuid]" . '[' . self::getMemoryUse() . ']' . print_r($data, true) . PHP_EOL;
        } else {
            echo '[' . date('Y-m-d H:i:s') . "][$uuid]" . print_r($data, true) . PHP_EOL;
        }
    }

    public static function getMemoryUse()
    {
        $memory = memory_get_usage() / 1024 / 1024;
        return number_format($memory, 3) . 'M';
    }

    /**
     * 保存日志
     * @param          $data
     * @param string $file
     * @param string $dir
     */
    public static function log($data, $file = 'fsquad_debug.log', $dir = 'fsquad')
    {
        $root = '/tmp/';

        // 不存在目录创建
        @file_put_contents($root . $dir . '_' . $file, '[' . date('Y-m-d H:i:s') . '] ' . print_r($data, true) . PHP_EOL, FILE_APPEND);
    }

    /**
     * 创建唯一id
     * @return string
     */
    public static function createId()
    {
        return md5(uniqid(mt_rand(), true));
    }

    public static function calP90($data = array())
    {
        if (empty($data)) {
            return 0;
        }
        $n = count($data);
        if ($n == 1) {
            return array_pop($data);
        }
        sort($data);
        $b = ($n - 1) * 0.9;
        $i = intval($b);
        $j = $b - $i;
        return sprintf('%.2f', (1 - $j) * $data[$i] + $j * $data[$i + 1]);
    }

    /**
     * 计算p90平均值 ： 比p90值小，再计算平均
     * @param         $p90
     * @param array $data
     * @return  float
     */
    public static function calP90Ave($p90, $data = array())
    {
        if (empty($data)) {
            return 0;
        }

        foreach ($data as $k => $v) {
            if ($v > $p90) {
                unset($data[$k]);
            }
        }
        $count = count($data);
        if (empty($count)) {
            return 0;
        }

        return sprintf('%.2f', array_sum($data) / $count);
    }

    public static function isDate(&$date)
    {
        if (strlen($date) > 10) {
            $date = substr($date, 0, 10);
        }
        return $date && preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $date);
    }

    public static function formatBirthday($birthday)
    {
        if ($birthday <= 0) {
            return '-';
        }

        if (preg_match('/^\d{8}$/i', $birthday)) {
            $result = date('Y-m-d', strtotime($birthday));
        } else {
            $result = date('Y-m-d', $birthday);
        }

        return $result;
    }

    /**
     * task 里面的打日志
     * @param $msg
     */
    public static function tasklog($msg)
    {
        if (IS_CLI) {
            echo "[" . date('Y-m-d H:i:s') . "]" . $msg . "\n";
        } else {
            Helper::debugger()->warning($msg);
        }
    }

    public static function strlen($str)
    {
        return mb_strlen($str, 'utf-8');
    }

    public static function getRoomBackgroundUrl($theme, $ext = 'jpg')
    {
        return "static/background/room_background_{$theme}.{$ext}";
    }

    public static function numberFormat($num, $decimals = 2, $decimal_separator = '.', $thousands_separator = '')
    {
        return number_format($num, $decimals, $decimal_separator, $thousands_separator);
    }

    public static function formatDate($time, $format = 'Y-m-d H:i:s')
    {
        if ($time) {
            return date($format, $time);
        }

        return '';
    }

    /**
     * @desc 根据日期获取周时间戳范围 结尾时间是周日的 23:59:59
     * @param string $date 日期
     * @return array
     */
    public static function getWeekStartAndEndTs($date)
    {
        $w = date('w', $date);
        $t = strtotime(date("Y-m-d", $date));

        $start = 0;
        $end = 0;

        if ($w <= 6 && $w > 1) {
            $start = $t - (($w - 1) * 86400);
            $end = $t + ((7 - $w) * 86400);
        } else if ($w == 1) {
            $start = $t;
            $end = $t + (86400 * 6);
        } else if ($w == 0) {
            $start = $t - (86400 * 6);
            $end = $t;
        }
        return array(
            "start" => $start,
            "end"   => $end + 86399
        );
    }

    /**
     * 检查是否是每周第一天
     * @param $time
     * @return bool
     */
    public static function isWeekStart($time)
    {
        [$stime, $etime] = array_values(self::getWeekStartAndEndTime($time));

        return $time == strtotime(date('Y-m-d', $stime));
    }

    /**
     * 检查是否是每周最后一天
     * @param $time
     * @return bool
     */
    public static function isWeekEnd($time)
    {
        [$stime, $etime] = array_values(self::getWeekStartAndEndTime($time));

        return $time == strtotime(date('Y-m-d', $etime));
    }

    /**
     * 检查是否是每月最后一天
     * @param $time
     * @return bool
     */
    public static function isMonthEnd($time)
    {
        [$stime, $etime] = array_values(self::getMonthStartAndEndTime($time));

        return $time == strtotime(date('Y-m-d', $etime));
    }

    /**
     * @desc 根据日期获取月时间戳范围
     * @param int $date 日期
     * @return array
     */
    public static function getMonthStartAndEndTime($date)
    {
        $startd = date('Y-m-01', $date);
        $endd = date('Y-m-d 23:59:59', strtotime("$startd +1 month -1 day"));

        $start = strtotime($startd);
        $end = strtotime($endd);

        return array(
            "start" => $start,
            "end"   => $end
        );
    }

    /**
     * @desc 根据日期获取周时间戳范围
     * @param int $date 日期
     * @return array
     */
    public static function getWeekStartAndEndTime($date)
    {
        $w = date('N', $date);
        $t = strtotime(date("Y-m-d", $date));

        return array(
            "start" => $t - ($w - 1) * 86400,
            "end"   => $t + (7 - $w) * 86400,
        );
    }

    /**
     * 去除首位双引号
     * @param $str
     * @return false|mixed|string
     */
    public static function removeQuote($str)
    {
        if (preg_match("/^\"/", $str)) {
            $str = substr($str, 1, strlen($str) - 1);
        }
        //判断字符串是否以'"'结束
        if (preg_match("/\"$/", $str)) {
            $str = substr($str, 0, strlen($str) - 1);;
        }
        return $str;
    }

    /**
     * 获取开始和结束时间
     * @param int $baseTime
     * @param int $day
     * @return array
     */
    public static function getDayStartAndEndTime($baseTime, $day = 0)
    {
        $baseTime += 86400 * $day;

        return [
            strtotime(date('Y-m-d 00:00:00', $baseTime)),
            strtotime(date('Y-m-d 23:59:59', $baseTime)),
        ];
    }

    /**
     * 获取周一时间戳
     * @param $time
     * @return int
     */
    public static function getTimeWeekStart($time)
    {
        $w = date('N', $time);
        $t = strtotime(date("Y-m-d", $time));
        return $t - ($w - 1) * 86400;
    }

    /**
     * 判断开始时间是否大于结束时间
     * @param $startTime
     * @param $endTime
     * @return bool
     */
    public static function checkStartAndEndTime($startTime, $endTime): bool
    {
        if (empty($startTime)
            || empty($endTime)
            || $startTime > $endTime) {
            return true;
        }

        return false;
    }
}
