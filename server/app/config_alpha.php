<?php

define('DEBUG', false);
define('BASE_URL', 'https://admin-finance.iambanban.com/');
define('PROXY_URL', 'http://47.244.191.22/proxy/');
define('Serv_Api_Name', '');

return array(
    // 读写
    'database' => [
        'cms' => array(
            "host" => "vpc-xs-manager.rwlb.rds.aliyuncs.com",
            "port" => 3306,
            "username" => "normal",
            "password" => "Imee2016",
            "dbname" => "cms",
            "charset" => 'utf8mb4'
        ),
    ],

    'redis' => array(
        'host' => '172.16.0.87',
        'port' => 6379,
        'persistent' => false
    ),

    'beanstalk' => array(
        'host' => '172.16.0.87',
        'port' => 11300,
        'timeout' => 1
    ),
    'lookup' => "172.16.0.87:4161",
    'nsq' => array(
        "172.16.0.87:4150",
    ),
    'nsq_circle' => array(
        "172.16.0.87:4150",
    ),
    /*
        cache 系统会将输出的http body缓存到xcache，并输出http header cache
        login true 系统会判断用户有没有登录，没有登录的会被定向到登录页面
        session true 系统会自动启用session，也可以自行在控制器中开启
        ui true 特殊用途，系统会将http body自动转化为javascript格式输出
    */
    'map' => array(
        'upload' => array(
            'image' => array('login' => true),
        ),
        'pay' => array(
            'wechat' => array('login' => true),
            'ali' => array('login' => true),
            'return' => array('login' => true),
            'cash' => array('login' => true),
        ),
        'account' => array(
            'profile' => array('login' => true),
            'phonebind' => array('login' => true),
            'phoneChange' => array('login' => true),
            'resetPassword' => array('login' => true),
            'verifyCurrentMobile' => array('login' => true),
            'wechat' => array('session' => true),
            'register' => array('session' => true),
            'allTalkFriends' => array('login' => true),
            'allTalkFriendsV2' => array('login' => true),
            'userState' => array('login' => true),
            'sync' => array('login' => true),
            'isFriend' => array('login' => true),
            'vipStatus' => array('login' => true),
            'vip' => array('login' => true),
            'position' => array('login' => true),
            'exchange' => array('login' => true),
            'getLikeEach' => array('login' => true),
            'getSphinxNum' => array('login' => true),
            'checkRemove' => array('login' => true),
        ),
        'contact' => array(
            'index' => array('login' => true),
            'updateToRead' => array('login' => true),
            'getNewFriends' => array('login' => true),
            'getBlack' => array('login' => true),
            'updateContactType' => array('login' => true),
            'updateToBlack' => array('login' => true),
            'getAll' => array('login' => true),
            'detail' => array('login' => true),
            'invite' => array('login' => true),
            'changeMarkName' => array('login' => true),
            'getNewFriendsNum' => array('login' => true),
            'uploadPhoneContact' => array('login' => true),
            'firstUploadPhoneContact' => array('login' => true),
            'getContactNums' => array('login' => true)
        ),
        'userprofile' => array(
            'getProfile' => array('login' => true),
            'positionDefault' => array('login' => true),
            'modProfile' => array('login' => true),
            'addBaseProfile' => array('login' => true),
            'addMyProfile' => array('login' => true),
            'addBgProfile' => array('login' => true),
            'addMyProfile2' => array('login' => true),
            'addBaseProfile2' => array('login' => true),
            'uploadBigPicture' => array('login' => true),
            'uploadBg' => array('login' => true),
            'getHaunt' => array('session' => true),
            'delBigPicture' => array('login' => true),
        ),
        'story' => array(
            'modStory' => array('login' => true),
            'uploadPhoto' => array('login' => true),
            'getList' => array('login' => true),
            'orderList' => array('login' => true),
            'delList' => array('login' => true),
        ),
        'group' => array(
            'create' => array('login' => true),
            'join' => array('login' => true),
            'anonymous' => array('login' => true),
            'quit' => array('login' => true),
            'refresh' => array('login' => true),
            'add' => array('login' => true),
            'remove' => array('login' => true),
            'info' => array('login' => true),
        ),
        'search' => array(
            'tips' => array('session' => true)
        ),
        'date' => array(
            'party' => array('login' => true),
            'partyJoin' => array('login' => true),
            'partyView' => array('login' => true),
            'chatState' => array('login' => true),
            'getIngDate' => array('login' => true),
            'canDate' => array('login' => true),
            'start' => array('login' => true),
            'show' => array('login' => true),
            'showParty' => array('login' => true),
            'showSimple' => array('login' => true),
            'uploadPicture' => array('login' => true),
            'surety' => array('login' => true),
            'carry' => array('login' => true),
            'time' => array('login' => true),
            'getSelectedDate' => array('login' => true),
            'getSelectedDateCate' => array('login' => true),
            'getPartys' => array('login' => true),
            'getPartyCity' => array('login' => true),
        ),
        'wechat' => array(
            'oauthCallback' => array('session' => true),
            'report' => array('session' => true),
            'view' => array('session' => true),
            'view2' => array('session' => true),
            'love1' => array('session' => true),
            'love2' => array('session' => true),
            'love22' => array('session' => true),
            'love3' => array('session' => true),
            'matchs' => array('session' => true),
            'reporttags' => array('session' => true),
            'addMath' => array('session' => true),
            'zanMatch' => array('session' => true),
            'school' => array('session' => true),
            'schoolSubmit' => array('session' => true),
            'cleanLove2' => array('session' => true),
            'clean2' => array('session' => true),
            'eeNotice' => array('session' => true),
            'drift' => array('session' => true),
            'semi' => array('session' => true),
            'halloween' => array('session' => true),
            'chat' => array('session' => true),
            'getWxShareJson' => array('session' => true),
        ),
        'qq' => array(
            'oauthCallback' => array('session' => true),
            'index' => array('session' => true),
            'login' => array('session' => true),
            'logout' => array('session' => true),
        ),
        'cloud' => array(
            'empty' => array('login' => true),
            'token' => array('login' => true),
            'discuz' => array('login' => true),
            'group' => array('login' => true),
        ),
        'lover' => array(
            'addMatch' => array('login' => true),
            'addLover' => array('login' => true),
            'addLoverXin' => array('login' => true),
            'listMatch' => array('login' => true),
            'zanMatch' => array('login' => true),
            'listFriendMatch' => array('login' => true),
            'listPresence' => array('login' => true),
            'delLover' => array('login' => true),
        ),
        'drift' => array(
            'list' => array('login' => true),
            'get' => array('login' => true),
            'save' => array('login' => true),
            'add' => array('login' => true),
            'bind' => array('login' => true),
            'discuz' => array('login' => true),
        ),
        'test' => array(
            'index' => array('session' => true)
        )
    ),
    //这个define里的东西可以直接在模板里通过cfg使用
    'define' => array(
        //图片加载域
        'img' => 'http://img.imee.com/',
        'domain' => '/',
    ),
    'updater' => array(
        'enabled' => false,
        'begin' => strtotime('2016-08-03 18:00:00'),
        'duration' => 60, //0 标识无效,即不会block用户
        'native' => '1.0.0.3', //native的最新版本
        'exclude' => array('1.0.0.1'),
        'js' => '1.0.0.4', //js的最新版本
    ),
    'filter_interface' => array(
        'url' => 'http://10.140.75.98:6080'
    ),
    'kafka_brokerlist' => '172.16.0.148:9092,172.16.0.149:9092,172.16.0.150:9092',
    'elasticsearch' => [
        'hosts'	 => ['172.16.0.29:9200'],
        'retries'   => 1,
    ],
);
