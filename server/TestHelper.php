<?php

use Phalcon\Di;

ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

define("ROOT_PATH", __DIR__);

set_include_path(
    ROOT_PATH . PATH_SEPARATOR . get_include_path()
);

// Required for phalcon/incubator
define('CONFIG', dirname(dirname(ROOT)) . DS . 'ee-config-admin' . (ENV != 'dev' ? '' : '-dev'));

require_once(APP_PATH . DS . 'libs' . DS . 'fixed' . DS . 'Functions.php');
require_once(APP_PATH . DS . 'libs' . DS . 'fixed' . DS . 'Loader.php');
require_once(APP_PATH . DS . 'config_define.php');

require_once(APP_PATH . DS . 'libs' . DS . 'vendor' . DS . 'autoload.php');

$loader = require_once APP_PATH . '/loader.php';
$loader->registerDirs(
    [
        ROOT_PATH,
    ]
);
$loader->register();

// Create a DI
$di = require_once APP_PATH . '/di.php';
Di::reset();

// Add any needed services to the DI here

Di::setDefault($di);
