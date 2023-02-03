<?php

namespace Imee\Service\Lesscode\Logic\Schema;


use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Traits\Curd\ListTrait;
use Phalcon\Di;

class GuidPointFieldsLogic
{
    use ListTrait;

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

    public function onGetFilter(&$filter)
    {
        $request = Di::getDefault()->get('request');
        $id = $request->getQuery('id') ?? 0;
        $filter['id'] = $id;
    }

    public function onListFormat(&$item)
    {

    }

    public function onAfterList($list): array
    {
        $item = current($list);
        $list = [];

        $request = Di::getDefault()->get('request');
        $id = $request->getQuery('id') ?? 0;

        $this->thisData = $this->masterModel::findFirstById($id);
        $this->thisDataConfig = $this->pointConfigModel::findFirstByPointId($id);

        $configArr = $this->getFieldsList($item);

        if (empty($configArr)) {
            return [];
        }

        $type   = $this->thisData->type;
        $isList = $type == AdapterSchema::POINT_LIST;

        // 字段列表
        $fieldList = $configArr['list'];

        $i = 1;

        foreach ($fieldList as $field => $item)
        {
            $enum = isset($item['enum']) && !empty($item['enum']) ? json_encode($item['enum'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '';

            // 如果存在service方法调用获取枚举 优先选择
            if (isset($item['func']) && !empty($item['func'])) {
                $enum = json_encode($item['func'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }

            $tmp = [
                'id'         => $this->thisData->id,
                'op_name'    => $this->thisData->title,
                'field_key'  => $field,
                'field_name' => $item['title'],
                'component'  => $item['component'],
                'enum'       => $enum,
                'is_hidden'  => $item['is_hidden'],
                'is_sort'    => $isList && isset($item['sort']) ? (string) intval($item['sort']) : '0',
                'is_disabled' => true === $isList ? '0' : $item['is_disabled'],
                'field_data_type' => $item['dataType'] ?? '',
                'is_required'     => (!isset($item['required']) || empty($item['required'])) ? '0' : '1', // 数据必填
            ];

            $list[] = $tmp;

            ++ $i;
        }

        return $list;
    }

    private function getFieldsList($item)
    {
        $pointInfo = $this->masterModel::findFirst([
            'conditions' => 'guid = :guid: and type = :type:',
            'bind' => ['guid' => $item['guid'], 'type' => AdapterSchema::POINT_LIST]
        ]);

        $config = $this->pointConfigModel::find([
            'conditions' => 'point_id = :point_id:',
            'bind' => ['point_id' => $pointInfo->id]
        ])->toArray();

        // 暂时只有1v1的关系
        $config = current($config);
        $configArr = !empty($config['config']) ? json_decode($config['config'], true) : [];

        if (empty($configArr)) {
            return [];
        }

        // 获取字段名称
        $schema = $this->schemaConfigModel::findFirstByGuid($item['guid']);
        $tableConfig = json_decode($schema->table_config, true)['fields'];

        foreach ($configArr['list'] as $field => &$value)
        {
            $value['title']       = $tableConfig[$field]['comment'] ?? '';
            $value['is_hidden']   = (string) $this->getIsHidden($field);
            $value['is_disabled'] = (string) $this->getIsDisabled($field);
        }

        return $configArr;
    }

    private function getIsHidden($field)
    {
        $config = !empty($this->thisDataConfig->config) ? json_decode($this->thisDataConfig->config, true) : [];

        if ($this->thisData->type == AdapterSchema::POINT_LIST) {
            $list = $config['list'] ?? [];
        } else {
            $list = $config['fields'] ?? [];
        }

        return (isset($list[$field]) && isset($list[$field]['hidden'])) ? (int) $list[$field]['hidden'] : 0;
    }

    private function getIsDisabled($field)
    {
        $config = !empty($this->thisDataConfig->config) ? json_decode($this->thisDataConfig->config, true) : [];

        if ($this->thisData->type == AdapterSchema::POINT_LIST) {
            return 0;
        }

        $list = $config['fields'] ?? [];

        return (isset($list[$field]) && isset($list[$field]['disabled'])) ? (int) $list[$field]['disabled'] : 0;
    }
}