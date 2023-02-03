<?php


namespace Imee\Service\Lesscode\Logic;

class FormUpdateLogic extends FormCreateLogic
{
    protected $opType = 'update';

    /**
     * 生成必要文件
     */
    public function handle()
    {
        $this->common();

        return [];
    }
}