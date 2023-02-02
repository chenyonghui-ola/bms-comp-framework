<?php

namespace Imee\Service\Event;

class ModelLogEventListener
{
    private $change;

    public function beforeSave($event, $connection)
    {
        $this->change = $connection->getChange();
        return true;
    }

    public function afterSave($event, $connection)
    {
        $logAttr = $connection->getLogAttr();

        if (empty($logAttr) || empty($logAttr->toArray())) {//防止与系统本来的save产生冲突
            return false;
        }
        $recordLogModel = $connection->getRecordLogModel();
        $recordLogModel->change = json_encode($this->change);
        $recordLogModel->dateline = time();
        $primaryKey = $connection->getLogPrimaryKey();
        $recordLogModel->related_id = $connection->$primaryKey;

        foreach ($logAttr->toArray() as $k => $v) {
            $recordLogModel->$k = $v;
        }
        $recordLogModel->save();
        return true;
    }
}
