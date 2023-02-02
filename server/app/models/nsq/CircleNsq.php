<?php

namespace Imee\Models\Nsq;

use Imee\Service\Nsq;

class CircleNsq
{
    const TOPIC_RS_CIRCLE_TAG = 'rs.circle.tag.es';

    /**
     * 动态标签
     */
    public static function publishRsCircleTagEs($cmd, $id): bool
    {
        return Nsq::publish(self::TOPIC_RS_CIRCLE_TAG, [
            'cmd' => $cmd,
            'data' => ['id' => $id],
        ]);
    }
}