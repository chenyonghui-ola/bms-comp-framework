<?php

namespace Imee\Service\Lesscode\Logic\Schema;

use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Service\Lesscode\Context\Schema\ListContext;
use Imee\Service\ModelSupportService;

class ListLogic
{
    /**
     * @var ListContext
     */
    protected $context;

    protected $conditions = [];

    /**
     * @var LesscodeSchemaPoint
     */
    protected $masterModel = LesscodeSchemaPoint::class;

    public function __construct(ListContext $context)
    {
        $this->context = $context;
    }

    public function getConditions()
    {
        $this->conditions = [
            '_model' => $this->masterModel
        ];

        if (!empty($this->context->type)) {
            $this->conditions['type'] = $this->context->type;
        }

        if (!empty($this->context->drive)) {
            $this->conditions['drive'] = $this->context->drive;
        }

        if (is_numeric($this->context->state) && $this->context->state >= 0) {
            $this->conditions['state'] = $this->context->state;
        }

        if (is_numeric($this->context->isSystem) && $this->context->isSystem >= 0) {
            $this->conditions['is_system'] = $this->context->isSystem;
        }
    }

    /**
     * 如果数据存在返回false
     * @return array
     */
    public function handle(): array
    {
        $list = ModelSupportService::getList($this->conditions);

        return $list->toArray();
    }
}