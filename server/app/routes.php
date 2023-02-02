<?php

use Phalcon\Mvc\Router;

$router = new Router();

$router->setDefaults(array(
    "namespace"  => "Imee\Controller",
    "controller" => "index",
    "action"     => "index"
));


$router->add(
    "/api/auth/:controller/:action/:params(|\/)",
    [
        "namespace"  => 'Imee\Controller\Auth',
        "controller" => 1,
        "action"     => 2,
        "params"     => 3
    ]
);

// 公共
$router->add(
    "/api/common/:controller/:action/:params(|\/)",
    [
        "namespace"  => 'Imee\Controller\Common',
        "controller" => 1,
        "action"     => 2,
        "params"     => 3
    ]
);

// 开放接口
$router->add(
    "/api/open/:controller/:action/:params(|\/)",
    [
        "namespace"  => 'Imee\Controller\Open',
        "controller" => 1,
        "action"     => 2,
        "params"     => 3
    ]
);

// lesscode 引入低代码所需要的路由
if (is_file(ROOT . '/comp/lesscode/app/routes.php')) {
    $router = require_once ROOT . '/comp/lesscode/app/routes.php';
}

return $router;
