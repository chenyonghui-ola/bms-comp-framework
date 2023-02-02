<?php

namespace Imee\Service;

use Imee\Helper\Traits\SingletonTrait;

class StatusService
{
    use SingletonTrait;

    public static function formatMap($map, $format = 'label,value')
    {
        if (empty($map)) {
            return $map;
        }

        $format = explode(',', $format);
        $label = $format[0] ?? 'label';
        $value = $format[1] ?? 'value';

        $formatRes = [];

        foreach ($map as $k => $v) {
            $formatRes[] = [
                $label => $v,
                $value => is_numeric($k) ? (string)$k : $k,
            ];
        }

        return $formatRes;
    }

    public function getState($value = null, $format = '')
    {
        $map = [0 => '生效', 1 => '下线'];

        if (!empty($value)) {
            return $map[$value] ?? '';
        }

        if (!empty($format)) {
            $map = self::formatMap($map, $format);
        }

        return $map;
    }

    public function getHave($value = null, $format = '')
    {
        $map = [0 => '无', 1 => '有'];

        if (!empty($value)) {
            return $map[$value] ?? '';
        }

        if (!empty($format)) {
            $map = self::formatMap($map, $format);
        }

        return $map;
    }
}