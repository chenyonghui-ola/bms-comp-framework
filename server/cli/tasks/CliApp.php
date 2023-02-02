<?php

namespace Cli\Tasks;

use Phalcon\Di;

class CliApp extends \Phalcon\CLI\Task
{
    protected function setTimeZone()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    //优化此代码，用于解决 supervisor cpu 问题
    private $_logRoot = '/home/log/cli/';
    private $_useStdout = false;
    private $_className = null;
    private $_processName;
    private $_logFileName; //日志文件名
    private $_logFp = null; //当前写入文件的指针
    private $_fileSize = 0; //当前正在写入的日志文件大小
    private $_supervisor = false;
    private $_logBuffer = array(); //日志
    private $_logLength = 0; //内存中保存的日志数量
    private $_maxFileSize = 10485760; //单个文件日志大小10MB
    private $_flushNumber = 100; //多少次刷新到磁盘
    private $_logFileNum = 10; //保存文件数

    protected function console($msg)
    {
        $uuid = Di::getDefault()->getShared('uuid');
        if ($this->_className == null) {
            $this->_className = get_class($this);
            if ($this->_useStdout == false && isset($_SERVER['SUPERVISOR_GROUP_NAME']) && isset($_SERVER['SUPERVISOR_PROCESS_NAME'])) {
                print_r($_SERVER);
                //在 supervisor 下运行 //environment
                echo "Env supervisor {$this->_className} {$_SERVER['SUPERVISOR_GROUP_NAME']}\n";
                $this->_supervisor = true;
                $this->_processName = $_SERVER['SUPERVISOR_PROCESS_NAME'];
                if (!is_dir($this->_logRoot)) @mkdir($this->_logRoot, 0777, true);
                $this->_logFileName = $this->_logRoot . $this->_processName . '.log';
                $this->_logFp = fopen($this->_logFileName, "a");
                $this->_fileSize = filesize($this->_logFileName);
            }
        }
        $message = "[" . date('Y-m-d H:i:s') . "][" . $this->_className . "][$uuid]" . $msg . "\n";
        if ($this->_supervisor) {
            $this->_logBuffer[] = $message;
            $this->_logLength++;
            if ($this->_logLength >= $this->_flushNumber) {
                $r = fwrite($this->_logFp, implode("", $this->_logBuffer));
                if ($r !== false) {
                    $this->_fileSize += $r;
                    if ($this->_fileSize >= $this->_maxFileSize) {
                        $this->_rotateLogFile();
                    }
                }
                $this->_logLength = 0;
                $this->_logBuffer = array();
            }
        } else {
            echo "[$uuid]" . $message;
        }
    }

    protected function useStdout()
    {
        $this->_useStdout = true;
    }

    protected function setLogPath($path)
    {
        $this->_logRoot = rtrim($path, "/") . "/";
    }

    protected function setLogFileNum($num)
    {
        if ($num <= 1) $num = 1;
        $this->_logFileNum = $num;
    }

    //设置刷新条数，默认100
    protected function setFlushNumber($number)
    {
        if ($number <= 0) {
            $number = 1;
        } else if ($number > 100) {
            $number = 100;
        }
        $this->_flushNumber = $number;
    }

    private function _rotateLogFile()
    {
        @fclose($this->_logFp);
        $prefixLength = strlen($this->_logFileName);
        $files = array();
        $ordering = array();
        foreach (glob($this->_logFileName . "*") as $val) {
            $number = substr($val, $prefixLength + 1);
            if (!empty($number) && is_numeric($number) && intval($number) > 0) {
                $number = intval($number);
                if ($number >= $this->_logFileNum) {
                    //删除
                    @unlink($val);
                } else {
                    $files[] = array(
                        "file"   => $val,
                        "target" => $this->_logFileName . '.' . ($number + 1),
                    );
                    $ordering[] = $number;
                }
            }
        }
        if (!empty($files)) {
            //排序
            array_multisort($ordering, SORT_DESC, SORT_NUMERIC, $files);
            foreach ($files as $file) {
                @rename($file['file'], $file['target']);
            }
        }
        @rename($this->_logFileName, $this->_logFileName . ".1");
        $this->_logFileName = $this->_logRoot . $this->_processName . '.log';
        $this->_logFp = fopen($this->_logFileName, "a");
        $this->_fileSize = 0;
    }

    public function __destruct()
    {
        if (!empty($this->_logBuffer) && $this->_logFp) {
            $this->_logBuffer[] = "[" . date('Y-m-d H:i:s') . "][" . $this->_className . "]complete\n";
            fwrite($this->_logFp, implode("", $this->_logBuffer));
            fclose($this->_logFp);
        }
    }

    protected function initException(\Exception $e)
    {
        if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
            $db = Di::getDefault()->getShared('db');
            $db->close();
            $db->connect();
        }
    }

    protected function isMysqlError($e)
    {
        $errorMessage = $e->getMessage();
        if (strpos($errorMessage, 'MySQL server has gone away') !== false
            || strpos($errorMessage, 'Lost connection to MySQL server during query') !== false
            || strpos($errorMessage, 'Can\'t connect to MySQL server') !== false
            || strpos($errorMessage, 'Trying to call method exec on a non-object') !== false
        ) {
            return true;
        }
        return false;
    }

    protected function _pingMysql($dbNames)
    {
        $this->console("check db status, " . implode(",", $dbNames));
        foreach ($dbNames as $name) {
            $conn = Di::getDefault()->getShared($name);
            try {
                $r = $conn->fetchAll("show errors");
                var_dump($r);
            } catch (\PDOException $e) {
                $this->console("pdo {$name} error" . $e->getMessage());
                $conn->close();
                $conn->connect();
            }
        }
    }
}