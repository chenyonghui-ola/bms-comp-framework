<?php
/**
 * 导出跟列表类似
 */
namespace Imee\Service\Lesscode\Traits\Curd;

trait ExportTrait
{
    use ListTrait;

    /**
     * 获取表头
     * @return mixed
     */
    abstract public function onGetHeader(): array;
}