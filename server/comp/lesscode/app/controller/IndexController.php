<?php
/**
 * 公共控制器接口
 */

namespace Imee\Controller\Lesscode;

class IndexController extends AdminBaseController
{
    protected function onConstruct()
    {
        $this->allowSort = array();
        parent::onConstruct();
    }
}
