<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Role;

use Imee\Service\Domain\Context\Auth\Role\ListContext;
use Imee\Models\Cms\CmsRoles;
use Phalcon\Di;
use Imee\Service\Domain\Service\Abstracts\NormalListAbstract;
use Imee\Service\Domain\Service\Auth\Processes\Role\Traits\RoleModelTrait;

/**
 * 角色列表
 */
class ListProcess extends NormalListAbstract
{
    use RoleModelTrait;
    protected $context;
    protected $masterClass;
    protected $query;

    public function __construct(ListContext $context)
    {
        $this->context = $context;
        $this->masterClass = CmsRoles::class;
        $this->query = CmsRoles::query();
    }

    protected function buildWhere()
    {
        $this->where['condition'][] = 'system_id=:system_id:';
        $this->where['bind']['system_id'] = SYSTEM_ID;
    }

    protected function setColumns()
    {
        $this->query->columns(array(
            "$this->masterClass.role_id",
            "$this->masterClass.role_name",
            "$this->masterClass.types",
            "$this->masterClass.modify_time",
        ));
    }
}
