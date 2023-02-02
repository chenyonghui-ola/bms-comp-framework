<?php

namespace Imee\Models\Nsq;

use Imee\Service\Nsq;

class TopicNsq
{
    const TOPIC_CIRCLE = 'xs.circle';

    public static function publishVerify($uid, $topicId, $status, $reason = ''): bool
    {
        return Nsq::publish(self::TOPIC_CIRCLE, array(
            'cmd'  => 'topic.verify',
            'data' => array(
                'uid'      => intval($uid),
                'topic_id' => intval($topicId),
                'result'   => $status,
                'reason'   => $reason,
            )
        ));
    }
}