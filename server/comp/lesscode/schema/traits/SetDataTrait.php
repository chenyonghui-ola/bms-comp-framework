<?php


namespace Imee\Schema\Traits;


use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Service\Lesscode\Exception\CurdException;

trait SetDataTrait
{
    /**
     * @var int
     */
    private $pointIdSet;

    /**
     * @var LesscodeSchemaConfig
     */
    private $schemaSet;

    /**
     * @var LesscodeSchemaPoint
     */
    private $pointInfoSet;

    /**
     * @var LesscodeSchemaPointConfig
     */
    private $pointConfigSet;

    /**
     * @var LesscodeSchemaPoint
     */
    private $pointInfoListSet;

    /**
     * @var LesscodeSchemaPointConfig
     */
    private $pointConfigListSet;

    /**
     * @var string 字段key
     */
    private $fieldKey;

    /**
     * @var string 字段名称设置
     */
    private $fieldName;

    /**
     * @var string 组件设置
     */
    private $fieldComponent;

    /**
     * @var string 组件设置
     */
    private $fieldEnum;

    /**
     * @var string 字段枚举服务方法
     */
    private $fieldEnumFunc;

    /**
     * @var int 字段是否隐藏
     */
    private $fieldIsHidden;

    /**
     * @var int 字段是否支持排序
     */
    private $fieldListIsSort;

    /**
     * @var string 字段数据类型
     */
    private $fieldListDataType;

    /**
     * @var int 表单是否禁用
     */
    private $fieldFormIsDisabled;

    /**
     * @var int 字段是否必填
     */
    private $fieldListIsRequired;

    /**
     * @var LesscodeSchemaConfig
     */
    private $schemaSetModel = LesscodeSchemaConfig::class;

    /**
     * @var LesscodeSchemaPoint
     */
    private $pointSetModel = LesscodeSchemaPoint::class;

    /**
     * @var LesscodeSchemaPointConfig
     */
    private $pointConfigSetModel = LesscodeSchemaPointConfig::class;

    public function setPointId($value)
    {
        $this->pointIdSet = $value;

        $this->schemaSet      = $this->schemaSetModel::findFirstByGuid($this->guid);
        $this->pointInfoSet   = $this->pointSetModel::findFirstById($value);
        $this->pointConfigSet = $this->pointConfigSetModel::findFirstByPointId($this->pointInfoSet->id);

        if ($this->pointInfoSet->type !== static::POINT_LIST) {
            $this->pointInfoListSet   = $this->pointSetModel::findFirst([
                'conditions' => 'guid = :guid: and type = :type:',
                'bind'       => ['guid' => $this->guid, 'type' => static::POINT_LIST]
            ]);
            $this->pointConfigListSet = $this->pointConfigSetModel::findFirstByPointId($this->pointInfoListSet->id);
        }

        return $this;
    }

    public function setFieldKey($value)
    {
        $this->fieldKey = $value;

        return $this;
    }

    public function setFieldName($value)
    {
        $this->fieldName = $value;

        return $this;
    }

    public function setFieldComponent($value)
    {
        $this->fieldComponent = $value;

        return $this;
    }

    public function setFieldEnum($value)
    {
        // 解析判断是否是需要使用service
        if (!is_array($value)) {
            $arr = json_decode($value, true);

            if (isset($arr['service']) && isset($arr['method'])) {
                $this->fieldEnumFunc = $value;
            } else {
                $this->fieldEnum = $value;
            }

        } else {
            $this->fieldEnum = $value;
        }

        return $this;
    }

    public function setFieldIsHidden($value)
    {
        $this->fieldIsHidden = $value;

        return $this;
    }

    public function setFieldListIsSort($value)
    {
        $this->fieldListIsSort = $value;

        return $this;
    }

    public function setFieldListDataType($value)
    {
        $this->fieldListDataType = $value;

        return $this;
    }

    public function setFieldListIsRequired($value)
    {
        $this->fieldListIsRequired = $value;

        return $this;
    }

    public function setFieldFormIsDisabled($value)
    {
        $this->fieldFormIsDisabled = $value;

        return $this;
    }

    /**
     * 设置的数据保存
     */
    public function setDataSave()
    {
        $this->tableSetData()
            ->listSetData()
            ->listFilterSetData()
            ->listFieldsSetData()
            ->listExtraSetData()
            ->createSetData()
            ->modifySetData()
            ->deleteSetData()
            ->operateSetData()
            ->handle();

//        print_r($this);
        return true;
    }

