<?php

namespace Imee\Models\Nsq;

use Imee\Libs\Utility;
use Imee\Service\Nsq;

class GiftNsq
{
    const TOPIC_XS_FINGER_GUESS = 'xs.finger.guess';

    /**
     * 猜拳礼物发消息通知给服务端
     */
    public static function publishXsFingerGuess(): bool
    {
        return Nsq::publish(self::TOPIC_XS_FINGER_GUESS, [
            'cmd'  => 'finger.guess.gift.created',
            'time' => Utility::microtimeFloat(),
            'data' => ['content' => 1],
        ]);
    }
}