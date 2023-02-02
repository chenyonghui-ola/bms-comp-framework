<?php

use Imee\Libs\Fixed\Loader;

$loader = new Loader();
$loader->registerNamespaces(array(
    "Imee\Controller" => "app/controller/",
    "Imee\Libs" => "app/libs/",
    "Imee\Exception" => "app/exception/",
    "Imee\Service" => "app/service/",
    "Config" => CONFIG . "/",
    "Imee\Helper" => "app/helper/",
    "Imee\Export" => "app/export/",
    "Imee\Models" => "app/models/",
    "Imee\Comp" => "comp/",
    "OSS" => "comp/common/oss/",
));

// lesscode 引入低代码所需要的命名空间
if (is_file(ROOT . '/comp/lesscode/app/loader.php')) {
	$loader = require_once ROOT . '/comp/lesscode/app/loader.php';
}

return $loader;
