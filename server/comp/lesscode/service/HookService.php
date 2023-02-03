<?php

namespace Imee\Service\Lesscode;

use Imee\Service\Lesscode\Traits\Curd\{CreateTrait, DeleteTrait, ListTrait, SaveTrait, ExportTrait};

class HookService
{
    use ListTrait, CreateTrait, SaveTrait, DeleteTrait, ExportTrait;

    const NO_FUNC = - 1;

    private $hookLogic;

    public function __construct($hookLogic = null)
    {
        $this->hookLogic = $hookLogic;
    }

    /**
     * todo 接收所有参数
     * @param $filter
     */
    public function onSetParams($params)
    {
        $this->hookLogic && method_exists($this->hookLogic, 'onSetParams') && $this->hookLogic->onSetParams($params);
    }

    /**
     * todo 重写获取筛选条件
     * @param $filter
     */
    public function onGetFilter(&$filter)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onGetFilter') ? $this->hookLogic->onGetFilter($filter) : '';
    }

    /**
     * todo 重写列表排序-用于连表字段排序等场景
     * @param $orderBy
     */
    public function onOrderBy(&$orderBy): void
    {
        $this->hookLogic && method_exists($this->hookLogic, 'onOrderBy') && $this->hookLogic->onOrderBy($orderBy);
    }

    public function onJoin($filter): array
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onJoin') ? $this->hookLogic->onJoin($filter) : [];
    }

    public function onGetColumns(): string
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onGetColumns') ? $this->hookLogic->onGetColumns() : '*';
    }

    /**
     * 重写list
     * @param $filter
     * @param $params
     * @return array
     */
    public function onList($filter, $params)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onList') ? $this->hookLogic->onList($filter, $params) : [];
    }

    /**
     * 是否重写list操作
     * @return bool
     */
    public function onRewriteList(): bool
    {
        $bool = false;

        if ($this->hookLogic) {
            if (method_exists($this->hookLogic, 'onRewriteList')) {
                $bool = $this->hookLogic->onRewriteList();
            }

            // 如果存在 onList 方法，证明就是需要重写的
            if (method_exists($this->hookLogic, 'onList')) {
                $bool = true;
            }
        }

        return $bool;
    }

    /**
     * todo 重写格式化列表数据
     * @param $item
     */
    public function onListFormat(&$item)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onListFormat') ? $this->hookLogic->onListFormat($item) : '';
    }

    /**
     * todo 特殊处理最后输出的列表数据
     * @param $list
     * @return mixed
     */
    public function onAfterList($list)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onAfterList') ? $this->hookLogic->onAfterList($list) : $list;
    }

    /**
     * 创建之前操作
     * @param $params
     */
    public function onBeforeCreate(&$params)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onBeforeCreate') ? $this->hookLogic->onBeforeCreate($params) : [];
    }

    /**
     * 是否重写create操作
     * @return bool
     */
    public function onRewriteCreate(): bool
    {
        $bool = false;

        if ($this->hookLogic) {
            if (method_exists($this->hookLogic, 'onRewriteCreate')) {
                $bool = $this->hookLogic->onRewriteCreate();
            }

            // 如果存在 onCreate 方法，证明就是需要重写的
            if (method_exists($this->hookLogic, 'onCreate')) {
                $bool = true;
            }
        }

        return $bool;
    }

    /**
     * 创建操作重写
     * @param $params
     */
    public function onCreate($params)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onCreate') ? $this->hookLogic->onCreate($params) : [];
    }

	/**
	 * 创建之前调用
	 * @param  \Phalcon\Mvc\Model  $model  添加数据的model
	 */
	public function onAttachCreate($model): void
	{
		$this->hookLogic && method_exists($this->hookLogic, 'onAttachCreate') && $this->hookLogic->onAttachCreate($model);
	}

    /**
     * 创建之后操作
     * @param                      $params
     * @param  \Phalcon\Mvc\Model  $model  添加数据的model
     */
    public function onAfterCreate($params, $model)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onAfterCreate') ? $this->hookLogic->onAfterCreate($params, $model) : [];
    }

    /**
     * 编辑之前操作
     * @param $params
     */
    public function onBeforeSave(&$params, $model)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onBeforeSave') ? $this->hookLogic->onBeforeSave($params, $model) : [];
    }

    /**
     * 是否重写save操作
     * @return bool
     */
    public function onRewriteSave(): bool
    {
        $bool = false;

        if ($this->hookLogic) {
            if (method_exists($this->hookLogic, 'onRewriteSave')) {
                $bool = $this->hookLogic->onRewriteSave();
            }

            // 如果存在 onSave 方法，证明就是需要重写的
            if (method_exists($this->hookLogic, 'onSave')) {
                $bool = true;
            }
        }

        return $bool;
    }

    /**
     * 编辑操作重写
     * @param $params
     */
    public function onSave($params)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onSave') ? $this->hookLogic->onSave($params) : self::NO_FUNC;
    }

    /**
     * 创建之前调用
     * @param  \Phalcon\Mvc\Model  $model  添加数据的model
     */
    public function onAttachSave($model): void
    {
        $this->hookLogic && method_exists($this->hookLogic, 'onAttachSave') && $this->hookLogic->onAttachSave($model);
    }

    /**
     * 编辑之后操作
     * @param                      $params
     * @param  \Phalcon\Mvc\Model  $model  添加数据的model
     */
    public function onAfterSave($params, $model)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onAfterSave') ? $this->hookLogic->onAfterSave($params, $model) : [];
    }

    /**
     * 编辑之前操作
     * @param $params
     */
    public function onBeforeDelete(&$params, $model)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onBeforeDelete') ? $this->hookLogic->onBeforeDelete($params, $model) : [];
    }

    /**
     * 是否重写delete操作
     * @return bool
     */
    public function onRewriteDelete(): bool
    {
        $bool = false;

        if ($this->hookLogic) {
            if (method_exists($this->hookLogic, 'onRewriteDelete')) {
                $bool = $this->hookLogic->onRewriteDelete();
            }

            // 如果存在 onDelete 方法，证明就是需要重写的
            if (method_exists($this->hookLogic, 'onDelete')) {
                $bool = true;
            }
        }

        return $bool;
    }

    /**
     * 删除操作重写
     * @param $params
     */
    public function onDelete($params)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onDelete') ? $this->hookLogic->onDelete($params) : [];
    }

    /**
     * 编辑之后操作
     * @param                      $params
     * @param  \Phalcon\Mvc\Model  $model  添加数据的model
     */
    public function onAfterDelete($params, $model)
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onAfterDelete') ? $this->hookLogic->onAfterDelete($params, $model) : [];
    }

    /**
     * 是否重写export操作
     * @return bool
     */
    public function onRewriteExport(): bool
    {
        $bool = false;

        if ($this->hookLogic) {
            if (method_exists($this->hookLogic, 'onRewriteList')) {
                $bool = $this->hookLogic->onRewriteList();
            }

            // 如果存在 onList 方法，证明就是需要重写的
            if (method_exists($this->hookLogic, 'onList')) {
                $bool = true;
            }
        }

        return $bool;
    }

    /**
     * 导出获取表头
     */
    public function onGetHeader(): array
    {
        return $this->hookLogic && method_exists($this->hookLogic, 'onGetHeader') ? $this->hookLogic->onGetHeader() : [];
    }
}