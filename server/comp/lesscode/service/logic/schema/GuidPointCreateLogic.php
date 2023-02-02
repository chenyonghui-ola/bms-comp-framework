<?php

namespace Imee\Service\Lesscode\Logic\Schema;


use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Context\Menu\UpdateMenuContext;
use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\Traits\Curd\CreateTrait;
use Phalcon\Di;

class GuidPointCreateLogic extends GuidPointBaseLogic
{
    use CreateTrait;

    /**
     * @var LesscodeSchemaPoint
     */
    protected $masterModel = LesscodeSchemaPoint::class;

    /**
     * @var LesscodeSchemaPointConfig
     */
    protected $pointConfigModel = LesscodeSchemaPointConfig::class;

    /**
     * @var LesscodeSchemaConfig
     */
    protected $schemaConfigModel = LesscodeSchemaConfig::class;

    /**
     * @var LesscodeMenu
     */
    protected $schemaMenuModel = LesscodeMenu::class;

    /**
     * @var LesscodeSchemaPoint 点击的数据
     */
    protected $thisData;

    protected $params;

    /**
     * @var string 操作
     */
    protected $action;

    protected $actionClass = [
        'updateMenu' => ['context' => UpdateMenuContext::class, 'logic' => UpdateMenuLogic::class]
    ];


    public function onRewriteCreate(): bool
    {
        return true;
    }

    public function onCreate($params)
    {
        $this->params = $params;

        if (!empty($this->action)) {
            $this->runAction();
            return true;
        }
        $config = $this->schemaConfigModel::findFirst($params['id']);

        if (empty($config)) {
            [$code, $msg] = CurdException::ILLEGAL_GUID_ERROR;
            throw new CurdException($msg, $code);
        }

        $configJson = empty($params['config']) ? '' : json_encode(json_decode($params['config'], true), JSON_UNESCAPED_UNICODE);

        if (!empty($params['config']) && !in_array($params['config'], ['[]', '{}']) && (empty($configJson) || strtolower($configJson) == 'null')) {
            [$code, $msg] = CurdException::FIELD_JSON_FORMAT_ERROR;
            throw new CurdException($msg, $code);
        }

        $guid = $config->guid;

        $info = new $this->masterModel;
        $info->title = $params['title'] ?? '';
        $info->type = $params['type'] ?? '';
        $info->guid = $params['guid'] ?? $guid;
        $info->drive = $params['drive'] ?? AdapterSchema::DRIVE_MYSQL;
        $info->state = $params['state'] ?? 0;
        $info->is_system = $params['is_system'] ?? 0;
        $info->logic = isset($params['logic']) && !empty($params['logic']) ? trim($params['logic']) : '';
        $info->save();

        if ($info->id > 0) {

            $configInfo = new $this->pointConfigModel;
            $configInfo->guid = $info->guid;
            $configInfo->point_id = $info->id;
            $configInfo->config = !empty($configJson) ? $configJson : '{}';
            $configInfo->save();
        }

        // 如果开启功能 则检查菜单是否开启
        $this->saveModule($info);

        return true;
    }

    public function onBeforeCreate(&$params)
    {
        if (isset($this->actionClass[$params['type'] ?? ''])) {
            $this->action = $params['type'];
            return;
        }
        $this->validation($params);
    }

    public function onAfterCreate($params, $model)
    {

    }

    public function validation($params)
    {
        if (!isset($params['id']) || empty($params['id'])) {
            [$code, $msg] = CurdException::ILLEGAL_GUID_ERROR;
            throw new CurdException($msg, $code);
        }

        if (!isset($params['title']) || empty($params['title'])) {
            [$code, $msg] = CurdException::FIELD_NO_DATA_ERROR;
            throw new CurdException(sprintf($msg, '功能名称'), $code);
        }

        if (!isset($params['type']) || empty($params['type'])) {
            [$code, $msg] = CurdException::FIELD_NO_DATA_ERROR;
            throw new CurdException(sprintf($msg, '操作类型'), $code);
        }

        if (!isset($params['drive']) || empty($params['drive'])) {
            [$code, $msg] = CurdException::FIELD_NO_DATA_ERROR;
            throw new CurdException(sprintf($msg, '驱动类型'), $code);
        }

        if (!isset($params['drive']) || empty($params['drive'])) {
            [$code, $msg] = CurdException::FIELD_NO_DATA_ERROR;
            throw new CurdException(sprintf($msg, '驱动类型'), $code);
        }
    }

    private function runAction()
    {
        if (!isset($this->actionClass[$this->action])) {
            return;
        }

        $guid = Di::getDefault()->get('request')->getPost('guid');

        $context = new $this->actionClass[$this->action]['context'](['params' => $this->params, 'guid' => $guid]);
        $logic   = new $this->actionClass[$this->action]['logic']($context);
        $logic->handle();
    }
}