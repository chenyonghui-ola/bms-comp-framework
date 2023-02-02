<?php

namespace Imee\Models\Traits;

use Phalcon\Events\Manager as EventsManager;
use Imee\Service\Event\ModelLogEventListener;

trait ModelLogTrait
{
    /**
     * 日志类的全路径
     * @var string
     * @example - DemoLog::class
     */
    // private $recordLog;

    /**
     * 日志context类的全路径
     * @var string
     * @example - DemoLogContext::class
     */
    // private $logContext;

    /**
     * 主表的主键，非必填
     * @var string
     */
    // private $logPrimaryKey;

    private $logAttr;

    public function getLogPrimaryKey()
    {
        return $this->logPrimaryKey;
    }

    protected function setLogEventsManager()
    {
        $eventsManager = new EventsManager();
        $eventsManager->attach(
            'model',
            new ModelLogEventListener()
        );

        $this->setEventsManager($eventsManager);
    }

    public function setLogAttr(array $conditions)
    {
        if (!empty($conditions)) {
            $this->logAttr  = (new \ReflectionClass($this->logContext))->newInstance($conditions);
        }

        return $this;
    }

    public function getLogAttr()
    {
        return $this->logAttr;
    }

    public function getRecordLogModel()
    {
        return new $this->recordLog();
    }
}
