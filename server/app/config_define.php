<?php

// ==== APPID ====
define('APP_ID', 11);

// 系统id，暂未用
define('SYSTEM_ID', 4);
// model缓存
define('MODEL_CACHE_LIFETIME', 120);

# 用研邮件退订域名
define('YY_DOMAIN', 'https://help.thinkerlab.com');
define('THINKERLAB_API_URL', 'https://help.thinkerlab.com');
define('THINKERLAB_URL', 'https://www.thinkerlab.com');

// OSS
define('OSS_IMAGE_URL_WEB', 'http://xs-image.starifymusic.com');//线上外网域名
define('OSS_IMAGE_URL_LOCAL', 'https://xs-starify.oss-ap-southeast-1-internal.aliyuncs.com');//线上内网域名
define('OSS_IMAGE_URL_TEST', 'http://bb-admin-test.oss-cn-hangzhou.aliyuncs.com');//测试域名

//上传的临时目录
define('UPLOAD_TMP_DIR', ROOT . DS . 'cache' . DS . 'tmp' . DS);
define('UPLOAD_PIC_DIR', '/public/imgs/');
define('UPLOAD_PACKAGE_DIR', '/public/package/');

// 是否打开ES
define('QUERY_USE_ES', false);

// 通用索引服务
define('API_ES_INDEX_CONFIG', 'http://rule-es-index.partying.private');

// 规则中心服务
define('API_RULE_CONFIG', 'http://rule-meta.partying.private');

// 客服websocket
define('CUSTOMER_SERVICE_SOCKET', 'websocket://0.0.0.0:2546');
define('CUSTOMER_SERVICE_CHANNEL_SERVER', '127.0.0.1');
define('CUSTOMER_SERVICE_CHANNEL_SERVER_PORT', 2500);
define('CUSTOMER_SERVICE_PUSH_CHANNEL', 'http://172.16.11.148:2550');

// RPC网关
define('Serv_Rpc_Gateway_Name', 'rpc-gateway.veeka.private');
// 自研IM服务
define('Serv_Im_Proxy_Name', 'serv-im-proxy.veeka.private');
// 融云服务
define('Serv_Rong_Proxy_Name', 'serv-rong-proxy.partying.private');

// 自研Spam-文本
define('Serv_Spam_Text_Name', 'data-learn-chinese-spam.partying.private');
// 自研Spam-图片
define('Serv_Spam_Image_Name', 'serv-learn-porn.partying.private');

// IP服务
define('Local_Server_Ip', '172.16.11.148');

define('CSV_EXPORT_TOPIC', 'veeka.exportexl');

define('VALIDATION_LANG', 'zh-CN');

// host
if (!defined("HTTP_HEAD")) define("HTTP_HEAD", "http://");
if (!defined("HTTPS_HEAD")) define("HTTPS_HEAD", "https://");
if (!defined("ROOT_HOST")) define("ROOT_HOST", "partying.sg");
if (!defined("WWW_HOST")) define("WWW_HOST", "www.partying.sg");
if (!defined("API_HOST")) define("API_HOST", "api.partying.sg");
if (!defined("PAY_HOST")) define("PAY_HOST", "pay.partying.sg");
if (!defined("TEST_HOST")) define("TEST_HOST", "test.partying.sg");

define('APP_BANBAN', 1);
define('SERV_SPAM_IMAGE_NAME', 'serv-learn-porn.banban.private');
// 推荐组图片检测新地址
define('Serv_Image_Scan', 'serv-image-scan.veeka.private');

// 图片OSS
define('CDN_IMG_DOMAIN', 'http://xs-image.partying.sg/');
// 音频OSS
define('CDN_VOICE_DOMAIN', 'https://xs-image.partying.sg/');
// 推荐组视频切片检测新地址
define('Serv_Video_Scan', 'serv-learn-video-scan.veeka.private');

//导出队列
define('EXPORTEXLS_DIR', '/api/public/');
define('EXPORTEXLS_QUEUE_NAME', 'veekanew.exportexl');

//session
define('SESSION_UNIQUE', 'veeka-new-');
define('SESSION_PRIFIX', 'vk_new_');
define('SESSION_NAME', '_VEEKANEW');


