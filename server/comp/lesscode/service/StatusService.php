<?php

namespace Imee\Service\Lesscode;

use Imee\Service\StatusService as BaseStatusService;

class StatusService extends BaseStatusService
{
    public static function getMainMenuMap($value = null, $format = '')
    {
        $list = (new MenuService())->getMainMenu();
        $map  = array_column($list, null, 'module_id');

        foreach ($map as $id => &$item)
        {
            $path = $item['controller'] . '/' . $item['action'];
            $item = $item['module_name'] . "({$id})({$path})";
        }

        if (!is_null($value) && is_numeric($value)) {
            return isset($map[$value]) ? $map[$value] : '';
        }

        if (!empty($format)) {
            $map = self::formatMap($map, $format);
        }

        return $map;
    }

    public static function getAllMenuMap($value = null, $format = '')
    {
        $list = (new MenuService())->getAllMenu();
        $map  = array_column($list, null, 'module_id');

        foreach ($map as $id => &$item)
        {
            $path = $item['controller'] . '/' . $item['action'];
            $item = $item['module_name'] . "({$id})({$path})";
        }

        if (!is_null($value) && is_numeric($value)) {
            return isset($map[$value]) ? $map[$value] : '';
        }

        if (!empty($format)) {
            $map = self::formatMap($map, $format);
        }

        return $map;
    }
}