    public function handle()
    {
        // 开始对比数据并且保存
        // 1、tableJson
        if ($this->schemaSet) {
            $this->schemaSet->table_config = json_encode($this->table, JSON_UNESCAPED_UNICODE);
            !empty($this->schemaSet->getChangedFields()) && $this->schemaSet->save();
        }

        // 2、list
        if ($this->pointInfoTypeIsList()) {
            $schemaList                   = !empty($this->pointConfigSet->config) ? json_decode($this->pointConfigSet->config, true) : [];
            $schemaList['list']           = $this->list;
            $this->pointConfigSet->config = json_encode($schemaList, JSON_UNESCAPED_UNICODE);

        } else {
            $schemaList                       = !empty($this->pointConfigListSet->config) ? json_decode($this->pointConfigListSet->config, true) : [];
            $schemaList['list']               = $this->list;
            $this->pointConfigListSet->config = json_encode($schemaList, JSON_UNESCAPED_UNICODE);
        }

        // 3、create
        if ($this->pointInfoTypeIsCreate()) {
            $this->pointConfigSet->config = json_encode($this->create, JSON_UNESCAPED_UNICODE);
        }

        // 4、modify
        if ($this->pointInfoTypeIsModify()) {
            $this->pointConfigSet->config = json_encode($this->modify, JSON_UNESCAPED_UNICODE);
        }

        // 5、保存数据
        if ($this->pointConfigSet && $this->pointConfigSet->getChangedFields()) {
            $this->pointConfigSet->save();
        }

        if ($this->pointConfigListSet && $this->pointConfigListSet->getChangedFields()) {
            $this->pointConfigListSet->save();
        }

    }

    private function tableSetData()
    {
        // 只有字段名称修改 才会更新
        if (empty($this->fieldName) || empty($this->fieldKey)) {
            return $this;
        }

        if ($this->table['fields'][$this->fieldKey]['comment'] != $this->fieldName) {
            $this->table['fields'][$this->fieldKey]['comment'] = $this->fieldName;
        }

        return $this;
    }

    private function listSetData()
    {
        // 组件
        if (!empty($this->fieldComponent)) {
            $this->list[$this->fieldKey]['component'] = $this->fieldComponent;
        }

        // 排序
        if (is_numeric($this->fieldListIsSort)) {
            if ($this->fieldListIsSort == 1) {
                // 判断字段是否支持排序
                $fields = $this->model::getTableFields();
                if (!empty($fields) && !in_array($this->fieldKey, $fields)) {
                    [$code, $msg] = CurdException::FIELD_NOT_SUPPORT_SORT;
                    throw new CurdException(sprintf($msg, $this->fieldKey), $code);
                }

                $this->list[$this->fieldKey]['sort'] = $this->fieldListIsSort;
            } else {
                if (isset($this->list[$this->fieldKey]['sort'])) {
                    $tmpList = $this->list[$this->fieldKey];
                    unset($tmpList['sort']);
                    $this->list[$this->fieldKey] = $tmpList;
                }
            }
        }

        // 枚举
        if (!empty($this->fieldEnum)) {
            $fieldEnum = @json_decode($this->fieldEnum, true);
            is_array($fieldEnum) && ($this->list[$this->fieldKey]['enum'] = $fieldEnum);
        }

        // 枚举服务
        if (!empty($this->fieldEnumFunc)) {
            $fieldEnumFunc = @json_decode($this->fieldEnumFunc, true);
            !empty($fieldEnumFunc) && ($this->list[$this->fieldKey]['func'] = $fieldEnumFunc);
        } else {
            if (isset($this->list[$this->fieldKey]['func'])) {
                unset($this->list[$this->fieldKey]['func']);
            }
        }

        // 是否隐藏
        if (is_numeric($this->fieldIsHidden)) {
            if ($this->fieldIsHidden == 1) {

                if ($this->pointInfoTypeIsList()) {
                    $this->list[$this->fieldKey]['hidden'] = $this->fieldIsHidden;
                }

            } else {

                if ($this->pointInfoTypeIsList() && isset($this->list[$this->fieldKey]['hidden'])) {
                    $tmpList = $this->list[$this->fieldKey];
                    unset($tmpList['hidden']);
                    $this->list[$this->fieldKey] = $tmpList;
                }
            }
        }

        // 数据类型
        if (!empty($this->fieldListDataType)) {
            $this->list[$this->fieldKey]['dataType'] = $this->fieldListDataType;
        } else {
            if (isset($this->list[$this->fieldKey]['dataType'])) {
                unset($this->list[$this->fieldKey]['dataType']);
            }
        }

        // 是否必填
        if (is_numeric($this->fieldListIsSort)) {
            if ($this->fieldListIsRequired == 1) {
                $this->list[$this->fieldKey]['required'] = $this->fieldListIsRequired;
            } else {
                if (isset($this->list[$this->fieldKey]['required'])) {
                    $tmpList = $this->list[$this->fieldKey];
                    unset($tmpList['required']);
                    $this->list[$this->fieldKey] = $tmpList;
                }
            }
        }

        return $this;
    }

