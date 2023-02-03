<?php

namespace Imee\Service\Lesscode\Traits\Curd;

trait CreateTrait
{
    /**
     * 创建之前操作
     * @param $params
     */
    abstract public function onBeforeCreate(&$params);

    /**
     * 创建之后操作
     * @param                      $params
     * @param  \Phalcon\Mvc\Model  $model  添加数据的model
     */
    abstract public function onAfterCreate($params, $model);

	public function onSetParams($params): void
	{

	}

    /**
     * 创建之前操作
     * @param  \Phalcon\Mvc\Model  $model  添加数据的model
     */
    public function onAttachCreate($model): void
    {

    }
}