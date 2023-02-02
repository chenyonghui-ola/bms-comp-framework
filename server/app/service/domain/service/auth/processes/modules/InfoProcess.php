<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Modules;

use Imee\Service\Domain\Context\Auth\Modules\InfoContext;
use Imee\Models\Cms\CmsModules;
use Phalcon\Di;
use Imee\Service\Helper;
use Imee\Service\Domain\Service\Auth\Processes\Modules\Traits\ParseControllerRouteTrait;

/**
 * 模块明细
 */
class InfoProcess
{
    use ParseControllerRouteTrait;
    private $context;

    public function __construct(InfoContext $context)
    {
        $this->context = $context;
    }

    private function buildWhere()
    {
        $where = [
            'condition' => [],
            'bind' => [],
        ];
        if (!empty($this->context->moduleId)) {
            $where['condition'][] = 'module_id = :module_id:';
            $where['bind']['module_id'] = $this->context->moduleId;
        }

        return $where;
    }

    public function handle()
    {
        $where = $this->buildWhere();
        $returnData = [];

        
        if (empty($where['condition'])) {
            return $returnData;
        }

        $where['condition'][] = 'system_id=:system_id:';
        $where['bind']['system_id'] = SYSTEM_ID;

        $model = CmsModules::findFirst([
            'conditions' => implode(' and ', $where['condition']),
            'bind' => $where['bind'],
        ]);

        return $this->format($model);
    }

    private function format($model)
    {
        $format = [];
        if (empty($model)) {
            return $format;
        }
        $format = $model->toArray();

        $format['id'] = $model->module_id;
        $format['code'] = $model->module_id;
        $format['name'] = $model->module_name;
        $format['path'] = '/'. strtolower($model->controller) . '/' . $model->action;
        
        if ($model->is_action == CmsModules::IS_ACTION_NO && $model->m_type == CmsModules::M_TYPE_PAGE) {
            $realDir = $this->context->dir;
            $pagePath = substr($model->controller, 0, strrpos($model->controller, '/'));
    
            $controllerRoutes = $this->getResult($realDir, $pagePath);


            $controllerRouteMap = array_column($controllerRoutes, null, 'path');
            $key = '/' . $model->controller . '/' . $model->action;

            if (!isset($controllerRouteMap[$key])) {
                return $format;
            }
            $format['module_name'] = $controllerRouteMap[$key]['name'];
            $format['name'] = $controllerRouteMap[$key]['name'];
            $format['points'] = $controllerRouteMap[$key]['points'];
        }
        return $format;
    }
}
