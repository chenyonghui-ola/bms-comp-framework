<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Modules;

use Imee\Service\Domain\Context\Auth\Modules\CreateContext;
use Imee\Models\Cms\CmsModules;
use Phalcon\Di;
use Imee\Service\Helper;
use Imee\Exception\Auth\ModulesException;
use Imee\Service\Domain\Service\Auth\Processes\Modules\Traits\ParseControllerRouteTrait;

/**
 * 模块创建
 */
class CommonVeryfyProcess
{
    use ParseControllerRouteTrait;
    protected $parentModule;
    protected $context;
    protected $controllerRoute;
    public function __construct(CreateContext $context)
    {
        $this->context = $context;
    }

    protected function vefiry()
    {
        if (!empty($this->context->parentModuleId)) {
            $this->parentModule = CmsModules::findFirst([
                'conditions' => 'module_id = :module_id: and system_id=:system_id:',
                'bind' => array(
                    'module_id' => $this->context->parentModuleId,
                    'system_id' => SYSTEM_ID,
                ),
                'order' => 'module_id desc'
            ]);
            if (empty($this->parentModule)) {
                list($code, $msg) = ModulesException::PARENT_MODULE_NOEXIST_ERROR;
                throw new ModulesException($msg, $code);
            }
        }

        $mType = array_search($this->context->type, CmsModules::$displayMType);
        
        if ($mType == CmsModules::M_TYPE_PAGE) {
            $this->checkRoute();
        }
    }

    private function checkRoute()
    {
        $realDir = $this->context->dir;
        $pagePath = substr($this->context->controller, 0, strrpos($this->context->controller, '/'));

        $controllerRoutes = $this->getResult($realDir, $pagePath);
        //查询现有的
        // $controllerRoutes = $this->parseControllerRoute($this->context->controller);

        if (empty($controllerRoutes)) {
            list($code, $msg) = ModulesException::ROUTE_NOEXISTS_ERROR;
            throw new ModulesException($msg, $code);
        }

        $controllerRouteMap = array_column($controllerRoutes, null, 'path');
        $key = '/' . strtolower($this->context->controller) . '/' . $this->context->action;

        if (!isset($controllerRouteMap[$key])) {
            list($code, $msg) = ModulesException::ROUTE_NOEXISTS_ERROR;
            throw new ModulesException($msg, $code);
        }

        $this->controllerRoute = $controllerRouteMap[$key];
    }
}
