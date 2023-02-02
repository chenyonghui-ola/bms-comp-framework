<?php

namespace Imee\Service\Lesscode\Schema;

use Imee\Service\Lesscode\BaseService;
use Imee\Service\Lesscode\Constant\CreateTableConstant;
use Imee\Service\Lesscode\Constant\FieldTypeConstant;
use Imee\Service\Lesscode\HelperService;

class FieldService extends BaseService
{
    public function getPk()
    {
        return $this->schema->getPk();
    }

    public function getTableFields(): array
    {
        $fields = $this->schema->getTableFields();

        return array_keys($fields);
    }

    public function getTableFieldsList(): array
    {
        $fields = $this->schema->getListConfig();

        return $fields;
    }

    public function getTableFieldsAll(): array
    {
        $fields = $this->schema->getTableFields();

        return $fields;
    }

    public function getTableModifyFields(): array
    {
        $fields = $this->schema->getModifyFields();

        return $fields;
    }

    /**
     * 获取页面需要展示的字段
     */
    public function getListShowFields(): string
    {
        $fieldArr = [];
        $fields   = $this->schema->getTableFields();

        foreach ($fields as $key => $field) {
            if (isset($field['show']) && $field['show'] === false) {
                unset($fields[$key]);
            }

            $fieldArr[] = $key;
        }

        unset($fields);

        return implode(',', $fieldArr);
    }

    /**
     * 处理列表附加数据
     * @param  array  $list
     * @return array
     */
    public function setAttach(array $list): array
    {
        // 查询额外关联字段
        $listFields = $this->schema->getListFields();

        if (empty($listFields)) {
            return $list;
        }

        $attachs = [];

        foreach ($listFields as $field => $listField) {
            // 展示字段名称和数据表字段一样
            if (count($listField) == 2) {
                [$masterFields, $attachModel] = $listField;

                $attachField = $field;
            } else {
                [$masterFields, $attachField, $attachModel] = $listField;
            }

            is_array($masterFields) ?
                ([$masterFields, $joinFields] = $masterFields) :
                $joinFields = $masterFields;

            // 合并一个数据库的数据
            $key = md5($masterFields . '_' . $joinFields . $attachModel);

            $attachs[$key]['config']          = [$masterFields, $joinFields, $attachModel];
            $attachs[$key]['fields'][]        = (($attachField == $field) ? $field : ($attachField . ' AS ' . $field));
            $attachs[$key]['fields_source'][] = $field;
        }

        unset($listFields);

        $attachAll = [];

        foreach ($attachs as $attach) {
            [$masterFields, $joinFields, $attachModel] = $attach['config'];

            $masterIds = array_column($list, $masterFields);

            $attachData = $this->setAttachGetData($attachModel, $attach, $joinFields, $masterIds);

            if (!empty($attachData)) {
                $attachData = array_column($attachData, null, $joinFields);
            }

            foreach ($attach['fields_source'] as $afield) {
                $attachAll[$afield]         = $attach;
                $attachAll[$afield]['data'] = $attachData;
            }
        }

        foreach ($list as $key => $item) {
            foreach ($attachAll as $field => $attachData) {
                [$masterFields, $joinFields, $attachModel] = $attachData['config'];

                $list[$key][$field] = isset($attachData['data'][$item[$masterFields]]) ? $attachData['data'][$item[$masterFields]][$field] : '';
            }
        }

        unset($attachAll, $attachs, $attach, $attachData);

        return $list;
    }

    public function setAttachGetData($attachModel, $attach, $joinFields, $masterIds)
    {
        return $attachModel::find([
            'columns'    => implode(',', array_merge($attach['fields'], [$joinFields])),
            'conditions' => "{$joinFields} IN ({{$joinFields}:array})",
            'bind'       => [$joinFields => $masterIds]
        ])->toArray();
    }

    /**
     * 格式化字段
     */
    public function formatFields($fields, $data)
    {
        // 查询字段是否是 date 类型
        foreach ($data as $field => &$value)
        {
            if (isset($fields[$field]) && isset($fields[$field]['component'])) {

                // 时间字段都需要处理成时间格式
                if (HelperService::isTime($fields[$field]['component']) && is_numeric($value) && $value > 0) {
                    $value = date($fields[$field]['dateFormat'] ?? 'Y-m-d H:i:s', $value);
                }

                if (HelperService::isTime($fields[$field]['component']) && is_numeric($value) && $value == 0) {
                    $value = '';
                }

                // 多选需要格式化成数组
                if (HelperService::isMultiple($fields[$field]['component']) && !empty($value)) {
                    $value = explode(',', $value);
                    $value = array_map('strval', $value);
                }
            }

            (!is_array($value) && !is_object($value)) && $value = strval($value);
        }

        return $data;
    }

    /**
     * 格式化字段
     */
    public function formatFieldsMore($fields, $data)
    {
        foreach ($fields as $field => $item)
        {
            if (!isset($data[$field])) {
                continue;
            }

            // 时间字段都需要处理成时间格式
            if (HelperService::isTime($item['component']) && is_numeric($data[$field]) && $data[$field] > 0) {
                $data[$field] = date($item['dateFormat'] ?? 'Y-m-d H:i:s', $data[$field]);
            }

            if (HelperService::isTime($item['component']) && is_numeric($data[$field]) && $data[$field] == 0) {
                $data[$field] = '';
            }
        }

        return $data;
    }

    /**
     * 格式化字段
     */
    public function formatFieldsEnum($fields, $data)
    {
        foreach ($fields as $field => $item)
        {
            if (!isset($data[$field])) {
                continue;
            }

            // todo 多选需要格式化
            if (HelperService::isMultiple($item['component']) && !empty($data[$field])) {
//                    $data[$field] = explode(',', $data[$field]);
//                    $data[$field] = array_map('strval', $data[$field]);
            } elseif (HelperService::isEnum($item['component'])) {
                // 单选格式化数据
                if (isset($item['func']) && !empty(isset($item['func']))) {
                    $enum = HelperService::getEnumFunc($item['func'], false);
                } else if (isset($item['enum']) && !empty($item['enum'])) {
                    $enum = HelperService::getEnumFormat($item['enum']);
                }

                $data[$field] = $enum[$data[$field]] ?? '';
            }
        }

        return $data;
    }

    public function isConvertComponent($component)
    {
        return HelperService::isTime($component) || HelperService::isMultiple($component) || HelperService::isEnum($component);
    }

    public static function getDefaultSymbol()
    {
        return FieldTypeConstant::DEFAULT_CONDITION_SYMBOL;
    }

    public static function getPkFieldName()
    {
        return CreateTableConstant::PK_NAME;
    }

    public static function getPkField()
    {
        return CreateTableConstant::PK_FIELD;
    }

    public static function getAttachFields()
    {
        return CreateTableConstant::ATTACH_FIELDS;
    }
}