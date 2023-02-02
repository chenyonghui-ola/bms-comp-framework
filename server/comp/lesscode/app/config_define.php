<?php

/**
 * 常量不会作用到项目，只是列出支持常量，需要使用复制到项目里
 */

// 定义版本
define('LESSCODE_VERSION', '1.0');

// 定义系统标识 例如：ka
define('LESSCODE_SYSTEM_FLAG', '');

// 定义系统导出队列名称 例如：userAdmin.exportexl
define('LESSCODE_SYSTEM_EXPORT_NAME', '');

// 控制默认驱动
define('ADAPTER_SCHEMA_DRIVE_FUNC_DEFAULT', 'api');

// 使用的数据库
define('LESSCODE_BASE_DATABASE_SCHEMA', 'cms');

// api路由前缀
define('LESSCODE_API_PREFIX', 'api');

// 数据库表前缀
define('DATABASE_TABLE_PREFIX', '');

// 数据库低代码表前缀
define('DATABASE_TABLE_LESSCODE_PREFIX', '');

// bms 数据库表前缀
define('DATABASE_BMS_TABLE_PREFIX', '');
define('LESSCODE_BMS_DATABASE_SCHEMA', 'bmsdb');

// xsst 数据库表前缀
define('DATABASE_XSST_TABLE_PREFIX', '');
define('LESSCODE_XSST_DATABASE_SCHEMA', 'xsstdb');
define('LESSCODE_XSST_SLAVE_DATABASE_SCHEMA', 'xsstdbs2');