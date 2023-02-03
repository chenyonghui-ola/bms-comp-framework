<?php


namespace Imee\Service\Lesscode\Logic\Curd;


use Imee\Helper\Traits\ResponseTrait;
use Imee\Models\Base\BaseModel;
use Imee\Models\Cms\Lesscode\AdapterModel;
use Imee\Schema\AdapterSchema;
use Imee\Service\Domain\Service\Auth\StaffService;
use Imee\Service\Helper;
use Imee\Service\Lesscode\Context\GuidContext;
use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\FilterService;
use Imee\Service\Lesscode\GetNameService;
use Imee\Service\Lesscode\HelperService;
use Imee\Service\Lesscode\HookService;
use Imee\Service\Lesscode\Interfaces\HandleInterface;
use Imee\Service\Lesscode\Schema\FieldService;
use Imee\Service\Lesscode\Schema\SchemaService;
use Phalcon\Di;

class BaseLogic implements HandleInterface
{
    use ResponseTrait;

    protected $params;

    protected $guid;

    /**
     * @var BaseModel
     */
    protected $model;

    /**
     * @var AdapterSchema
     */
    protected $schema;

    protected $logic;

    protected $page = 1;

    protected $limit = 15;

    protected $sort = ''; // 排序字段

    protected $dir = 'asc'; // 降序或升序

    protected $orderBy = 'id desc';

    protected $offset;

    /**
     * @var HookService
     */
    protected $hookService;

    /**
     * @var FieldService
     */
    protected $fieldService;

    /**
     * @var FilterService
     */
    protected $filterService;

    /**
     * @var StaffService
     */
    protected $staffService;

    protected $listConfigConvertFields;

    public function __construct($params)
    {
        $this->params = $params;

        $guid = AdapterSchema::getRequestGuid();

        if (!empty($guid)) {
            $this->guid = $guid;
            unset($this->params['guid']);
        } elseif (isset($this->params['guid']) && !empty($this->params['guid'])) {
            $this->guid = $this->params['guid'];
        }

        $this->setListParams();
    }

    public function handle()
    {
        $this->nameService();
        $this->hookService();
    }

    protected function setListParams()
    {
        $this->page    = $this->params['page'] ?? 1;
        $this->limit   = $this->params['limit'] ?? 15;
        $this->offset  = $this->params['offset'] ?? 0;
        $this->sort    = $this->params['sort'] ?? '';
        $this->dir     = $this->params['dir'] ?? '';
        $this->orderBy = $this->params['orderBy'] ?? $this->sort . ' ' . $this->dir;
    }

    /**
     * todo lesscode 确认参数
     * @return int
     */
    public function getPageNo()
    {
        return (int) $this->page;
    }

    /**
     * todo lesscode 确认参数
     * @return int
     */
    public function getPageSize()
    {
        return (int) $this->limit;
    }

    /**
     * todo lesscode 确认参数
     * @return string
     */
    public function getPageOrder()
    {
        return !empty(trim($this->orderBy)) ? $this->orderBy : $this->schema->getPk() . ' asc';
    }

    protected function formatList($list, $callback = null)
    {
        if (empty($list)) {
            return [];
        }

//        $service = new SchemaService();
//        $context = new GuidContext(['guid' => $this->guid]);
//        $fields  = $service->getFields($context);
//        $fields && $fields = array_column($fields, null, 'name');
        $listConfig = $this->schema->getListConfig();
		$this->schema->setRawList($list);
        $this->staffService = new StaffService();

        foreach ($list as &$item) {
            // 创建时间和更新时间处理
            if (isset($item['dateline']) && is_numeric($item['dateline'])) {
                $item['dateline'] = $item['dateline'] > 0 ? date('Y-m-d H:i:s', $item['dateline']) : '-';
            }

            if (isset($item['create_time']) && is_numeric($item['create_time'])) {
                $item['create_time'] = $item['create_time'] > 0 ? date('Y-m-d H:i:s', $item['create_time']) : '-';
            }

            if (isset($item['update_time']) && is_numeric($item['update_time'])) {
                $item['update_time'] = $item['update_time'] > 0 ? date('Y-m-d H:i:s', $item['update_time']) : '-';
            }

            $item = $this->formatAdminUid($item);

            // 时间字段都需要处理成时间格式
            $item = $this->fieldService->formatFields($listConfig, $item);

            $callback instanceof \Closure && $callback($item);
        }

        return $list;
    }

    /**
     * 功能同上 但是增加枚举值替换 导出使用
     * @param        $list
     * @param  null  $callback
     */
    protected function formatListMore($list, $callback = null)
    {
        if (empty($list)) {
            return [];
        }

        $this->listConfigConvertFields || $this->listConfigConvertFields = $this->getListConfigConvertFields();
        $this->staffService || $this->staffService = new StaffService();
        $this->schema->setRawList($list);

        foreach ($list as &$item) {
            // 创建时间和更新时间处理
            if (isset($item['dateline']) && is_numeric($item['dateline'])) {
                $item['dateline'] = $item['dateline'] > 0 ? date('Y-m-d H:i:s', $item['dateline']) : '-';
            }

            if (isset($item['create_time']) && is_numeric($item['create_time'])) {
                $item['create_time'] = $item['create_time'] > 0 ? date('Y-m-d H:i:s', $item['create_time']) : '-';
            }

            if (isset($item['update_time']) && is_numeric($item['update_time'])) {
                $item['update_time'] = $item['update_time'] > 0 ? date('Y-m-d H:i:s', $item['update_time']) : '-';
            }

            $item = $this->formatAdminUid($item);

            // 时间字段都需要处理成时间格式
            $item = $this->fieldService->formatFieldsMore($this->listConfigConvertFields, $item);

            $callback instanceof \Closure && $callback($item);
        }

        return $list;
    }

