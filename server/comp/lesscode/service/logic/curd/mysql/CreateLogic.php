<?php


namespace Imee\Service\Lesscode\Logic\Curd\Mysql;

use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Constant\CreateTableConstant;
use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\HelperService;
use Imee\Service\Lesscode\Schema\FieldService;
use Imee\Service\Lesscode\Logic\Curd\BaseLogic;

class CreateLogic extends BaseLogic
{
    protected $opType = AdapterSchema::POINT_CREATE;
    protected $drive = AdapterSchema::DRIVE_MYSQL;

    private $action = 'create';

    public function handle()
    {
        parent::handle();

        $this->hookService->onSetParams($this->params);

        if (true === $this->hookService->onRewriteCreate()) {
            return $this->rewriteCreate();
        }

        $this->hookService->onBeforeCreate($this->params);

        $this->fieldService = new FieldService(new $this->model, $this->schema);

        $fieldList = $this->fieldService->getTableFieldsList();
        $fieldAll  = $this->fieldService->getTableFieldsAll();
        $fields    = $this->fieldService->getTableFields();

        $model = new $this->model;

        foreach ($this->params as $field => $value) {
            if (!in_array($field, $fields)) {
                continue;
            }

            if (isset(CreateTableConstant::ATTACH_FIELDS[$field])) {
                continue;
            }

            // 验证字段是否是枚举类型 合法性
            if (isset($fieldList[$field]) && isset($fieldList[$field]['enum']) && !empty($fieldList[$field]['enum']) && is_array($fieldList[$field]['enum'])) {
                $enum = $this->getFieldEnumValue($fieldList[$field]['enum']);

                if (is_array($value)) {
                    if (array_intersect($value, (array) $enum) != $value) {
                        [$code, $msg] = CurdException::ENUM_LIST_ERROR;
                        throw new CurdException(sprintf($msg, $fieldAll[$field]['comment']), $code);
                    }
                } else {
                    if (!in_array($value, (array) $enum)) {
                        [$code, $msg] = CurdException::ENUM_LIST_ERROR;
                        throw new CurdException(sprintf($msg, $fieldAll[$field]['comment']), $code);
                    }
                }
            }

            // 如果是时间类型的字段 转化成int类型
            if (isset($fieldList[$field])
                && isset($fieldList[$field]['component'])
                && HelperService::isTime($fieldList[$field]['component'])) {
                $value = strtotime($value);
            }

            // 如果是多选类型 需要转化成字符串
            if (isset($fieldList[$field])
                && isset($fieldList[$field]['component'])
                && HelperService::isMultiple($fieldList[$field]['component'])
                && is_array($value)) {
                $value = implode(',', $value);
            }

            if (!isset($fieldList[$field]['specialchar']) || $fieldList[$field]['specialchar'] != false) {
                $model->{$field} = htmlspecialchars(trim($value));
            } else {
                $model->{$field} = $value;
            }
        }

        if (in_array(CreateTableConstant::CREATE_TIME_FIELD, $fields)) {
            $model->create_time = time();
        }

        if (method_exists($model, 'setLogAttr')) {
            $model->setLogAttr([
                'op_uid' => $this->params['admin_uid'] ?? 0,
                'action' => $this->action
            ]);
        }

        $this->hookService->onAttachCreate($model);

        $bool = $model->save();

        if (false === $bool) {
            [$code, $msg] = CurdException::CREATE_ERROR;
            throw new CurdException($msg, $code);
        }

        $res = $this->hookService->onAfterCreate($this->params, $model);

        return $res;
    }

    public function rewriteCreate()
    {
        $this->hookService->onBeforeCreate($this->params);

        $bool = $this->hookService->onCreate($this->params);

        if (false === $bool) {
            [$code, $msg] = CurdException::SAVE_ERROR;
            throw new CurdException($msg, $code);
        }

        $res = $this->hookService->onAfterCreate($this->params, []);

        return $res;
    }
}