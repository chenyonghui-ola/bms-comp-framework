<?php

namespace Imee\Models\Nsq;

use Imee\Service\Nsq;

class RoomNsq
{
    const ROOM_PROMOTE = 'xs.room.promote';

    public static function roomPromote($rid): bool
    {
	    return Nsq::publish(self::ROOM_PROMOTE, [$rid]);
    }
}