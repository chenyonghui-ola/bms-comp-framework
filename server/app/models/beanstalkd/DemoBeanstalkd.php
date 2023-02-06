<?php

namespace Imee\Models\Beanstalkd;

use Imee\Comp\Common\Beanstalkd\Client;

class DemoBeanstalkd
{
    const TOPIC_PUSH_MESSAGE = 'xs.user_push_message';

    public static function pushMessage($params, $cmd = 'push_management')
    {
        $client = new Client();
        $client->choose(self::TOPIC_PUSH_MESSAGE);
        $client->set([
            'cmd'  => $cmd,
            'data' => $params,
        ], 1024, 2);
        $client->close();
    }
}