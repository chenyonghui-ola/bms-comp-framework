<?php
/**
 * 公共控制器接口
 */

namespace Imee\Controller\Lesscode;

use Imee\Controller\BaseController;
use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\InitDataService;

class GuidlistController extends BaseController
{
    protected function onConstruct()
    {
        parent::onConstruct();

    }

    /**
     * @page guidList
     * @name 低代码平台-功能管理
     */
    public function mainAction()
    {

    }

    /**
     * @page guidList
     * @point 更新配置
     */
    public function updateConfigAction()
    {
        $guid = $this->request->getPost('guid', 'trim', '');

        if (empty($guid)) {
            CurdException::throwException(CurdException::ILLEGAL_ERROR);
        }

        $service = new InitDataService($guid, 0);
        return $this->outputSuccess($service->handle());
    }

}
