<?php

namespace Imee\Models\Beanstalkd;

use Imee\Comp\Common\Beanstalkd\Client;

class BaseBeanstalkd
{
    public static function push($topic, $params, $cmd, $priority = 1024, $delay = 1)
    {
        $client = new Client();
        $client->choose($topic);
        $client->set([
            'cmd'  => $cmd,
            'data' => $params,
        ], $priority, $delay);
        $client->close();
    }
}