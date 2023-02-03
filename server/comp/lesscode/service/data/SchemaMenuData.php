<?php

namespace Imee\Service\Lesscode\Data;

use Imee\Helper\Traits\FactoryServiceTrait;
use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Service\Lesscode\Context\Modules\CreateContext;
use Imee\Service\Lesscode\FactoryService;

/**
 * @property LesscodeMenu schemaMenuModel
 * @method   LesscodeMenu findFirstByGuid($params)
 */
class SchemaMenuData
{
    use FactoryServiceTrait;

    protected $factorys = [
        FactoryService::class
    ];

    /**
     * 创建菜单
     * @param  CreateContext  $context
     */
    public function create(CreateContext $context)
    {

    }

    /**
     * 菜单更新
     * @param  CreateContext  $context
     */
    public function save(CreateContext $context)
    {

    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([(new self())->schemaMenuModel, $name], $arguments);
    }
}