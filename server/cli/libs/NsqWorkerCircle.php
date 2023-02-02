<?php
namespace Cli\Libs;

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

class NsqWorkerCircle extends Base
{
    public function __construct($onMessage, $onException = null, $onExit = null)
    {
        $config = Di::getDefault()->getShared('config')->nsq_circle;
        parent::__construct($config, $onMessage, $onException, $onExit);
    }
}