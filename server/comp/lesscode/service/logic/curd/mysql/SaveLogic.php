<?php


namespace Imee\Service\Lesscode\Logic\Curd\Mysql;

use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Constant\CreateTableConstant;
use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\HelperService;
use Imee\Service\Lesscode\Schema\FieldService;
use Imee\Service\Lesscode\Logic\Curd\BaseLogic;

class SaveLogic extends BaseLogic
{
    protected $opType = AdapterSchema::POINT_MODIFY;
    protected $drive = AdapterSchema::DRIVE_MYSQL;

    private $action = 'modify';

    public function handle()
    {
        parent::handle();

		$this->hookService->onSetParams($this->params);

        if (true === $this->hookService->onRewriteSave()) {
            return $this->rewriteSave();
        }

        $this->fieldService = new FieldService(new $this->model, $this->schema);
        $pk                 = $this->fieldService->getPk();

        if (!isset($this->params[$pk]) || empty($pk)) {
            [$code, $msg] = CurdException::ILLEGAL_ERROR;
            throw new CurdException($msg, $code);
        }

        $pkVal = $this->params[$pk];
        unset($this->params[$pk]);

        // todo lesscode 数据校验 validations

        $model = $this->model::findFirst([
            'conditions' => $pk . ' = :pk:',
            'bind'       => ['pk' => $pkVal]
        ]);

        if (!$model) {
            [$code, $msg] = CurdException::NO_DATA_ERROR;
            throw new CurdException($msg, $code);
        }

        $this->hookService->onBeforeSave($this->params, $model);

        $fieldList = $this->fieldService->getTableFieldsList();
        $fieldAll  = $this->fieldService->getTableFieldsAll();
        $fields    = $this->fieldService->getTableFields();

        foreach ($this->params as $field => $value) {
            // 验证字段是否存在
            if (!in_array($field, $fields)) {
                continue;
            }

            // 如果字段隐藏或者禁用 不需要操作
            if (false === $this->ckeckFieldModify($field)) {
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
                    if (!empty($value) && !in_array($value, (array) $enum)) {
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

        if (in_array(CreateTableConstant::UPDATE_TIME_FIELD, $fields)) {
            $model->update_time = time();
        }

        if (method_exists($model, 'setLogAttr')) {
            $model->setLogAttr([
                'op_uid' => $this->params['admin_uid'] ?? 0,
                'action' => $this->action
            ]);
        }

        $this->hookService->onAttachSave($model);

        $bool = $model->save();

        if (false === $bool) {
            [$code, $msg] = CurdException::SAVE_ERROR;
            throw new CurdException($msg, $code);
        }

        $res = $this->hookService->onAfterSave($this->params, $model);

        return $res;
    }

    public function rewriteSave()
    {
        $this->hookService->onBeforeSave($this->params, []);

        $bool = $this->hookService->onSave($this->params);

        if (false === $bool) {
            [$code, $msg] = CurdException::SAVE_ERROR;
            throw new CurdException($msg, $code);
        }

        $res = $this->hookService->onAfterSave($this->params, []);

        return $res;
    }
}