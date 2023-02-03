<?php

namespace Imee\Service\Lesscode;

use Imee\Schema\AdapterSchema;

class EnumService
{
    /**
     * 低代码-驱动枚举
     * @param  null    $value
     * @param  string  $format
     * @return string|string[]
     */
    public static function getDriveMap($value = null, $format = '')
    {
        $map = [];
        $arr = (new AdapterSchema())->driveArr;

        foreach ($arr as $val)
        {
            $map[$val] = $val;
        }

        if (!is_null($value) && is_numeric($value)) {
            return isset($map[$value]) ? $map[$value] : '';
        }

        if (!empty($format)) {
            $map = self::formatMap($map, $format);
        }

        return $map;
    }

    /**
     * 低代码-操作类型枚举
     * @param  null    $value
     * @param  string  $format
     * @return string|string[]
     */
    public static function getPointTypeMap($value = null, $format = '')
    {
        $map = (new AdapterSchema())->pointTypeMap;

        if (!is_null($value) && !empty($value)) {
            return isset($map[$value]) ? $map[$value] : '';
        }

        if (!empty($format)) {
            $map = self::formatMap($map, $format);
        }

        return $map;
    }

    public static function formatMap($map, $format)
    {
        if (empty($map)) {
            return $map;
        }

        $format = explode(',', $format);
        $label  = $format[0] ?? 'label';
        $value  = $format[1] ?? 'value';

        $formatRes = [];

        foreach ($map as $k => $v)
        {
            $formatRes[] = [
                $label => $v,
                $value => $k,
            ];
        }

        return $formatRes;
    }
}