<?php


namespace Imee\Schema\Traits;


use Phalcon\Di;

trait SchemaTrait
{
    public function getGuid(): string
    {
        return $this->guid;
    }

    public static function getRequestGuid(): string
    {
        $guid = Di::getDefault()->get('request')->getQuery('guid');
        $guid = !empty($guid) ? $guid : Di::getDefault()->get('request')->getPost('guid');
        return (string) $guid;
    }

    /**
     * 获取功能配置 lesscode_schema_config table_config字段
     * @return array
     */
    public function getTable(): array
    {
        return $this->json2Array($this->table);
    }

    /**
     * 获取表备注，也是功能名称
     * @return string
     */
    public function getTableTitle(): string
    {
        return (string) $this->json2Array($this->table)['comment'];
    }

    /**
     * 获取功能字段信息
     * @return array
     */
    public function getTableFields(): array
    {
        return $this->json2Array($this->table)['fields'];
    }

    /**
     * 获取功能字段配置
     * @param $field
     * @return array
     */
    public function getTableField($field): array
    {
        return $this->json2Array($this->table)['fields'][$field] ?? [];
    }

    /**
     * 获取功能字段配置的类型
     * @param $field
     * @return string
     */
    public function getTableFieldType($field): string
    {
        return $this->json2Array($this->table)['fields'][$field]['type'] ?? '';
    }

    /**
     * 获取表名称，默认是空，使用AdapterModel才会使用
     * @return string
     */
    public function getTableConfigName(): string
    {
        $table = $this->json2Array($this->table);
        return (string) isset($table['table_name']) ? $table['table_name'] : '';
    }

    /**
     * 获取表使用的连接
     * @return string
     */
    public function getTableConfigSchema(): string
    {
        $table = $this->json2Array($this->table);
        return (string) isset($table['table_schema']) ? $table['table_schema'] : '';
    }

    /**
     * 获取功能字段配置 lesscode_schema_point_config.config.list
     * @return array
     */
    public function getListConfig(): array
    {
        return $this->json2Array($this->list) ?? [];
    }

    /**
     * 获取功能筛选配置 lesscode_schema_point_config.config.filter
     * @return array
     */
    public function getListFilter(): array
    {
        return $this->json2Array($this->listFilter) ?? [];
    }

    /**
     * 获取功能关联表数据配置 lesscode_schema_point_config.config.fields
     * @return array
     */
    public function getListFields(): array
    {
        return $this->json2Array($this->listFields) ?? [];
    }

    /**
     * 判断列表是否是一个多选类型列表
     * @return bool
     */
    public function getListMultiple(): bool
    {
        return $this->listMultiple ?? false;
    }

    /**
     * 判断列表是否支持缓存筛选
     * @return bool
     */
    public function getListFilterCache(): bool
    {
        return $this->listFilterCache ?? false;
    }

    /**
     * 判断列表是否支持挂件
     * @return array
     */
    public function getListPatch(): array
    {
        return !empty($this->listPatch) && is_array($this->listPatch) ? $this->listPatch : [];
    }

    /**
     * 获取操作栏按钮配置
     * @return array
     */
    public function getListOperate(): array
    {
        return $this->json2Array($this->operate) ?? [];
    }

    /**
     * 获取额外的一些列表配置
     * @return array
     */
    public function getListExtra(): array
    {
        return $this->json2Array($this->listExtra) ?? [];
    }

    /**
     * 获取列表 创建/导出等那横栏操作配置
     * @return array
     */
    public function getListAction(): array
    {
        return $this->json2Array($this->listAction) ?? [];
    }

    /**
     * 获取创建操作配置
     * @return array
     */
    public function getCreate(): array
    {
        return $this->json2Array($this->create) ?? [];
    }

    /**
     * 获取创建操作字段配置
     * @return array
     */
    public function getCreateFields(): array
    {
        return $this->json2Array($this->create)['fields'] ?? [];
    }

    /**
     * 获取编辑操作配置
     * @return array
     */
    public function getModify(): array
    {
        return $this->json2Array($this->modify) ?? [];
    }

    /**
     * 获取编辑操作字段配置
     * @return array
     */
    public function getModifyFields(): array
    {
        return $this->json2Array($this->modify)['fields'] ?? [];
    }

    /**
     * 获取所有钩子文件（只有 列表/创建/编辑/删除/导出 钩子文件才有用）
     * @return array
     */
    public function getLogics(): array
    {
        return $this->json2Array($this->logics) ?? [];
    }

    /**
     * 获取主键字段
     * @return string
     */
    public function getPk(): string
    {
        $pk = $this->json2Array($this->table)['pk'];
        return is_array($pk) ? current($pk) : $pk;
    }

    public function json2Array($data)
    {
        return is_array($data) ? $data : (is_array(@json_decode($data, true)) ? json_decode($data, true) : $data);
    }

	public function setRawList($list)
	{
		if (empty($list)) {
			$this->rawList = [];
		} else {
			$this->rawList = array_column($list, null, $this->getPk());
		}
	}

    public function getRawList()
	{
		return $this->json2Array($this->rawList);
	}
}