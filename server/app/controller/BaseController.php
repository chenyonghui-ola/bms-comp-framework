<?php

namespace Imee\Controller;

use Imee\Exception\ApiException;
use Imee\Comp\Common\Beanstalkd\Client;
use Imee\Comp\Common\Redis\RedisBase;
use Imee\Comp\Common\Redis\RedisHash;
use Imee\Service\Domain\Service\Auth\StaffService;
use Imee\Service\Domain\Context\Auth\Staff\UserInfoContext;

class BaseController extends \Phalcon\Mvc\Controller
{
    private $staffService;
    protected $lang;
    protected $guid;            // 唯一id
    protected $uid;             // 用户ID
    protected $allowSort = array();

    /**
     * @var array 请求所有参数
     */
    protected $params;

    //此类的控制器无需验证登录
    private $notLoginNeed = array(
        'index'      => ['index'],
        'auth/login' => ['index', 'logout', 'qwindex', 'callback'],
    );

    //不需要权限
    private $notPermission = [
        'common/upload.image',
        'common/upload.video',
        'common/upload.voice',
        'common/upload.file',
        'auth/staff.leftMenu',
        'auth/staff.menu',
        'auth/staff.permission',
    ];

    // 不需要权限的控制器
    private $notPermissionCtl = [
        'common/enum',
        'common/unittest',
        'operate/operatelog',
    ];

    // 不需要记录日志
    private $notLog = [
        'help' => ['getNewUsers']
    ];

    protected function onConstruct()
    {
        // parent::onConstruct();
        $this->staffService = new StaffService();

        $sessionId = $this->request->getQuery('token', 'trim', '');
        if ($sessionId) {
            $this->session->setId($sessionId);
        }

        $this->lang = $this->request->getHeader('Lang');
        $this->lang = $this->lang ?: 'zh_cn';

        $this->session->start();
        $num = 0;
        while ($num <= 3) {
            $this->uid = intval($this->session->get('uid'));
            if ($this->uid > 0) {
                break;
            }
            $num++;
        }
        $valid = true;

        if (!$this->uid) {
            $valid = false;
        } else {
            $userInfoContext = new UserInfoContext([
                'user_id' => $this->uid,
            ]);
            $profile = $this->staffService->getUserInfo($userInfoContext);
            if (empty($profile) || !$profile['user_status']) {
                $valid = false;
            }
        }
        if (!$valid) {
            $this->uid = 0;
            $this->session->remove('uid');
            $this->session->remove('purview');
            $this->session->remove('userinfo');
        }

        $this->params = array_merge(
            ['admin_uid' => $this->uid],
            $this->request->getQuery(),
            $this->request->getPost()
        );
    }

    public function beforeExecuteRoute()
    {
        //开始处理权限控制问题
        $nameSpace = $this->dispatcher->getNamespaceName();
        $controller = $this->dispatcher->getControllerName();

        $preArr = explode('\\', trim(str_replace('Imee\Controller', '', $nameSpace), '\\'));
        $pre = implode('/', array_map(function ($val) {
            return lcfirst($val);
        }, $preArr));

        if (!empty($pre)) {
            $controller = $pre . '/' . $controller;
        }

        $action = $this->dispatcher->getActionName();

        $map = $this->notLoginNeed;
        if (isset($map[$controller]) && in_array($action, $map[$controller])) {
            // 因为涉及到密码，所以这里要单独记录操作日志
            $post = array();
            if (isset($_POST["username"])) {
                $post["username"] = $_POST["username"];
            }
            $this->logger->warning('[ip,useid,url,get,post][' . $this->uid .
                '][' . $controller . '/' . $action . '],' .
                json_encode($_GET) . ',' . json_encode($post));

            return true;
        }

        // 记录操作日志
        if (!isset($this->notLog[$controller]) || !in_array($action, $this->notLog[$controller])) {
            $this->logger->warning('[ip,useid,url,get,post][' . $this->uid . '][' . $controller . '/' .
                $action . '],' . json_encode($_GET) . ',' . json_encode($_POST));
        }
        if ($this->uid <= 0) {
            $this->goToLogin();
            return false;
        }

        //剩下的都是要验证的
        $purview = $this->staffService->getUserAllAction($this->uid);
        if (!empty($purview)) {
            $this->session->set('purview', $purview);
        }

        $purviewName = $controller . '.' . $action;

        // 判断是否是低代码功能
        if (method_exists($this, 'checkAutoMenu')) {
            if ($this->checkAutoMenu($purviewName) && (in_array($purviewName, $this->notPermission) || in_array($controller, $this->notPermissionCtl))) {
                return true;
            }
        }
        if (in_array($purviewName, $this->notPermission) || in_array($controller, $this->notPermissionCtl)) {
            return true;
        }


        if (!is_array($purview) || !in_array($purviewName, $purview)) {
            throw new ApiException(ApiException::NO_PERMISS_ERROR);
        }
    }

