<?php

namespace Imee\Service\Lesscode\Context;

class FormFieldContext extends BaseContext
{
    /**
     * @var string 卡片title 用作模块名称
     */
    protected $cardTitle;

    /**
     * @var string 字段类型
     */
    protected $type;

    /**
     * @var string 字段名称
     */
    protected $title;

    /**
     * @var string FormItem
     */
    protected $xDecorator;

    /**
     * @var string Input/Select ····
     */
    protected $xComponent;

    /**
     * @var array 表单验证
     */
    protected $xValidator;

    /**
     * @var string 组件唯一ID
     */
    protected $xDesignableId;

    /**
     * @var int
     */
    protected $xIndex;

    /**
     * @var int 表单name
     */
    protected $name;

    /**
     * @var int select/radio 选项枚举
     */
    protected $enum;

    /**
     * @var array [maxLength] 最大长度
     */
    protected $xComponentProps;

    /**
     * @var string 默认值
     */
    protected $default;

    /**
     * @var string 是否是筛选项
     */
    protected $isFilterField;

    /**
     * @var string 主键字段
     */
    protected $isPk = false;

    /**
     * @var SysFieldContext sys context
     */
    protected $sysContext;
}