<?php


namespace Imee\Service\Lesscode\Context;


class TemplateContext extends BaseContext
{
    /**
     * @var string 名称
     */
    protected $name;

    /**
     * @var string 前缀
     */
    protected $prefix;

    /**
     * @var string 后缀
     */
    protected $suffix;

    /**
     * @var array 抽象数据
     */
    protected $data;

    /**
     * @var string 操作类型
     */
    protected $opType;

    /**
     * @var FileBuildContext 构造文件需要的数据
     */
    protected $fileBuildContext;
}