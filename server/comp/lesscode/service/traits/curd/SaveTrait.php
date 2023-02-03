<?php

namespace Imee\Service\Lesscode\Traits\Curd;

trait SaveTrait
{
    /**
     * 编辑之前操作
     * @param $params
     */
    abstract public function onBeforeSave(&$params, $model);

    /**
     * 编辑之后操作
     * @param                      $params
     * @param  \Phalcon\Mvc\Model  $model  添加数据的model
     */
    abstract public function onAfterSave($params, $model);

	public function onSetParams($params): void
	{

	}


    /**
     * 编辑之前操作
     * @param  \Phalcon\Mvc\Model  $model  添加数据的model
     */
    public function onAttachSave($model): void
    {

    }
}