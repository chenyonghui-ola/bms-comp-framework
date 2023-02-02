<?php


namespace Imee\Service\Lesscode\Context;


class SysFieldContext extends BaseContext
{
    /**
     * @var string 转化到系统使用的字段需要的类型
     */
    protected $type;

    /**
     * @var string 名称 key
     */
    protected $name;

    /**
     * @var int 长度
     */
    protected $maxLength;

    /**
     * @var array 枚举
     */
    protected $enum;

    /**
     * @var array 字段名称
     */
    protected $title;

    /**
     * @var string 默认值
     */
    protected $default;
}