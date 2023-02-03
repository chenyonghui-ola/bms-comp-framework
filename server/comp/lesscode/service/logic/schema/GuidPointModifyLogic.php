<?php

namespace Imee\Service\Lesscode\Logic\Schema;


use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Schema\AdapterSchema;
use Imee\Service\Domain\Service\Auth\ModulesService;
use Imee\Service\Helper;
use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\Traits\Curd\SaveTrait;
use Imee\Service\Domain\Context\Auth\Modules\InfoContext;

class GuidPointModifyLogic extends GuidPointBaseLogic
{
    use SaveTrait;

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


    public function onRewriteSave(): bool
    {
        return true;
    }

    public function onSave($params)
    {
        $id = $params['id'];

        $info = $this->masterModel::findFirstById($id);

        if (empty($info)) {
            [$code, $msg] = CurdException::NO_DATA_ERROR;
            throw new CurdException($msg, $code);
        }

        $params['title'] = trim($params['title'] ?? '');
        $params['drive'] = trim($params['drive'] ?? AdapterSchema::getDriveFuncDefault());
        $params['logic'] = trim($params['logic'] ?? '');

        !empty($params['title']) && $params['title'] != $info->title && $info->title = $params['title'];
        !empty($params['type'])  && $params['type']  != $info->type  && $info->type  = $params['type'];
        !empty($params['drive']) && $params['drive'] != $info->drive && $info->drive = $params['drive'];

        $params['state'] != $info->state && $info->state = $params['state'];
        $params['is_system'] != $info->is_system && $info->is_system = $params['is_system'];
        $params['logic'] != $info->logic && $info->logic = $params['logic'];

        if ($info->getChangedFields()) {
            $info->save();
        }

        $config = json_encode(json_decode($params['config'], true), JSON_UNESCAPED_UNICODE);

        if (!empty($params['config']) && !in_array($params['config'], ['[]', '{}']) && (empty($config) || strtolower($config) == 'null')) {
            [$code, $msg] = CurdException::FIELD_JSON_FORMAT_ERROR;
            throw new CurdException($msg, $code);
        }


        if (!empty($config) && strtolower($config) != 'null') {
            $configInfo = $this->pointConfigModel::findFirst([
                'conditions' => 'point_id = :point_id:',
                'bind' => ['point_id' => $id]
            ]);

            if (!empty($configInfo)) {
                $configInfo->config = $config;
                $configInfo->save();
            }
        }

        // 校验一下配置id是否有误 强制更新一下
        $this->checkUpdatePointConfig();

        // 如果开启功能 则检查菜单是否开启
        $this->saveModule($info);

        return true;
    }

    public function onBeforeSave(&$params, $model)
    {
        $this->validation($params);
    }

    public function onAfterSave($params, $model)
    {

    }

    public function validation($params)
    {
        if (!isset($params['id']) || empty($params['id'])) {
            [$code, $msg] = CurdException::ILLEGAL_GUID_ERROR;
            throw new CurdException($msg, $code);
        }
    }
}