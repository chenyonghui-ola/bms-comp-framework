<?php

namespace Imee\Controller;

use Imee\Service\Lesscode\Context\GuidContext;
use Imee\Service\Lesscode\Context\ListConfigContext;
use Imee\Service\Lesscode\CurdService;
use Imee\Service\Lesscode\Schema\SchemaService;
use Imee\Service\Lesscode\Validations\CurdValidation;
use Imee\Service\Lesscode\Validations\GuidValidation;

abstract class AdminBaseController extends BaseController
{
    protected function onConstruct()
    {
        parent::onConstruct();

        $request = array_merge($this->request->getQuery(), $this->request->getPost());
        $this->guid = $request['guid'] ?? '';
    }

    /**
     * 获取列表配置接口
     */
    public function schemaConfigAction()
    {
        GuidValidation::make()->validators($this->request->getPost());

        $service = new SchemaService();
        $context = new GuidContext($this->request->getPost());

        return $this->outputJson($service->getConfig($context));
    }

    /**
     * 获取列表配置接口
     */
    public function listConfigAction()
    {
        GuidValidation::make()->validators(array_merge($this->request->getQuery(), $this->request->getPost()));

        $service = new SchemaService();
        $context = new ListConfigContext(array_merge($this->request->getQuery(), $this->request->getPost(), ['purview' => $this->session->get('purview'), 'admin_uid' => $this->uid]));

        return $this->outputJson($service->getListConfig($context));
    }

    /**
     * 通用列表
     */
    public function listAction()
    {
        CurdValidation::make()->validators(array_merge($this->request->getQuery(), $this->request->getPost()));

        $guid = $this->request->getQuery('guid');
        $guid = !empty($guid) ? $guid : $this->request->getPost('guid');

        $service = new CurdService();
        $res = $service->getlist(array_merge($this->request->getQuery(), $this->request->getPost(), ['guid' => $guid, 'admin_uid' => $this->uid]));

        return $this->outputSuccess($res['list'], ['total' => $res['total']]);
    }

    /**
     * 通用表单添加
     */
    public function createAction()
    {
        CurdValidation::make()->validators(array_merge($this->request->getQuery(), $this->request->getPost()));

        $service = new CurdService();
        $res = $service->create(array_merge($this->request->getQuery(), $this->request->getPost(), ['admin_uid' => $this->uid]));

        return $this->outputSuccess($res);
    }

    /**
     * 通用表单编辑
     */
    public function saveAction()
    {
        CurdValidation::make()->validators(array_merge($this->request->getQuery(), $this->request->getPost()));

        $service = new CurdService();
        $res = $service->modify(array_merge($this->request->getQuery(), $this->request->getPost(), ['admin_uid' => $this->uid]));

        return $this->outputSuccess($res);
    }

    /**
     * 通用表单编辑
     */
    public function modifyAction()
    {
        CurdValidation::make()->validators(array_merge($this->request->getQuery(), $this->request->getPost()));

        $service = new CurdService();
        $res = $service->modify(array_merge($this->request->getQuery(), $this->request->getPost(), ['admin_uid' => $this->uid]));

        return $this->outputSuccess($res);
    }

    /**
     * 通用表单删除
     */
    public function deleteAction()
    {
        CurdValidation::make()->validators(array_merge($this->request->getQuery(), $this->request->getPost()));

        $service = new CurdService();
        $res = $service->delete(array_merge($this->request->getQuery(), $this->request->getPost(), ['admin_uid' => $this->uid]));

        return $this->outputSuccess($res);
    }

    /**
     * 通用导出
     */
    public function exportAction()
    {
        CurdValidation::make()->validators(array_merge($this->request->getQuery(), $this->request->getPost(), ['admin_uid' => $this->uid]));

        return $this->syncExportWork($this->guid, $this->guid . '.export', array_merge($this->request->getQuery(), $this->request->getPost()));
    }
}