<?php

namespace Imee\Service\Lesscode\Logic\Schema;


use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Traits\Curd\SaveTrait;
use Phalcon\Di;

class GuidPointFieldsModifyLogic
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
     * @var LesscodeSchemaPoint 点击的数据
     */
    protected $thisData;

    /**
     * @var LesscodeSchemaPointConfig 点击的数据配置
     */
    protected $thisDataConfig;

    public function onRewriteSave(): bool
    {
        return true;
    }

    public function onSave($params)
    {
        $this->thisData       = $this->masterModel::findFirstById($params['id']);
        $this->thisDataConfig = $this->pointConfigModel::findFirstByPointId($this->thisData->id);

        $schema = new AdapterSchema($this->thisData->guid);

        $schema->setPointId($params['id'])
            ->setFieldKey($params['field_key'])
            ->setFieldName($params['field_name'])
            ->setFieldComponent($params['component'])
            ->setFieldEnum($params['enum'] ?? [])
            ->setFieldFormIsDisabled($params['is_disabled'] ?? '')
            ->setFieldIsHidden($params['is_hidden'] ?? '')
            ->setFieldListIsSort($params['is_sort'] ?? '')
            ->setFieldListDataType($params['field_data_type'] ?? '')
            ->setFieldListIsRequired($params['is_required'] ?? 0)
            ->setDataSave();
    }

    public function onBeforeSave(&$params, $model)
    {

    }

    public function onAfterSave($params, $model)
    {

    }
}