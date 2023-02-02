<?php

namespace Imee\Service\Lesscode\Traits\Help;

use Imee\Service\Lesscode\Exception\CommonException;
use Imee\Service\Helper;
use Imee\Service\StatusService;

trait ValidationParamsTrait
{
    public function validationFieldRequire($field, $fieldName = null, $type = null)
    {
        empty($fieldName) && $fieldName = $field;
        empty($type) && $type = 'text';

        if (!empty($this->params[$field])) {
            return;
        }

        if ($type === 'select') {
            [$code, $msg] = CommonException::FILTER_NO_SELECT;
        } else {
            [$code, $msg] = CommonException::FILTER_NO_TEXT;
        }

        throw new CommonException(sprintf($msg, $fieldName), $code);
    }

    public function validationFieldLength($field, $fieldName = null, $length = 0)
    {
        if ($length <= 0) {
            return;
        }

        $strlen = Helper::strlen($this->params[$field]);

        if ($strlen <= $length) {
            return;
        }

        [$code, $msg] = CommonException::COMMON_FIELD_LENGTH_MAX;
        throw new CommonException(sprintf($msg, $fieldName, $length), $code);
    }

    public function validationFieldEnum($field, $fieldName = null, $enum = [])
    {
        empty($fieldName) && $fieldName = $field;

        if (isset($this->params[$field]) && ((isset($enum[0]) && in_array($this->params[$field], $enum)) || !isset($enum[0]) && isset($enum[$this->params[$field]]))) {
            return;
        }
        [$code, $msg] = CommonException::COMMON_ENUM_NO_DATA;

        throw new CommonException(sprintf($msg, $fieldName), $code);
    }

    public function validationPkRequire()
    {
        $cpk = isset($this->cpk) && !empty($this->cpk) ? $this->cpk : 'id';

        if (!empty($this->params[$cpk])) {
            return;
        }

        [$code, $msg] = CommonException::COMMON_ILLEGAL;
        throw new CommonException($msg, $code);
    }

    public function validationPk($modelClass)
    {
        $pk  = isset($this->pk) && !empty($this->pk) ? $this->pk : 'id';
        $cpk = isset($this->cpk) && !empty($this->cpk) ? $this->cpk : 'id';

        $info = $modelClass::findFirst([
            'conditions' => $pk . ' = :pk:',
            'bind'       => ['pk' => $this->params[$cpk]]
        ]);

        if (!empty($info)) {
            empty($this->info) && $this->info = $info;
            return;
        }

        [$code, $msg] = CommonException::COMMON_NO_DATA;
        throw new CommonException($msg, $code);
    }
}