    private function listFilterSetData()
    {
        return $this;
    }

    private function listFieldsSetData()
    {
        return $this;
    }

    private function listExtraSetData()
    {
        return $this;
    }

    private function createSetData()
    {
        if (is_numeric($this->fieldIsHidden)) {
            if ($this->fieldIsHidden == 1) {

                if ($this->pointInfoTypeIsCreate()) {
                    $this->create['fields'][$this->fieldKey]['hidden'] = $this->fieldIsHidden;
                }

            } else {

                if ($this->pointInfoTypeIsCreate() && isset($this->create['fields'][$this->fieldKey]['hidden'])) {
                    $tmpList = $this->create['fields'][$this->fieldKey];
                    unset($tmpList['hidden']);
                    $this->create['fields'][$this->fieldKey] = $tmpList;
                }

            }
        }

        // 是否禁用
        if (is_numeric($this->fieldFormIsDisabled)) {
            if ($this->fieldFormIsDisabled == 1) {

                if ($this->pointInfoTypeIsCreate()) {
                    $this->create['fields'][$this->fieldKey]['disabled'] = $this->fieldFormIsDisabled;
                }

            } else {

                if ($this->pointInfoTypeIsCreate() && isset($this->create['fields'][$this->fieldKey]['disabled'])) {
                    $tmpList = $this->create['fields'][$this->fieldKey];
                    unset($tmpList['disabled']);
                    $this->create['fields'][$this->fieldKey] = $tmpList;
                }
            }
        }

        return $this;
    }

    private function modifySetData()
    {
        if (is_numeric($this->fieldIsHidden)) {
            if ($this->fieldIsHidden == 1) {

                if ($this->pointInfoTypeIsModify()) {
                    $this->modify['fields'][$this->fieldKey]['hidden'] = $this->fieldIsHidden;
                }

            } else {

                if ($this->pointInfoTypeIsModify() && isset($this->modify['fields'][$this->fieldKey]['hidden'])) {
                    $tmpList = $this->modify['fields'][$this->fieldKey];
                    unset($tmpList['hidden']);
                    $this->modify['fields'][$this->fieldKey] = $tmpList;
                }
            }
        }

        // 是否禁用
        if (is_numeric($this->fieldFormIsDisabled)) {
            if ($this->fieldFormIsDisabled == 1) {

                if ($this->pointInfoTypeIsModify()) {
                    $this->modify['fields'][$this->fieldKey]['disabled'] = $this->fieldFormIsDisabled;
                }

            } else {

                if ($this->pointInfoTypeIsModify() && isset($this->modify['fields'][$this->fieldKey]['disabled'])) {
                    $tmpList = $this->modify['fields'][$this->fieldKey];
                    unset($tmpList['disabled']);
                    $this->modify['fields'][$this->fieldKey] = $tmpList;
                }
            }
        }

        return $this;
    }

    private function deleteSetData()
    {
        return $this;
    }

    private function operateSetData()
    {
        return $this;
    }

    private function pointInfoTypeIsList(): bool
    {
        return $this->pointInfoSet->type === static::POINT_LIST;
    }

    private function pointInfoTypeIsCreate(): bool
    {
        return $this->pointInfoSet->type === static::POINT_CREATE;
    }

    private function pointInfoTypeIsModify(): bool
    {
        return $this->pointInfoSet->type === static::POINT_MODIFY;
    }

    private function pointInfoTypeIsDelete(): bool
    {
        return $this->pointInfoSet->type === static::POINT_DELETE;
    }
}