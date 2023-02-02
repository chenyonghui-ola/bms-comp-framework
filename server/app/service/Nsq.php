<?php

namespace Imee\Service;

use Phalcon\Di;
use Imee\Comp\Common\PhpNsq\PhpNsq;

require_once ROOT . DS . 'comp/common/phpnsq/PhpNsq.php';
require_once ROOT . DS . 'comp/common/phpnsq/wire/Reader.php';
require_once ROOT . DS . 'comp/common/phpnsq/wire/Writer.php';
require_once ROOT . DS . 'comp/common/phpnsq/utility/Logging.php';
require_once ROOT . DS . 'comp/common/phpnsq/utility/IntPacker.php';
require_once ROOT . DS . 'comp/common/phpnsq/utility/Stream.php';
require_once ROOT . DS . 'comp/common/phpnsq/tunnel/Pool.php';
require_once ROOT . DS . 'comp/common/phpnsq/tunnel/Tunnel.php';
require_once ROOT . DS . 'comp/common/phpnsq/tunnel/Config.php';
require_once ROOT . DS . 'comp/common/phpnsq/command/Base.php';

class Nsq
{
    // lookup
    const Nsq_Lookup = "lookup"; // 业务
    const Nsq_Lookup_Admin = "lookup_admin"; // 后台

    // nsd
    const Nsq = "nsq"; // 业务
    const Nsq_Admin = "nsq_admin"; // 后台

    const Nsq_Circle = 'nsq_circle';

    private static $_forwardToCircle = array(

        // ==== 业务 ====


        // ==== 后台 ====
        'admin.account.relationship' => self::Nsq_Admin,
        'admin.broker'               => self::Nsq_Admin,
        'admin.http.proxy'           => self::Nsq_Admin,
        'admin.im.msg'               => self::Nsq_Admin,
        'admin.review'               => self::Nsq_Admin,
        'help.gwebc'                 => self::Nsq_Admin,
        'im.notify'                  => self::Nsq_Admin,
        'oversea.gaia.msg'           => self::Nsq_Admin,
        'ur.user'                    => self::Nsq_Admin,
        'xs.beta.cron'               => self::Nsq_Admin,
        'xs.chat.media.message'      => self::Nsq_Admin,
        'xs.chat.message.check'      => self::Nsq_Admin,
        'xs.report'                  => self::Nsq_Admin,
        'xs.text.scan.porn'          => self::Nsq_Admin,
        'xs.xs_chatroom_package'     => self::Nsq_Admin,
        'xs.xs_user_profile'         => self::Nsq_Admin,
        'admin.msg.push'             => self::Nsq_Admin,
    );

    public static function getNsdNameByTopic($topic)
    {
        if (isset(self::$_forwardToCircle[$topic])) {
            return self::$_forwardToCircle[$topic];
        } else {
            throw new \Exception("error params topic {$topic}");
        }
    }

    private static function getAddrsByTopic($topic)
    {
        $nsdName = self::getNsdNameByTopic($topic);
        $manager = Di::getDefault()->getShared('config');
        if (!$manager->{$nsdName}) throw new \Exception("error nsd name {$nsdName}");
        return $manager->{$nsdName};
    }

    private static function getAddrsByNsdName($nsdName)
    {
        if (is_null($nsdName)) $nsdName = self::Nsq;
        $manager = Di::getDefault()->getShared('config');
        if (!$manager->{$nsdName}) throw new \Exception("error nsd name {$nsdName}");
        return $manager->{$nsdName};
    }

    //废弃$nsdName参数
    public static function publish($topic, $message, $delay = 0, $nsdName = null)
    {
        //做点比较恶心的事情
        if (IS_CLI) {
            $proxy = Di::getDefault()->getShared('nsq_proxy');
            if ($proxy->enabled()) {
                return $proxy->publish($topic, $message, $delay);
            }
        }
        $config = self::getAddrsByTopic($topic);
        $phpnsq = new PhpNsq($config);
        if ($delay > 0) {
            $r = $phpnsq->setTopic($topic)->publishDefer($message, 1000 * $delay, $error);
        } else {
            $r = $phpnsq->setTopic($topic)->publish($message, $error);
        }
        $phpnsq->close();
        return $r;
    }

    //废弃$nsdName参数
    public static function publishMulti($topic, $messages, $nsdName = null)
    {
        $config = self::getAddrsByTopic($topic);
        $phpnsq = new PhpNsq($config);
        $r = $phpnsq->setTopic($topic)->publishMulti($messages, $error);
        $phpnsq->close();
        return $r;
    }

    //别忘记 close,
    //如果多次调用pub时间相差太大，需要自己定时ping
    public static function instance($nsdName = null)
    {
        $config = self::getAddrsByNsdName($nsdName);
        return new PhpNsq($config);
    }
}
