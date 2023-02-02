<?php

namespace Imee\Models\Cms;

class CmsModules extends BaseModel
{
    
    public function initialize()
    {
        parent::initialize();
    }

    const M_TYPE_MENU = 1;
    const M_TYPE_PAGE = 2;

    public static $displayMType = [
        self::M_TYPE_MENU => 'menu',
        self::M_TYPE_PAGE => 'page',
    ];

    const IS_ACTION_YES = 1;
    const IS_ACTION_NO = 0;

    const DELETED_YES = 1;
    const DELETED_NO = 0;

    public static function findModules($module_ids = [], $parent_id = -1, $is_action = -1): array
    {
        $model = self::query()
            ->where('system_id = :system_id:', ['system_id' => SYSTEM_ID]);
        if (!empty($module_ids)) {
            $model->inWhere('module_id', $module_ids);
        }
        if ($parent_id >= 0) {
            $model->andWhere('parent_module_id = :parent_module_id:', ['parent_module_id' => $parent_id]);
        }
        if ($is_action >= 0) {
            $model->andWhere('is_action = :is_action:', ['is_action' => $is_action]);
        }
        return $model->execute()->toArray();
    }

    public static function findModulesByParent(&$modules, $parent_ids = [])
    {
        if (empty($parent_ids)) {
            return $modules;
        }
        $model = self::query()
            ->where('system_id = :system_id:', ['system_id' => SYSTEM_ID])
            ->inWhere('parent_module_id', $parent_ids)
            ->execute()
            ->toArray();
        if (empty($model)) {
            return $modules;
        }
        $modules = array_merge($modules, $model);
        $tem_parent_ids = [];
        foreach ($model as $value) {
            $tem_parent_ids[] = $value['module_id'];
        }
        return self::findModulesByParent($modules, $tem_parent_ids);
    }

    public static function findValidById($moduleId)
    {
        return self::findFirst([
            'conditions' => 'module_id = :module_id: and is_action=:is_action:',
            'bind' => array(
                'module_id' => $moduleId,
                'is_action' => self::IS_ACTION_NO,
            ),
            'order' => 'module_id desc'
        ]);
    }
}
