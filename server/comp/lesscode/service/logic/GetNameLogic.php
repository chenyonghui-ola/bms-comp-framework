<?php


namespace Imee\Service\Lesscode\Logic;


use Imee\Service\Lesscode\Data\SchemaConfigData;


/**
 * @property SchemaConfigData schemaConfigData
 */
class GetNameLogic
{
    const MODEL_PREFIX = '';
    const MODEL_SUFFIX = '';

    const SCHEMA_PREFIX = '';
    const SCHEMA_SUFFIX = 'Schema';

    const MODULE_CONTROLLER_PREFIX = 'auto/';


    public static function getModel($guid, $prefix = null, $suffix = null): string
    {
        return (!is_null($prefix) ? $prefix : self::MODEL_PREFIX)
            . ucfirst(camel_case($guid))
            . (!is_null($suffix) ? $suffix : self::MODEL_SUFFIX);
    }

    public static function getSchema($guid, $prefix = null, $suffix = null): string
    {
        return (!is_null($prefix) ? $prefix : self::SCHEMA_PREFIX)
            . ucfirst(camel_case($guid))
            . (!is_null($suffix) ? $suffix : self::SCHEMA_SUFFIX);
    }

    public static function getAll($guid): array
    {
        $info = SchemaConfigData::findFirstByGuid($guid);

        return $info ? $info->toArray() : [];
    }

    public static function moduleControllerName($controller)
    {
        return self::MODULE_CONTROLLER_PREFIX . lcfirst($controller);
    }

    public static function moduleControllerNameComplete($controller, $prefix)
    {
        return $prefix . '/' . lcfirst($controller);
    }
}