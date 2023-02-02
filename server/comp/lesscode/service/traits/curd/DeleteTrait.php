<?php

namespace Imee\Service\Lesscode\Traits\Curd;

trait DeleteTrait
{
    /**
     * 编辑之前操作
     * @param $params
     */
    abstract public function onBeforeDelete(&$params, $model);

    /**
     * 编辑之后操作
     * @param                      $params
     * @param  \Phalcon\Mvc\Model  $model  添加数据的model
     */
    abstract public function onAfterDelete($params, $model);

	public function onSetParams($params): void
	{

	}
}