    /**
     * 增加枚举值替换 导出使用
     * @param        $list
     * @param  null  $callback
     */
    protected function formatItemMore($item)
    {
        if (empty($item)) {
            return [];
        }

        $this->listConfigConvertFields || $this->listConfigConvertFields = $this->getListConfigConvertFields();
        // 时间字段都需要处理成时间格式
        $item = $this->fieldService->formatFieldsEnum($this->listConfigConvertFields, $item);

        return $item;
    }

    protected function formatAdminUid($item)
    {
        $adminArr = ['admin', 'admin_id', 'op_id', 'op_uid'];

        static $_adminUids = [];
        static $_randomToken = '';

        if (empty($_randomToken)) {
            $_randomToken = AdapterSchema::getInstance([])->getRandomToken();
            $_adminUids = [];
        } else {
            $randomToken = AdapterSchema::getInstance([])->getRandomToken();

            // 证明本次请求已经跟上次请求不一样了
            if ($randomToken !== $_randomToken) {
                $_adminUids = [];
                $_randomToken = $randomToken;
            }
        }

        $listConfig = $this->schema->getListConfig();

        foreach ($adminArr as $adminField)
        {
            if (isset($item[$adminField]) && $item[$adminField] == 0) {
                continue;
            }

            if (isset($listConfig[$adminField]) && isset($listConfig[$adminField]['format']) && $listConfig[$adminField]['format'] == false) {
                continue;
            }

            if (isset($item[$adminField]) && isset($_adminUids[$item[$adminField]])) {
                $item[$adminField] = $_adminUids[$item[$adminField]];
                break;
            }

            if (isset($item[$adminField]) && is_numeric($item[$adminField])) {
                $user = $this->staffService->getInfoByUid($item[$adminField]);
                $item[$adminField] = $user ? $user['user_name'] : ' - ';
                $_adminUids[$item[$adminField]] = $item[$adminField];
            }
        }

        return $item;
    }

    /**
     * 只取出导出需要格式化的字段
     */
    private function getListConfigConvertFields()
    {
        $fields = [];
        $listConfig  = $this->schema->getListConfig();

        if (empty($listConfig)) {
            return $fields;
        }

        foreach ($listConfig as $key => $item)
        {
            if (isset($item['hidden']) && $item['hidden'] == 1) {
                continue;
            }

            if (isset($item['component']) && $this->fieldService->isConvertComponent($item['component'])) {
                $fields[$key] = $item;
            }
        }

        return $fields;
    }


    public function nameService()
    {
        // 获取各种文件名称/命名空间
        $getNameService = new GetNameService();

        $all = $getNameService->getAll($this->guid);

        if (empty($all)) {
            [$code, $msg] = CurdException::ILLEGAL_GUID_ERROR;
            throw new CurdException($msg, $code);
        }

        // api类型不需要model
        // 实例化之前需要做一些准备工作
        method_exists($all['model'], 'handleParams') && $all['model']::handleParams($this->params);

        $this->setSchema();
        $this->setModel($all);
        $this->setLogic();
    }

    public function setModel($all)
    {
        if ($this->isDriveApi()) {
            $this->model = '';
        } else {
            if (ltrim($all['model'], '\\') === AdapterModel::class) {
                $all['model']::setAdapterSchema($this->schema);
            }

            if (false === class_exists($all['model'])) {
                [$code, $msg] = CurdException::SYSTEM_SCHEMA_MODEL_NO_FOUND;
                throw new CurdException(sprintf($msg, $all['model']), $code);
            }

            $this->model = new $all['model'];
        }
    }

    public function setSchema()
    {
        $this->schema = AdapterSchema::getInstance($this->guid);
    }

    public function setLogic()
    {
        $this->logic  = $this->schema->getLogics()[$this->opType] ?? '';
    }

    public function hookService()
    {
        if (ENV === 'dev') {
            if (!empty($this->logic) && !class_exists($this->logic)) {
                [$code, $msg] = CurdException::HOOK_LOGIC_FILE_NOT_EXIST;
                throw new CurdException(sprintf($msg, $this->logic), $code);
            }
        }

        // 实现钩子函数
        $this->hookService = new HookService((!empty($this->logic) && class_exists($this->logic)) ? new $this->logic : '');
    }

    protected function getFieldEnumValue($list)
    {
        $enum = [];

        foreach ($list as $item) {
            [, $enumVal] = array_values($item);
            $enum[] = $enumVal;
        }

        return $enum;
    }

    protected function ckeckFieldModify($field): bool
    {
        $modify = $this->fieldService->getTableModifyFields();

        if (!isset($modify[$field])) {
            return true;
        }

        if (isset($modify[$field]['hidden']) && $modify[$field]['hidden'] == true) {
            return false;
        }

        if (isset($modify[$field]['disabled']) && $modify[$field]['disabled'] == true) {
            return false;
        }

        return true;
    }

    protected function isDriveApi(): bool
    {
        return $this->drive === AdapterSchema::DRIVE_API;
    }

    protected function isDriveMongo(): bool
    {
        return $this->drive === AdapterSchema::DRIVE_MONGO;
    }

    protected function isDriveMysql(): bool
    {
        return $this->drive === AdapterSchema::DRIVE_MYSQL;
    }
}