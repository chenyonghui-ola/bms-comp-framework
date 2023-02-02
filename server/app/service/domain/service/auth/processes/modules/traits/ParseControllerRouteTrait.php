<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Modules\Traits;

use Imee\Service\Helper;
use Imee\Exception\Auth\ModulesException;

trait ParseControllerRouteTrait
{
    private function parseControllerRoute($path, &$points)
    {
        $className = 'Imee\\Controller\\' . str_replace(' ', '\\', ucwords(str_replace('/', ' ', $path))) . 'Controller';
        $this->getLesscodeClassName($className);
        try {
            $reflector = new \ReflectionClass($className);
        } catch (\Exception $e) {
            Helper::debugger()->error(__CLASS__ .' : '. $e->getMessage());
            list($code, $msg) = ModulesException::PATH_NOEXISTS_ERROR;
            throw new ModulesException($msg, $code);
        }
        $pages = [];
        // $points = [];

        $methods = $reflector->getMethods();
        foreach ($methods as $method) {
            $docs = $method->getDocComment();
            $pageCode = $this->getDocTag($docs, '@page');
            if (!$pageCode) {
                continue;
            }
            $methodName = $method->getName();
            if (!isset($points[$pageCode])) {
                $points[$pageCode] = [];
            }

            $action = preg_replace('/(.+)Action/', '$1', $methodName);
            if (!isset($pages[$pageCode])) {
                $pageName = $this->getDocTag($docs, '@name');
                if ($pageName) { // 这里是一个页面的声明
                    $pages[$pageCode] = [
                        'name' => $pageName,
                        'action' => $action,
                        'controller' => $path,
                        'path' => '/' . $path . '/' . $action,
                    ];
                }
            }
            $pointName = $this->getDocTag($docs, '@point'); // 这里是一个功能点的声明
            if (!$pointName) {
                continue;
            }
            $points[$pageCode][] = [
                'name' => $pointName,
                'action' => $action,
                'controller' => $path,
                'path' => '/' . $path . '/' . $action,
            ];
        }
        
        if (empty($pages)) {
            return $pages;
        }

        foreach ($pages as $pageCode => &$page) {
            $page['points'] = isset($points[$pageCode]) ? $points[$pageCode] : [];
        }

        return $pages;
    }

    /**
     * 解析注解@开始，到行尾
     */
    protected function getDocTag($docs, $tag)
    {
        $tag = '@' . trim($tag, '@') . ' ';
        if (($pos = mb_strpos($docs, $tag)) !== false) {
            $pos += mb_strlen($tag);
            $docs = str_replace("\r", "\n", $docs);
            $pageName = mb_substr($docs, $pos, mb_strpos($docs, "\n", $pos) - $pos);
            return trim($pageName);
        }
        return '';
    }

    private function getFiles($realDir)
    {
        $fileList = [];
        $this->getLesscodeDir($realDir);
        $files = scandir($realDir);
        foreach ($files as $r) {
            if ($r == '.' || $r == '..' || $this->isLesscodeIgnore($r)) {
                continue;
            }
            $newDir = $realDir . '/' . $r;
            if (is_dir($newDir)) {
                $fileList = array_merge($fileList, $this->getFiles($newDir));
            } else {
                $fileList[] = $newDir;
            }
        }
        return $fileList;
    }

    private function getResult($realDir, $pagePath)
    {
        $format = [];
        $realMergeDir = $realDir;
        if (!empty($pagePath)) {
            $realMergeDir = $realDir . '/' . $pagePath;
        }

        $files = $this->getFiles($realMergeDir);
        $this->getLesscodeRealDir($realDir);

        $points = [];
        foreach ($files as $filename) {
            if (!preg_match('/Controller.php$/', $filename)) {
                continue;
            }
            $this->getLesscodeFilename($filename);
            $pathList = explode('/', trim(str_replace(['Controller.php', $realDir], '', $filename), '/'));
            $path = implode('/', array_map(function ($val) {
                return lcfirst($val);
            }, $pathList));
            $this->getLesscodePath($path);

            $item = $this->parseControllerRoute($path, $points);
            if (!$item) {
                continue;
            }
            $format = array_merge($format, $item);
        }
        
        if (!empty($format)) {
            foreach ($format as $k => &$v) {
                $v['points'] = $points[$k];
            }
        }
        return array_values($format);
    }

    private function getLesscodeDir(&$realDir)
    {
        $realDirArr = explode('/', $realDir);
        $end        = end($realDirArr);
        if ((!isset($this->context->page) || $this->context->page !== '低代码平台') && $end !== 'lesscode') {
            return;
        }

        $realDir = str_replace('app/controller/lesscode', 'lesscode/app/controller', $realDir);
    }

    private function getLesscodeRealDir(&$realDir)
    {
        if (!isset($this->context->page) || $this->context->page !== '低代码平台') {
            return;
        }

        $realDir = str_replace(['app/controller', 'lesscode/app/controller'], 'lesscode/app/controller', $realDir);
    }

    private function isLesscodeIgnore($r)
    {
        if (!isset($this->context->page) || $this->context->page !== '低代码平台') {
            return false;
        }

        if (false !== stripos($r, 'AdminBaseController')) {
            return true;
        }

        return false;
    }

    private function getLesscodePath(&$path)
    {
        if ($path !== 'base' && false === stripos($path, '/')) {
            $path = 'lesscode/' . $path;
        }
    }

    private function getLesscodeClassName(&$className)
    {
        if (!isset($this->context->page) || $this->context->page !== '低代码平台') {
            return false;
        }

        if (false === stripos($className, 'Imee\\Controller\\Lesscode\\')) {
            $className = str_replace('Imee\\Controller\\', 'Imee\\Controller\\Lesscode\\', $className);
        }
    }

    private function getLesscodeFilename(&$filename)
    {
        if (!isset($this->context->page) || $this->context->page !== '低代码平台') {
            $filename = str_replace('/lesscode', '', $filename);
        }
    }
}
