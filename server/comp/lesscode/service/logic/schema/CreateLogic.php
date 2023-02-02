<?php

namespace Imee\Service\Lesscode\Logic\Schema;

use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Service\Lesscode\Context\Schema\CreateContext;

class CreateLogic
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
            $pointModel = new $this->masterModel;
            $pointModel->guid = $guid;
            $pointModel->title = $point['title'];
            $pointModel->type = $point['type'];
            $pointModel->drive = $point['drive'];
            $pointModel->state = $point['state'] ?? 1;
            $pointModel->save();

            if ($pointModel->id <= 0) {
                continue;
            }

            // 添加配置
            $this->createPointConfig($pointModel);
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

        // 优化创建配置里主键默认不显示
        if ($pointModel->type == $this->context->schemaClass::POINT_CREATE) {
            $config['fields'][$this->context->schemaClass->getPk()]['hidden'] = 1;
        }

        // 优化编辑配置里主键默认禁用
        if ($pointModel->type == $this->context->schemaClass::POINT_MODIFY) {
            $config['fields'][$this->context->schemaClass->getPk()]['disabled'] = 1;
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
}