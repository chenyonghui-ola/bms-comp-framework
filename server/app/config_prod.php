<?php

define('BASE_URL', 'https://admin.letsveeka.com/');

return array(
    // 读写
    'database' => [
        'cms' => array(
            "host" => "public-admin.rwlb.singapore.rds.aliyuncs.com",
            "port" => 3306,
            "username" => "veeka",
            "password" => "VeekaOIAD02",
            "dbname"   => "veeka_cms",
            "charset" => 'utf8mb4'
        ),

        // xss 主库
        'xssdb' => array(
            "host" => "public-admin.rwlb.singapore.rds.aliyuncs.com",
            "port"  => 3306,
            "username" => "veeka",
            "password" => "VeekaOIAD02",
            "dbname"   => "veeka_xss",
            "charset" => 'utf8mb4'
        ),

        // xsst 主库
        'xsstdb' => array(
            "host" => "public-admin.rwlb.singapore.rds.aliyuncs.com",
            "port" => 3306,
            "username" => "veeka",
            "password" => "VeekaOIAD02",
            "dbname"   => "veeka_xsst",
            "charset" => 'utf8mb4'
        ),

        // gaia 主库
        'gaiadb' => array(
            "host" => "public-admin.rwlb.singapore.rds.aliyuncs.com",
            "port"  => 3306,
            "username" => "veeka",
            "password" => "VeekaOIAD02",
            "dbname"   => "veeka_gaia",
            "charset" => 'utf8mb4'
        ),

        // xianshi主库 - new
        'xsdb' => array(
            "host" => "veeka-master.mysql.singapore.rds.aliyuncs.com",
            "port" => 3306,
            "username" => "normal",
            "password" => "Imee2016",
            "dbname" => "xianshi",
            "charset" => 'utf8mb4'
        ),
        // xianshi从库 - new
        'slavedb' => array(
            "host" => "public-admin.rwlb.singapore.rds.aliyuncs.com",
            "port"  => 3306,
            "username" => "veeka",
            "password" => "VeekaOIAD02",
            "dbname"   => "veeka_xianshi",
            "charset" => 'utf8mb4'
            // "host" => "veeka-master.mysql.singapore.rds.aliyuncs.com",
            // "port" => 3306,
            // "username" => "normal",
            // "password" => "Imee2016",
            // "dbname" => "xianshi",
            // "charset" => 'utf8mb4'
        ),

        // config 主库
        'bbcdb' => array(
            "host" => "veeka-master.mysql.singapore.rds.aliyuncs.com",
            "port" => 3306,
            "username" => "normal",
            "password" => "Imee2016",
            "dbname" => "config",
            "charset" => 'utf8mb4'
        ),
        // config 从库
        'bbc_slavedb' => array(
            "host" => "public-admin.rwlb.singapore.rds.aliyuncs.com",
            "port"  => 3306,
            "username" => "veeka",
            "password" => "VeekaOIAD02",
            "dbname"   => "veeka_config",
            "charset" => 'utf8mb4'
            // "host" => "veeka-master.mysql.singapore.rds.aliyuncs.com",
            // "port" => 3306,
            // "username" => "normal",
            // "password" => "Imee2016",
            // "dbname" => "config",
            // "charset" => 'utf8mb4'
        ),

        // activity 主库
        'activity' => array(
            "host" => "veeka-master.mysql.singapore.rds.aliyuncs.com",
            "port" => 3306,
            "username" => "normal",
            "password" => "Imee2016",
            "dbname" => "activity",
            "charset" => 'utf8mb4'
        ),
        // activity 从库
        'activity_slavedb' => array(
            "host" => "public-admin.rwlb.singapore.rds.aliyuncs.com",
            "port"  => 3306,
            "username" => "veeka",
            "password" => "VeekaOIAD02",
            "dbname"   => "veeka_activity",
            "charset" => 'utf8mb4'
            // "host" => "veeka-master.mysql.singapore.rds.aliyuncs.com",
            // "port" => 3306,
            // "username" => "normal",
            // "password" => "Imee2016",
            // "dbname" => "activity",
            // "charset" => 'utf8mb4'
        ),

        // banban_union 主库
        'union_db' => array(
            "host" => "veeka-master.mysql.singapore.rds.aliyuncs.com",
            "port" => 3306,
            "username" => "normal",
            "password" => "Imee2016",
            "dbname" => "banban_union",
            "charset" => 'utf8mb4'
        ),
        // banban_union 从库
        'union_slavedb' => array(
            "host" => "public-admin.rwlb.singapore.rds.aliyuncs.com",
            "port"  => 3306,
            "username" => "veeka",
            "password" => "VeekaOIAD02",
            "dbname"   => "veeka_banban_union",
            "charset" => 'utf8mb4'
            // "host" => "veeka-master.mysql.singapore.rds.aliyuncs.com",
            // "port" => 3306,
            // "username" => "normal",
            // "password" => "Imee2016",
            // "dbname" => "banban_union",
            // "charset" => 'utf8mb4'
        ),

        // yougo主库
        'yougodb' => array(
            "host" => "172.16.11.148",
            "port"	=> 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname"   => "yougo",
            "charset" => 'utf8mb4'
        ),
        // yougo从库
        'yougo_slavedb' => array(
            "host" => "172.16.11.148",
            "port"	=> 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname"   => "yougo",
            "charset" => 'utf8mb4'
        ),

        // 用研库
        'urdb' => array(
            "host" => "172.16.11.148",
            "port"	=> 3306,
            "username" => "admin",
            "password" => "imee2016",
            "dbname"   => "ur",
            "charset" => 'utf8mb4'
        ),

        // lemon主库
        'lemondb' => array(
            "host" => "veeka-master.mysql.singapore.rds.aliyuncs.com",
            "port"	=> 3306,
            "username" => "normal",
            "password" => "Imee2016",
            "dbname"   => "lemon",
            "charset" => 'utf8mb4'
        ),

        // lemon从库
        'lemon_slavedb' => array(
            "host" => "public-admin.rwlb.singapore.rds.aliyuncs.com",
            "port"  => 3306,
            "username" => "veeka",
            "password" => "VeekaOIAD02",
            "dbname"   => "veeka_lemon",
            "charset" => 'utf8mb4'
            // "host" => "veeka-master.mysql.singapore.rds.aliyuncs.com",
            // "port"	=> 3306,
            // "username" => "normal",
            // "password" => "Imee2016",
            // "dbname"   => "lemon",
            // "charset" => 'utf8mb4'
        ),

        // 充值 - 读写分离库
        'rechargedb' => array(
            "host" => "public-admin.rwlb.singapore.rds.aliyuncs.com",
            "port" => 3306,
            "username" => "recharge",
            "password" => "Imee20220304",
            "dbname" => "recharge",
            "charset" => 'utf8mb4',
        ),

        // bms公共库
        'bms_shared' => array(
            "host" => "public-admin.rwlb.singapore.rds.aliyuncs.com",
            "port"  => 3306,
            "username" => "bms_shared",
            "password" => "BmsSharedImee2022",
            "dbname"   => "bms_shared",
            "charset" => 'utf8mb4'
        ),
    ],

    // 业务-redis
    'redis' => array(
        'host' => 'veeka-cache.redis.singapore.rds.aliyuncs.com',
        'port' => 6379,
        'persistent' => false
    ),

    // Admin-redis
    'redis_admin' => array(
        'host' => '172.16.11.148',
        'port' => 6379,
        'persistent' => false
    ),
    // Admin-ssdb
    'ssdb_admin' => [
        'host' => '172.16.9.210',
        'port' => 6389,
    ],
    // Admin-beanstalk
    'beanstalk' => array(
        'host' => '172.16.11.148',
        'port' => 11300,
        'timeout' => 1
    ),

    // 业务-nsq
    'lookup' => "172.16.11.147:4161",
    'nsq' => array(
        "172.16.11.147:4150",
        "172.16.11.147:4152",
    ),
    // Admin-nsq
    'lookup_admin' => "172.16.11.148:4161",
    'nsq_admin' => array(
        "172.16.11.148:4150",
    ),

    'map' => array(),
    'filter_interface' => array(
        'url' => 'http://10.140.75.98:6080'
    ),
    // 业务kafka
    'kafka_brokerlist' => 'alikafka-pre-cn-2r42lygi300r-1-vpc.alikafka.aliyuncs.com:9092,alikafka-pre-cn-2r42lygi300r-2-vpc.alikafka.aliyuncs.com:9092,alikafka-pre-cn-2r42lygi300r-3-vpc.alikafka.aliyuncs.com:9092',

    // Admin-es
    'elasticsearch' => [
        'hosts' => ['172.16.9.211:9200'],
        'retries'   => 1,
    ],

    // 无用
    'sphinx' => array(
        'host' => '127.0.0.1',
        'port' => 9312,
        'charset' => 'utf8'
    ),
);
