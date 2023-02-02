<?php

namespace Imee\Schema;

use Phalcon\Mvc\Model;

class AdapterSchema extends BaseSchema
{
    const DRIVE_DEFAULT = 'mysql';
    const DRIVE_MYSQL   = 'mysql';
    const DRIVE_MONGO   = 'mongo';
    const DRIVE_API     = 'api';

    const POINT_MAIN   = 'main';
    const POINT_LIST   = 'list';
    const POINT_CREATE = 'create';
    const POINT_MODIFY = 'modify';
    const POINT_DELETE = 'delete';
    const POINT_EXPORT = 'export';

    const POINT_ACTION = 'action';  // 创建操作区域
    const POINT_MODAL = 'modal';  // 弹窗
    const POINT_NEED_CONFIRM = 'need_confirm'; // 确认框
    const POINT_GUID = 'guid'; // guid 列表
    const POINT_URL  = 'url'; // 跳转url

    // 系统功能的guid
    const SYSTEM_GUID_LIST = 'guidList';
    const SYSTEM_GUID_POINT_LIST = 'guidPointList';
    const SYSTEM_GUID_POINT_FIELD = 'guidPointFields';
    const SYSTEM_GUID = ['guidList', 'guidPointList', 'guidPointFields', 'guidMenu'];

    protected static $driveFuncDefault = 'mysql';

    protected $driveArr = [self::DRIVE_MYSQL, self::DRIVE_MONGO, self::DRIVE_API];
    protected $pointArr = [self::POINT_MAIN, self::POINT_LIST, self::POINT_CREATE, self::POINT_MODIFY, self::POINT_DELETE, self::POINT_EXPORT];
    protected $pointTypeMap = [
        self::POINT_ACTION       => '创建操作区域',
        self::POINT_LIST         => '列表(基础功能)',
        self::POINT_CREATE       => '创建(基础功能)',
        self::POINT_MODIFY       => '编辑(基础功能)',
        self::POINT_DELETE       => '删除(基础功能)',
        self::POINT_EXPORT       => '导出(基础功能)',
        self::POINT_MODAL        => '弹窗(数据操作区域)',
        self::POINT_NEED_CONFIRM => '确认框(数据操作区域)',
        self::POINT_GUID         => '低代码列表(数据操作区域)',
        self::POINT_URL          => '跳转url(数据操作区域)',
    ];

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array optype => logic
     */
    protected $logics;

    /**
     * @var array 表结构
     */
    protected $table;

    /**
     * @var array 验证
     */
    protected $validation;

    /**
     * @var array 列表配置
     */
    protected $list;

    /**
     * @var array 列表筛选配置
     */
    protected $listFilter;

    /**
     * @var array 列表关联项配置
     */
    protected $listFields;

    /**
     * @var array 额外的配置
     */
    protected $listExtra;

    /**
     * @var bool 列表是否多选
     */
    protected $listMultiple;

    /**
     * @var bool 列表是否支持筛选缓存
     */
    protected $listFilterCache;

    /**
     * @var bool 列表是否支持补丁挂件
     */
    protected $listPatch;

    /**
     * @var array 列表操作栏 跟创建按钮同级
     */
    protected $listAction;

    /**
     * @var array 创建操作配置
     */
    protected $create;

    /**
     * @var array 编辑操作配置
     */
    protected $modify;

    /**
     * @var array 删除操作配置
     */
    protected $delete;

    /**
     * @var array 操作配置
     */
    protected $operate;

	/**
	 * @var array 原始数据
	 */
    protected $rawList;

    public static function initDriveFuncDefault()
    {
        $driveFuncDefault = defined('ADAPTER_SCHEMA_DRIVE_FUNC_DEFAULT');
        self::$driveFuncDefault = !empty($driveFuncDefault) ? ADAPTER_SCHEMA_DRIVE_FUNC_DEFAULT : self::$driveFuncDefault;
    }

    public static function isApiDriveFunc(): bool
    {
        self::initDriveFuncDefault();
        return self::$driveFuncDefault === static::DRIVE_API;
    }

    public static function isMysqlDriveFunc(): bool
    {
        self::initDriveFuncDefault();
        return self::$driveFuncDefault === static::DRIVE_MYSQL;
    }

    public static function isMongoDriveFunc(): bool
    {
        self::initDriveFuncDefault();
        return self::$driveFuncDefault === static::DRIVE_MONGO;
    }

    public static function getDriveFuncDefault(): string
    {
        self::initDriveFuncDefault();
        return self::$driveFuncDefault;
    }

    public static function isSystemGuid($guid): bool
    {
        return in_array($guid, static::SYSTEM_GUID);
    }

    public static function isSystemNoMenuGuid($guid): bool
    {
        return in_array($guid, [static::SYSTEM_GUID_LIST, self::SYSTEM_GUID_POINT_LIST, self::SYSTEM_GUID_POINT_FIELD]);
    }

}