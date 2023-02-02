<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Traits;

trait MenuFormatTrait
{
    private function formatMenuList($allMenus = [], $lang = 'zh_cn')
    {
        $menus = [];
        $pages = [];
        $points = [];


        if (empty($allMenus)) {
            return [];
        }
        
        $mapMenus = array_column($allMenus, null, 'module_id');

        foreach ($allMenus as $row) {
            //兼容前端字段
            $row['id'] = $row['module_id'];
            $row['code'] = $row['module_id'];
            $row['name'] = __T($row['module_name'], [], $lang);
            $row['weight'] = 0;
            if ($row['is_action'] == 1) { //point
                $row['page_id'] = $row['parent_module_id'];
                $row['menu_id'] = isset($mapMenus[$row['page_id']]) ? $mapMenus[$row['page_id']]['parent_module_id'] : 0;
                $points[] = $row;
            } elseif ($row['is_action'] == 0 && $row['m_type'] == 1) { //menu
                $row['type'] = !$row['parent_module_id'] ? 'menu-1' : 'menu-2';
                $row['pid'] = $row['parent_module_id'];
                $menus[] = $row;
            } elseif ($row['is_action'] == 0 && $row['m_type'] == 2) { //page
                $row['parent_id'] = $row['parent_module_id'];
                $row['menu_id'] = $row['parent_module_id'];
                $row['page_id'] = 0;
                $row['path'] = '/'. $row['controller'] . '/' . $row['action'];
                $pages[] = $row;
            }
        }

        return [
            'menus' => $menus,
            'pages' => $pages,
            'points' => $points
        ];
    }

    private function formatPermissionList($permissions = []): array
    {
        if (empty($permissions)) {
            return [];
        }
        $points = [];
        $mapMenus = array_column($permissions, null, 'module_id');

        foreach ($permissions as $row) {
            //兼容前端字段
            $row['id'] = $row['module_id'];
            $row['code'] = $row['module_id'];
            $row['name'] = $row['module_name'];
            $row['weight'] = 0;
            if ($row['is_action'] == 1) { //point
                $row['page_id'] = $row['parent_module_id'];
                $row['menu_id'] = isset($mapMenus[$row['page_id']]) ? $mapMenus[$row['page_id']]['parent_module_id'] : 0;
                $points[] = $row;
            }
        }

        return $points;
    }
}
