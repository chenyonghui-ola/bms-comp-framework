<?php

define('DEBUG', true);
define('BASE_URL', 'https://admin-finance.iambanban.com/');
define('PROXY_URL', 'http://47.244.191.22/proxy/');
define('MYSQL_HOST_DEV', '192.168.11.51');
define('REDIS_HOST_DEV', '192.168.11.51');

return array(
    'database' => [
        'cms' => array(
            "host" => MYSQL_HOST_DEV,
            "port" => 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname"   => "veeka_cms",
            "charset" => 'utf8mb4'
        ),
        // xss 主库
        'xssdb' => array(
            "host" => MYSQL_HOST_DEV,
            "port"  => 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname"   => "veeka_xss",
            "charset" => 'utf8mb4'
        ),
        // xsst 主库
        'xsstdb' => array(
            "host" => MYSQL_HOST_DEV,
            "port"  => 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname"   => "veeka_xsst",
            "charset" => 'utf8mb4'
        ),

        // gaia 主库
        'gaiadb' => array(
            "host" => MYSQL_HOST_DEV,
            "port"  => 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname"   => "veeka_gaia",
            "charset" => 'utf8mb4'
        ),


        // xianshi主库 - new
        'xsdb' => array(
            "host" => MYSQL_HOST_DEV,
            "port" => 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname" => "veeka-xianshi",
            "charset" => 'utf8mb4'
        ),
        // xianshi从库 - new
        'slavedb' => array(
            "host" => MYSQL_HOST_DEV,
            "port" => 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname" => "veeka-xianshi",
            "charset" => 'utf8mb4'
        ),
        // config 主库
        'bbcdb' => array(
            "host" => MYSQL_HOST_DEV,
            "port" => 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname" => "veeka_config",
            "charset" => 'utf8mb4'
        ),
        // config 从库
        'bbc_slavedb' => array(
            "host" => MYSQL_HOST_DEV,
            "port" => 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname" => "veeka_config",
            "charset" => 'utf8mb4'
        ),
        // lemon主库
        'lemondb' => array(
            "host" => MYSQL_HOST_DEV,
            "port" => 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname"   => "lemon",
            "charset" => 'utf8mb4'
        ),

        // lemon从库
        'lemon_slavedb' => array(
            "host" => MYSQL_HOST_DEV,
            "port"	=> 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname"   => "lemon",
            "charset" => 'utf8mb4'
        ),
        // bms公共库
        'bms_shared' => array(
            "host" => MYSQL_HOST_DEV,
            "port"  => 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname"   => "bms_shared",
            "charset" => 'utf8mb4'
        ),
    ],

    // 业务-redis
    'redis' => array(
        'host' => REDIS_HOST_DEV,
        'port' => 6379,
        'persistent' => false
    ),

    // Admin-redis
    'redis_admin' => array(
        'host' => REDIS_HOST_DEV,
        'port' => 6379,
        'persistent' => false
    ),
    // Admin-ssdb
    'ssdb_admin' => [
        'host' => '127.0.0.1',
        'port' => 6389,
    ],
    // Admin-beanstalk
    'beanstalk' => array(
        'host' => '127.0.0.1',
        'port' => 11300,
        'timeout' => 1
    ),

    // 业务-nsq
    'lookup' => "127.0.0.1:4161",
    'nsq' => array(
        "127.0.0.1:4150",
        "127.0.0.1:4152",
    ),
    'nsq_circle' => array(
        "127.0.0.1:4150",
    ),
    // Admin-nsq
    'lookup_admin' => "127.0.0.1:4161",
    'nsq_admin' => array(
        "127.0.0.1:4150",
    ),
    // 业务-kafka
    'kafka_brokerlist' => '',

    // Admin-es
    'elasticsearch' => [
        'hosts' => ['127.0.0.1:9200'],
        'retries'   => 1,
    ],

    'map' => array(),
);
