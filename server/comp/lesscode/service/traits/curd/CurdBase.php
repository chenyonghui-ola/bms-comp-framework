<?php

namespace Imee\Service\Lesscode\Traits\Curd;

trait CurdBase
{
	/**
	 * 列表
	 * @return mixed
	 */
	abstract public function listAction();

	/**
	 * 添加
	 * @return mixed
	 */
	abstract public function createAction();

	/**
	 * 编辑
	 * @return mixed
	 */
	abstract public function modifyAction();

	/**
	 * 删除
	 * @return mixed
	 */
	abstract public function deleteAction();

    /**
     * 导出
     * @return mixed
     */
    abstract public function exportAction();

}