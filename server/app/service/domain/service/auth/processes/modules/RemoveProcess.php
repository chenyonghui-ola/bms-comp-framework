<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Modules;

use Imee\Service\Domain\Context\Auth\Modules\RemoveContext;
use Imee\Models\Cms\CmsModules;
use Phalcon\Di;
use Imee\Service\Helper;
use Imee\Exception\Auth\ModulesException;

/**
 * 模块删除
 */
class RemoveProcess
{
    private $context;

    public function __construct(RemoveContext $context)
    {
        $this->context = $context;
    }

    private function vefiry($model)
    {
        if (empty($model)) {
            list($code, $msg) = ModulesException::MODULE_NOEXIST_ERROR;
            throw new ModulesException($msg, $code);
        }

        $info = CmsModules::findFirst([
            'conditions' => 'parent_module_id = :parent_module_id: and system_id=:system_id:',
            'bind' => array(
                'parent_module_id' => $this->context->moduleId,
                'system_id' => SYSTEM_ID,
            ),
            'order' => 'module_id desc'
        ]);
        if (!empty($info)) {
            list($code, $msg) = ModulesException::MODULE_HAS_CHILDREN_ERROR;
            throw new ModulesException($msg, $code);
        }
    }

    public function handle()
    {
        $model = CmsModules::findFirst([
            'conditions' => 'module_id = :module_id: and system_id=:system_id:',
            'bind' => array(
                'module_id' => $this->context->moduleId,
                'system_id' => SYSTEM_ID,
            ),
            'order' => 'module_id desc'
        ]);

        $this->vefiry($model);


        $model->delete();
    }
}
