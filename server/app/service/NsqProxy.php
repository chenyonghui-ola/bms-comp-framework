<?php

namespace Imee\Service;

//为了优化cli进程里 nsq发送消息调用Nsq::publish带来的端口打开，关闭的开销问题
class NsqProxy
{
    private $_worker = null;

    public function __construct()
    {
    }

    public function enabled()
    {
        return !is_null($this->_worker);
    }

    public function publish($topic, $message, $delay = 0)
    {
        $this->_worker->publish($topic, $message, $delay);
    }

    public function csmsPublish($topic, $message, $delay = 0)
    {
        return $this->_worker->csmsPublish($topic, $message, $delay);
    }

    public function setWorker($worker)
    {
        $this->_worker = $worker;
    }

    public function __destruct()
    {
        $this->_worker = null;
    }
}