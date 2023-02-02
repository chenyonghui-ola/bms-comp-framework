<?php

namespace Imee\Controller\Lesscode;

use Imee\Controller\BaseController;
use Imee\Service\Lesscode\Traits\Help\CommonCtlTrait;
use Imee\Service\Lesscode\Traits\Curd\CurdBase;
use Imee\Service\Lesscode\Context\GuidContext;
use Imee\Service\Lesscode\Context\ListConfigContext;
use Imee\Service\Lesscode\CurdService;
use Imee\Service\Lesscode\Schema\SchemaService;
use Imee\Service\Lesscode\Validations\CurdValidation;
use Imee\Service\Lesscode\Validations\GuidValidation;
use Imee\Service\Lesscode\MenuService;

abstract class AdminBaseController extends BaseController
{
    use CurdBase, CommonCtlTrait;

    protected $requestParams;

    /**
     * @var string 低代码菜单转化实际模块名称
     */
    protected $realModule;

    /**
     * @var string 低代码菜单转化实际控制器名称
     */
    protected $realController;

    /**
     * @var string 低代码菜单转化实际方法名称
     */
    protected $realAction;

    // 低代码action公用权限
    private $lesscodeNotPermissionAction = [
        'listConfig'   => 'list',
        'listFilter'   => 'list',
        'schemaConfig' => 'list',
    ];

    protected function onConstruct()
    {
        parent::onConstruct();

        $get  = $this->request->getQuery();
        $post = $this->request->getPost();

        if (isset($post['guid']) && !empty($post['guid'])) {
            $this->guid = $post['guid'] ?? '';
        } elseif (isset($get['guid']) && !empty($get['guid'])) {
            $this->guid = $get['guid'] ?? '';
        }

        $this->requestParams = array_merge($get, $post, ['guid' => $this->guid, 'admin_uid' => $this->uid]);
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
        GuidValidation::make()->validators($this->requestParams);

        $service = new SchemaService();
        $context = new ListConfigContext(array_merge($this->request->getQuery(), $this->request->getPost(), ['purview' => $this->session->get('purview'), 'admin_uid' => $this->uid]));

        return $this->outputJson($service->getListConfig($context));
    }

    /**
     * 通用列表
     */
    public function listAction()
    {
        CurdValidation::make()->validators($this->requestParams);

        // todo lesscode 分页等数据

        $guid = $this->request->getQuery('guid');
        $guid = !empty($guid) ? $guid : $this->request->getPost('guid');

        $service = new CurdService();
        $res     = $service->getlist($this->requestParams);

        return $this->outputSuccess($res['list'], ['total' => $res['total']]);
    }

    /**
     * 通用表单添加
     */
    public function createAction()
    {
        CurdValidation::make()->validators($this->requestParams);

        // todo lesscode 数据校验 validations

        $service = new CurdService();
        $res     = $service->create($this->requestParams);

        return $this->outputSuccess($res);
    }

    /**
     * 通用表单编辑
     */
    public function saveAction()
    {
        CurdValidation::make()->validators($this->requestParams);

        $service = new CurdService();
        $res     = $service->modify($this->requestParams);

        return $this->outputSuccess($res);
    }

    /**
     * 通用表单编辑
     */
    public function modifyAction()
    {
        CurdValidation::make()->validators($this->requestParams);

        $service = new CurdService();
        $res     = $service->modify($this->requestParams);

        return $this->outputSuccess($res);
    }

    /**
     * 通用表单删除
     */
    public function deleteAction()
    {
        CurdValidation::make()->validators($this->requestParams);

        $service = new CurdService();
        $res     = $service->delete($this->requestParams);

        return $this->outputSuccess($res);
    }

    /**
     * 通用导出
     */
    public function exportAction()
    {
        CurdValidation::make()->validators($this->requestParams);

        return $this->syncExportWork($this->guid, $this->guid . '.lesscode.export', $this->requestParams);
    }

    /**
     * 检查是否是低代码创建菜单
     * @param $purviewName
     */
    protected function checkAutoMenu(&$purviewName)
    {
        $purviewArr = explode('.', $purviewName);
        $action     = end($purviewArr);

        if ($purviewArr[0] != 'lesscode/index') {

            ENV == 'dev' && $this->notPermission = array_merge($this->notPermission, [
                'lesscode/form.create',
                'lesscode/form.update',
                'lesscode/form.check',
            ]);

            return false;
        }

        $guid = $this->request->getQuery('guid', 'trim', '');

        if (empty($guid)) {
            $guid = $this->request->getPost('guid', 'trim', '');
        }

        if (empty($guid)) {
            return false;
        }

        // 判断是否是低代码菜单
        $menuService = new MenuService();
        $context     = new GuidContext(['guid' => $guid]);

        if (true === $menuService->checkCreate($context)) {
            return false;
        }

        // 获取菜单
        $data = $menuService->getInfo($context);

        if (empty($data)) {
            return false;
        }

        // 判断是否存在实体控制器，如果存在转发请求
        [$this->realModule, $this->realController] = explode('/', $data['controller']);
        $this->realAction = $action;

        $controllerNameSpace = '\\Imee\\Controller\\' . ucfirst($this->realModule) . '\\' . ucfirst($this->realController) . 'Controller';

        if (class_exists($controllerNameSpace) && method_exists($controllerNameSpace, $this->realAction . 'Action')) {
            $this->dispatcher->forward([
                'namespace'  => 'Imee\Controller\\' . ucfirst($this->realModule),
                'controller' => $this->realController,
                'action'     => $this->realAction,
                'params'     => $this->dispatcher->getParams()
            ]);
        }

        // 低代码部分菜单无需权限控制
        if (isset($this->lesscodeNotPermissionAction[$action])) {
            // 如果是低代码菜单 需要重写 $purviewName
            $purviewName = $data['controller'] . '.' . $this->lesscodeNotPermissionAction[$action];
            return false;
        }

        // 如果是低代码菜单 需要重写 $purviewName
        $controller = $data['controller'];

        // 如果是低代码菜单 需要重写 $purviewName
        $purviewName = $controller . '.' . $action;

        return true;
    }
}