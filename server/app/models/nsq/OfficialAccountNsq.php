<?php

namespace Imee\Models\Nsq;

use Imee\Service\Nsq;

class OfficialAccountNsq
{
    const OFFICIAL_ACCOUNT_STATE = 'xs.cmd';

    /**
     * 修改官方账号状态发消息通知给服务端
     */
    public static function publishSetState($data): bool
    {
	    //发送消息到xs，由xs更改数据
	    return Nsq::publish(self::OFFICIAL_ACCOUNT_STATE, [
		    'cmd' => 'forbidden',
		    'data' => $data
	    ]);
    }
}