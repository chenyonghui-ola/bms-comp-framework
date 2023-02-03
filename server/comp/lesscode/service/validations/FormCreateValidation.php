<?php

namespace Imee\Service\Lesscode\Validations;

use Imee\Libs\Validator;

/**
 * 表单创建
 */
class FormCreateValidation extends Validator
{
    public function rules(): array
    {
        return [
            'formily_schema' => 'required|string',
            'guid'           => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'formily_schema' => 'Formily Schema',
            'guid'           => 'Guid',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute 是必填项',
        ];
    }

    /**
     * 返回数据结构
     */
    protected function response()
    {
        return [
            'result' => [
                'success' => true,
                'code'    => 0,
                'msg'     => '',
                'data'    => null,
            ],
        ];
    }
}