    private function goToLogin()
    {
        throw new ApiException(ApiException::NO_LOGIN_ERROR);
    }

    protected function getExtPost()
    {
        $res = @file_get_contents("php://input");
        if (!empty($res)) {
            $data = json_decode($res, true);
            if (is_array($data)) {
                return $data;
            }
        }
        return false;
    }

    protected function redirect($url)
    {
        if (0 && $this->request->isAjax()) {
            //jsonp获取不到http header
            $this->response->setHeader('Json-Status', '302');
            return $this->response->setHeader('Json-Location', $this->url->get($url));
        } else {
            $prefix = substr($url, 0, 7);
            $isAbsolute = $prefix == 'http://' || $prefix == 'https:/' || substr($url, 0, 1) == '/';
            $url = $this->url->get($url, null, !$isAbsolute);
            return $this->response->redirect($url, true);
        }
    }

    protected function isAjax()
    {
        return $this->request->isAjax() || $this->isJsonp();
    }

    private function isJsonp()
    {
        return false;
//        return !is_null($this->_jsonpCallback);
    }

    protected function outputJson($data)
    {
        if ($this->isJsonp()) {
            return $this->response->setContent($this->_jsonpCallback . '(' . json_encode($data) . ');');
        } else {
            $out = json_encode($data, JSON_UNESCAPED_UNICODE);
            if ($out === false && $data && json_last_error() == JSON_ERROR_UTF8) {
                $out = json_encode($this->utf8ize($data), JSON_UNESCAPED_UNICODE);
            }
            return $this->response->setContent($out);
        }
    }

    private function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
        }
        return $mixed;
    }

    protected function outputSuccess($data = null, $options = null)
    {
        $array = array(
            'success' => true,
            'code'    => 0,
            'data'    => $data,
            'msg'     => '',
        );
        if ($options) {
            $array = array_merge($array, $options);
        }
        return $this->outputJson($array);
    }

    protected function outputError($code, $msg = null, $options = null)
    {
        $array = array(
            'success' => false,
            'code'    => $code,
            'msg'     => $msg
        );
        if ($options) {
            $array = array_merge($array, $options);
        }
        return $this->outputJson($array);
    }

    /**
     * 导出
     * @param $filePrefix
     * @param $cmdStr
     * @param array $paramData
     * @throws \Exception
     */
    protected function syncExportWork($filePrefix, $cmdStr, array $paramData = [])
    {
        $tmpTimeInt = time();
        $adminUid = $this->uid;
        $redis = new RedisHash(RedisBase::REDIS_ADMIN);
        if ($this->request->isAjax() || intval($this->request->getQuery('polling'))) {
            $name = $redis->get('hash.' . $cmdStr, $adminUid);
            $isOk = file_exists(ROOT . DS . 'public' . DS . $name . '.csv');
            if ($isOk) {
                return $this->outputSuccess(['is_ok' => $isOk, 'url' => EXPORTEXLS_DIR . $name . '.csv']);
            }
            return $this->outputSuccess(['is_ok' => $isOk, 'file_name' => $name . '.csv']);
        } else {
            $oldName = $redis->get('hash.' . $cmdStr, $adminUid); //先检查30s内是否有刷新
            $oldTime = !empty($oldName) ? substr($oldName, (strlen($filePrefix) + 1), 10) : 0;
            if ($tmpTimeInt - $oldTime <= 30) {
                $randName = md5($adminUid . $oldTime);
                $fileTime = $oldTime;
            } else {
                $randName = md5($adminUid . $tmpTimeInt);
                $fileTime = $tmpTimeInt;
            }
            $fullName = $filePrefix . '_' . $fileTime . '_' . $randName;
            $redis->set('hash.' . $cmdStr, [$adminUid => $fullName]);
            if (ENV == 'dev' || $tmpTimeInt - $oldTime > 30) {
                $client = new Client();
                $client->choose(EXPORTEXLS_QUEUE_NAME); //使用同一个队列记录,不同导出逻辑根据cmd字符串确定，30s只投递一次
                $mergeData = array_merge($paramData, ['admin_uid' => $adminUid, 'time_int' => $fileTime]);
                $client->set(array(
                    'cmd'  => $cmdStr,
                    'data' => $mergeData,
                ));
                $client->close();
            }
            return $this->outputSuccess(['file_name' => $fullName . '.csv']);
        }
    }
}
