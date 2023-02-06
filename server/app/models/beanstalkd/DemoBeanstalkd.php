<?php

namespace Imee\Models\Beanstalkd;

class DemoBeanstalkd extends BaseBeanstalkd
{
    const TOPIC_PUSH_MESSAGE = 'xs.user_push_message';

    public static function pushMessage($params, $cmd = 'push_management')
    {
        self::push(self::TOPIC_PUSH_MESSAGE, $params, $cmd);
    }
}