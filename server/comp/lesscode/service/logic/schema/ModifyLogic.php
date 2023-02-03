<?php

namespace Imee\Service\Lesscode\Logic\Schema;

use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Service\Lesscode\Context\Schema\CreateContext;
use Imee\Service\Lesscode\HelperService;

class ModifyLogic
{
    /**
     * @var CreateContext
     */
    protected $context;

    protected $conditions = [];

    /**
     * @var LesscodeSchemaPoint
     */
    protected $masterModel = LesscodeSchemaPoint::class;

    /**
     * @var LesscodeSchemaPointConfig
     */
    protected $configModel = LesscodeSchemaPointConfig::class;

    public function __construct(CreateContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        $guid   = $this->context->guid;
        $points = $this->getPoints();

        foreach ($points as $point)
        {
            $pointModel = $this->masterModel::findFirst([
                'conditions' => 'guid = :guid: and type = :type:',
                'bind' => ['guid' => $guid, 'type' => $point['type']]
            ]);

            if (empty($pointModel)) {
                $pointModel = new $this->masterModel;
                $pointModel->guid = $guid;
                $pointModel->type = $point['type'];
                $pointModel->title = $point['title'];
                $pointModel->drive = $point['drive'];
                $pointModel->state = $point['state'] ?? 1;
                $pointModel->save();
            }

            if ($pointModel->id <= 0) {
                continue;
            }

            // 添加配置
            $this->modifyPointConfig($pointModel);
        }

        return;
    }

    protected function createPointConfig($pointModel)
    {
        $pointConfigModel = new $this->configModel;
        $pointConfigModel->guid = $this->context->guid;
        $pointConfigModel->point_id = $pointModel->id;

        $config = [];

        if ($pointModel->type == $this->context->schemaClass::POINT_LIST) {
            $config['list'] = $this->context->schemaClass->list ?? [];
            $config['filter'] = $this->context->schemaClass->listFilter ?? [];
            $config['fields'] = $this->context->schemaClass->listFields ?? [];
        }

        $pointConfigModel->config = json_encode(!empty($config) ? $config : new \stdClass());
        $pointConfigModel->save();
    }

    protected function modifyPointConfig($pointModel)
    {
        $pointConfigModel = $this->configModel::findFirstByPointId($pointModel->id);

        if (empty($pointConfigModel)) {
            $this->createPointConfig($pointModel);
            return;
        }

        // 只有列表配置需要编辑 其他配置不可修改 通过低代码功能管理配置
        if ($pointModel->type != $this->context->schemaClass::POINT_LIST) {
            return;
        }

        // 编辑配置
        $config = $pointConfigModel->config;

        if (!empty($config)) {
            $config = json_decode($config, JSON_UNESCAPED_UNICODE);
            $config['list'] = $this->mergeSchemaList((array) $config['list'], $this->context->schemaClass->list);
            $config['filter'] = $this->mergeSchemaListFilter((array) $config['filter'], $this->context->schemaClass->listFilter);

        } else {
            $config = [
                'list'   => $this->context->schemaClass->list ?? [],
                'filter' => $this->context->schemaClass->listFilter ?? [],
                'fields' => $this->context->schemaClass->listFields ?? []
            ];
        }

        $pointConfigModel->config = json_encode(!empty($config) ? $config : new \stdClass());
        $pointConfigModel->save();
    }


    private function getPoints(): array
    {
        return [
            ['title' => '列表', 'type' => $this->context->schemaClass::POINT_LIST,   'drive' => $this->context->schemaClass::getDriveFuncDefault(), 'state' => 1],
            ['title' => '创建', 'type' => $this->context->schemaClass::POINT_CREATE, 'drive' => $this->context->schemaClass::getDriveFuncDefault(), 'state' => 0],
            ['title' => '编辑', 'type' => $this->context->schemaClass::POINT_MODIFY, 'drive' => $this->context->schemaClass::getDriveFuncDefault(), 'state' => 0],
            ['title' => '删除', 'type' => $this->context->schemaClass::POINT_DELETE, 'drive' => $this->context->schemaClass::getDriveFuncDefault(), 'state' => 0],
        ];
    }

    private function mergeSchemaList($before, $after)
    {
        if (empty($before) && empty($after)) {
            return new \stdClass();
        }

        $list = [];

        foreach ($after as $field => $item)
        {
            if (!isset($before[$field])) {
                $list[$field] = $item;
                continue;
            }

            $tmpItem = $before[$field];

            // 除了组建类型和枚举 其他直接覆盖
            if (isset($tmpItem['component']) && !HelperService::isExpandComponent($tmpItem['component'])) {
                unset($tmpItem['component']);
            }

            // 如果之前设置有枚举 后面提交数据没有枚举的情况则不覆盖
            if (isset($item['enum']) && !empty($item['enum'])) {
                unset($tmpItem['enum']);
            }

            $list[$field] = array_merge($item, $tmpItem);
        }

        return $list;
    }

    private function mergeSchemaListFilter($before, $after)
    {
        if (empty($before) && empty($after)) {
            return new \stdClass();
        }

        $list = [];

        foreach ($after as $field => $item) {
            if (!isset($before[$field])) {
                $list[$field] = $item;
                continue;
            }

            $tmpItem = $before[$field];

            // 除了组建类型和枚举 其他直接覆盖
            if (isset($tmpItem['component']) && !HelperService::isExpandComponent($tmpItem['component'])) {
                unset($tmpItem['component']);
            }

            // 如果之前设置有枚举 后面提交数据没有枚举的情况则不覆盖
            if (isset($item['enum']) && !empty($item['enum'])) {
                unset($tmpItem['enum']);
            }

            unset($tmpItem['enum']);

            $list[$field] = array_merge($item, $tmpItem);
        }

        return $list;
    }
}