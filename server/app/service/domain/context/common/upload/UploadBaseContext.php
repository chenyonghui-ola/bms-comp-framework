<?php

namespace Imee\Service\Domain\Context\Common\Upload;

use Imee\Service\Domain\Context\BaseContext;

/**
 * 上传类基类
 */
class UploadBaseContext extends BaseContext
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var string 映射的标识符
     */
    protected $action;
    /**
     * @var 上传文件
     */
    protected $file;
    /**
     * @var string 上传文件存放路径
     */
    protected $path;
    /**
     * @var string 存储空间
     */
    protected $bucket;
    /**
     * @var int 文件大小
     */
    protected $allowFileSize;
    /**
     * @var string 文件扩展
     */
    protected $allowExt;
    /**
     * @var string 操作类型 （根据此参数动态组装上传路径和控制上传大小）例：commodity:header_union
     */
    protected $type;
}
