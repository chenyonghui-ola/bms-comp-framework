<?php
/**
 * select枚举下拉,统一用这个
 */

namespace Imee\Controller\Common;

use Imee\Controller\BaseController;
use Imee\Service\StatusService;

class EnumController extends BaseController
{
    const PARAMS_FORMAT = [null, 'label,value'];

    private $classMap = [
        'state' => ['class' => StatusService::class, 'method' => 'getState', 'params' => self::PARAMS_FORMAT],
    ];

    public function getListAction()
    {
        $type = $this->request->getQuery('type', 'trim', '');
        $res = [];
        if (empty($type)) {
            return $this->outputSuccess($res);
        }

        $typeArr = explode(',', $type);
        foreach ($typeArr as $item) {
            if (!isset($this->classMap[$item])) {
                continue;
            }

            $class = $this->classMap[$item]['class'];
            $method = $this->classMap[$item]['method'];
            $params = $this->classMap[$item]['params'];

            $refiection = new \ReflectionMethod($class, $method);
            if ($refiection->isStatic()) {
                $res[$item] = call_user_func_array([$class, $method], $params);
            } else {
                $res[$item] = call_user_func_array([new $class, $method], $params);
            }
        }

        $res = translate_output($res, $this->lang);
        return $this->outputSuccess($res);
    }
}
