<?php

// ==== APPID ====
define('APP_ID', 11);
define('APP_BANBAN', 1); // 伴伴
define('APP_PDY', 2); // 皮队友
define('APP_XIONGSHOU', 3); // 谁是凶手
define('APP_KAIXIN', 4); // 开心玩
define('APP_PT', 5); // PT
define('APP_RUSH', 6); // 新冲鸭
define('APP_MAX', 7);
define('APP_MINI_GRP', 8);
define('APP_VEEKA', 11);

// 系统id: cms_user 表 system_id
define('SYSTEM_ID', 4);
// model缓存
define('MODEL_CACHE_LIFETIME', 86400);

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

//验证类提示语言配置
define('VALIDATION_LANG', 'zh-CN');

//导出队列
define('EXPORTEXLS_DIR', '/api/public/');
define('EXPORTEXLS_QUEUE_NAME', 'veekanew.exportexl');

//session
define('SESSION_UNIQUE', 'veeka-new-');
define('SESSION_PRIFIX', 'vk_new_');
define('SESSION_NAME', '_VEEKANEW');
define('SESSION_LIFETIME', 86400);


