<?php


namespace Imee\Service\Lesscode\Context;


class FileCreateContext extends BaseContext
{
    /**
     * @var array 文件列表
     */
    protected $files = [];

    /**
     * @var int 0：添加文件 1:修改文件
     */
    protected $mode = 0;

    /**
     * @var string 单个添加文件
     */
    protected $addFilePath;

    /**
     * @var string 单个添加文件
     */
    protected $addFileType;

    /**
     * @var FileSchemaDiffContext todo lesscode schema文件修改对比
     */
    protected $schemaDiffContext;
}