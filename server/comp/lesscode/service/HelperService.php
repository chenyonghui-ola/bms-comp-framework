<?php

namespace Imee\Service\Lesscode;

use Imee\Schema\AdapterSchema;
use Imee\Service\Helper;
use Imee\Service\Lesscode\Constant\FieldTypeConstant;
use Imee\Service\Lesscode\Exception\ExportException;

class HelperService
{
    // 范围查询组件
    const RANG_INPUT = 'rangeinput';
    // 多选组件
    const SELECT = 'select';
    const SELECT_GROUP = 'select.group';
    const MULTIPLE_SELECT = 'multipleselect';

    // 上传组件
    const UPLOAD = 'upload';
    const UPLOAD_FILE = 'uploadfile';
    const MULTIPLE_UPLOAD = 'multipleupload';

    // 单选
    const RADIO_GROUP = 'radio.group';
    const RADIO_GROUP1 = 'radiogroup';

    // 复选
    const CHECKBOX_GROUP = 'checkbox.group';
    const CHECKBOX_GROUP1 = 'checkboxgroup';

    // 数字
    const INT = 'int';
    const INTEGER = 'integer';
    const NUMBER = 'number';

    // 时间
    const DATE = 'date';
    const DATEPICKER = 'datepicker';

    /**
     * 枚举类型组件
     * @param $component
     * @return bool
     */
    public static function isEnum($component): bool
    {
        return in_array(strtolower($component), [
            self::RADIO_GROUP, self::RADIO_GROUP1,
            self::SELECT_GROUP, self::SELECT, self::MULTIPLE_SELECT,
            self::CHECKBOX_GROUP, self::CHECKBOX_GROUP1
        ]);
    }

    /**
     * int类型组件
     * @param $type
     * @return bool
     */
    public static function isInt($type): bool
    {
        return in_array(strtolower($type), [self::INT, self::INTEGER, self::NUMBER]);
    }

    /**
     * 时间类型组件
     * @param $component
     * @return bool
     */
    public static function isTime($component): bool
    {
        return in_array(strtolower($component), [self::DATEPICKER, self::DATE]);
    }

    /**
     * 多选类型组件
     * @param $component
     * @return bool
     */
    public static function isMultiple($component): bool
    {
        return in_array(strtolower($component), [self::CHECKBOX_GROUP, self::CHECKBOX_GROUP1, self::MULTIPLE_SELECT]);
    }

    /**
     * 范围input框组件
     * @param $component
     * @return bool
     */
    public static function isRangeComponent($component): bool
    {
        return in_array(strtolower($component), [self::RANG_INPUT]);
    }

    /**
     * 上传组件
     * @param $component
     * @return bool
     */
    public static function isUpload($component): bool
    {
        return in_array(strtolower($component), [self::UPLOAD, self::UPLOAD_FILE, self::MULTIPLE_UPLOAD]);
    }

    /**
     * 额外扩展的一些组件
     * @param $component
     * @return bool
     */
    public static function isExpandComponent($component): bool
    {
        return in_array($component, [self::RANG_INPUT, self::MULTIPLE_SELECT, self::UPLOAD_FILE, self::MULTIPLE_UPLOAD]);
    }

    public static function dateConditionFormat($data, $field, \Closure $closure = null, $isAnd = 'and')
    {
        $map = [
            FieldTypeConstant::CONDITION_EGT => '>=',
            FieldTypeConstant::CONDITION_GT  => '>',
            FieldTypeConstant::CONDITION_ELT => '<=',
            FieldTypeConstant::CONDITION_LT  => '<',
        ];

        $conditions = [];

        if (!is_array(current($data))) {
            [$symbol, $value] = $data;
            $value        = $closure instanceof \Closure ? $closure(addslashes($value), $symbol) : addslashes($value);
            $conditions[] = $field . ' ' . $map[strtolower($symbol)] . ' ' . "'{$value}'";
        } else {
            foreach ($data as $item) {
                [$symbol, $value] = $item;
                $value        = $closure instanceof \Closure ? $closure(addslashes($value), $symbol) : addslashes($value);
                $conditions[] = $field . ' ' . $map[strtolower($symbol)] . ' ' . "'{$value}'";
            }
        }

        return implode(' ' . $isAnd . ' ', $conditions);
    }

    public static function getEnumFunc($func, $useParams = true)
    {
        $service = $func['service'] ?? '';
        $method  = $func['method'] ?? '';
        $params  = $useParams === true ? ($func['params'] ?? []) : [];

        if (empty($service) || empty($method)) {
            return [];
        }

        static $_enumArr = [];
        static $_randomToken = '';

        if (empty($_randomToken)) {
            $_randomToken = AdapterSchema::getInstance([])->getRandomToken();
            $_enumArr     = [];
        } else {
            $randomToken = AdapterSchema::getInstance([])->getRandomToken();

            // 证明本次请求已经跟上次请求不一样了
            if ($randomToken !== $_randomToken) {
                $_enumArr     = [];
                $_randomToken = $randomToken;
            }
        }

        $key = md5(json_encode($func) . $useParams);

        if (isset($_enumArr[$key])) {
            return $_enumArr[$key];
        }

        $tmpEnum = [];

        if (method_exists($service, $method)) {
            $refiection = new \ReflectionMethod($service, $method);
            if ($refiection->isStatic()) {
                $tmpEnum = !empty($params) ? call_user_func([$service, $method], ...$params) : call_user_func([$service, $method]);
            } else {
                $tmpEnum = !empty($params) ? call_user_func([new $service, $method], ...$params) : call_user_func([new $service, $method]);
            }
        }

        $_enumArr[$key] = !empty($tmpEnum) ? $tmpEnum : [];

        return $_enumArr[$key];
    }

    public static function getEnumFormat($enum)
    {
        if (empty($enum)) {
            return [];
        }

        $format = [];

        foreach ($enum as $item) {
            if (!isset($item[1])) {
                continue;
            }

            $format[$item[1]] = $item[0];
        }

        return $format;
    }

    public static function getSystemFlag(): string
    {
        if (!defined('LESSCODE_SYSTEM_FLAG')) {
            [$code, $msg] = ExportException::EXPORT_CONSTANT_MUST;
            throw new ExportException(sprintf($msg, 'LESSCODE_SYSTEM_FLAG'), $code);
        }

        return LESSCODE_SYSTEM_FLAG;
    }

    public static function getExportRedisKey($cmdStr): string
    {
        return 'hash.' . self::getSystemFlag() . '.' . $cmdStr;
    }

    public static function getExportQueueName(): string
    {
        if (!defined('LESSCODE_SYSTEM_EXPORT_NAME')) {
            [$code, $msg] = ExportException::EXPORT_CONSTANT_MUST;
            throw new ExportException(sprintf($msg, 'LESSCODE_SYSTEM_EXPORT_NAME'), $code);
        }

        return LESSCODE_SYSTEM_EXPORT_NAME;
    }
}