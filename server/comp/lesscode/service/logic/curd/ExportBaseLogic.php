<?php


namespace Imee\Service\Lesscode\Logic\Curd;

use Imee\Schema\AdapterSchema;
use Imee\Service\Helper;
use Imee\Service\Lesscode\ExportService;
use Imee\Service\Lesscode\FilterService;
use Imee\Service\Lesscode\HelperService;
use Imee\Service\Lesscode\Schema\FieldService;
use Imee\Service\Lesscode\Traits\Help\ExportCsvTrait;
use Imee\Service\ModelSupportService;

class ExportBaseLogic extends BaseLogic
{
    use ExportCsvTrait;

    protected $pageSize = 2000;

    /**
     * @var string 查询的字段
     */
    protected $fields = '*';

    /**
     * @var string 排序
     */
    protected $order;

    /**
     * @var \Closure 格式化列表数据回调
     */
    protected $formatListClosure = null;

    /**
     * 人效数据导出
     */
    public function handle()
    {
        parent::handle();

        if (true === $this->hookService->onRewriteExport() || $this->isDriveApi()) {
            return $this->rewriteExport();
        }

        $data = $this->params;

        $file        = $this->getRandFile($data['hashkey'], $data['admin_uid']);
        $tmpLockFile = $file . '.lock';

        try {
            $tmp = iconv("UTF-8", "gbk//IGNORE", implode(',', array_values($this->getHeader()))) . "\n";
            file_put_contents($tmpLockFile, $tmp, FILE_APPEND);

            $this->filterService = new FilterService($this->model, $this->schema);
            $this->fieldService  = new FieldService($this->model, $this->schema);

            $this->hookService->onSetParams($this->params);

            // 处理连表等场景排序
            $this->hookService->onOrderBy($order);

            $conditions = $this->getConditions();

            if (!isset($conditions['_model'])) {
                $conditions['_model'] = get_class($this->model);
            }

            if (defined('LESSCODE_VERSION') && version_compare(LESSCODE_VERSION, '1.1', '>=')) {
                $join = $this->hookService->onJoin($conditions);

                // 关联查询
                if (!empty($join)) {
                    $conditions['_join'] = $join;
                }
            }

            $count = ModelSupportService::getCount($conditions);

            if ($count == 0) {
                throw new \Exception('no data');
            }

            $page = ceil($count / $this->pageSize);
            $keys = array_keys($this->getHeader());

            $fields = $this->hookService->onGetColumns();
            $fields = !empty($fields) ? $fields : $this->getFields();

            for ($currentPage = 1; $currentPage <= $page; ++ $currentPage) {
                $list = ModelSupportService::getList($conditions, $fields, $order??$this->getOrderBy(), $currentPage, $this->pageSize)->toArray();
                if (empty($list)) {
                    break;
                }

                // 处理关联数据
                $list = $this->fieldService->setAttach($list);

                // todo lesscode 解决一些关联数据查询
                $list = $this->formatListMore($list, function (&$item)
                {
                    if ($this->formatListClosure instanceof \Closure) {
                        $item = call_user_func($this->formatListClosure, $item);
                    }

                    $this->hookService->onListFormat($item);

                });

                $list = $this->hookService->onAfterList($list);

                foreach ($list as &$lItem)
                {
                    // 枚举等格式化数据放在钩子函数后
                    $lItem = $this->formatItemMore($lItem);

                    $cKeys = $keys;
                    foreach ($cKeys as $key => &$value) {
                        if (isset($lItem[$value])) {
                            // 判断数据是否对象/数组
                            if (is_array($lItem[$value])) {
                                $value = $this->getObjectValue($lItem[$value]);
                            } else {
                                $value = $lItem[$value];
                            }
                        }
                    }

                    $lItem = $cKeys;
                }

                $tmpStr = $this->formatCsvTextBatch($list);
                file_put_contents($tmpLockFile, $tmpStr, FILE_APPEND);
            }

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->console($errorMessage);

            $this->mysqlErrorException($errorMessage);
        }

        rename($tmpLockFile, $file);
    }

    /**
     * 兼容从task那边把代码抽离出来后所用的输出
     * @param $msg
     */
    protected function console($msg)
    {
        Helper::console($msg, true);
    }

    protected function getConditions()
    {
        return ['_model' => $this->model];
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getOrderBy()
    {
        $this->order = $this->schema->getPk() . ' asc';
        return $this->order;
    }

    public function setSchema()
    {
        $this->schema = AdapterSchema::getInstance($this->guid, true);
    }

    /**
     * 获取表头
     */
    protected function getHeader(): array
    {
        return ExportService::getListFields();
    }

    private function getObjectValue($data)
    {
        if (isset($data['value'])) {
            $value = $data['value'] ?? '';
        } else {
            $arr = [];
            foreach ($data as $item)
            {
                $arr = $item['value'] ?? '';
            }

            $value = implode(',', $arr);
        }

        return $value;
    }

}