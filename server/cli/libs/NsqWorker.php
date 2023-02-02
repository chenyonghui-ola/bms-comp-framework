<?php

namespace Imee\Cli\Libs;

use Phalcon\Di;
use Imee\Comp\Common\PhpNsq\Command\Base;

require_once ROOT . DS . 'comp/common/phpnsq/PhpNsq.php';
require_once ROOT . DS . 'comp/common/phpnsq/wire/Reader.php';
require_once ROOT . DS . 'comp/common/phpnsq/wire/Writer.php';
require_once ROOT . DS . 'comp/common/phpnsq/utility/Logging.php';
require_once ROOT . DS . 'comp/common/phpnsq/utility/IntPacker.php';
require_once ROOT . DS . 'comp/common/phpnsq/utility/Stream.php';
require_once ROOT . DS . 'comp/common/phpnsq/tunnel/Pool.php';
require_once ROOT . DS . 'comp/common/phpnsq/tunnel/Tunnel.php';
require_once ROOT . DS . 'comp/common/phpnsq/tunnel/Config.php';
require_once ROOT . DS . 'comp/common/phpnsq/Message/Message.php';
require_once ROOT . DS . 'comp/common/phpnsq/Command/Base.php';

class NsqWorker extends Base
{
    public function __construct($onMessage, $onException = null, $onExit = null, $nsdName = null)
    {
        $manager = Di::getDefault()->getShared('config');
        if (is_null($nsdName)) {
            $nsdName = array("nsq");
        } else if (is_string($nsdName)) {
            if (!$manager->{$nsdName}) throw new \Exception("error nsd name {$nsdName}");
            $nsdName = array($nsdName);
        } else if (is_array($nsdName)) {
            foreach ($nsdName as $nsdVal) {
                if (!$manager->{$nsdVal}) throw new \Exception("error nsd name {$nsdVal}");
            }
        } else {
            throw new \Exception("error params nsdName");
        }
        $data = array();
        foreach ($nsdName as $name) {
            $config = $manager->{$name};
            foreach ($config as $v) {
                $data[] = $v;
            }
        }
        parent::__construct($data, $onMessage, $onException, $onExit);
    }
}