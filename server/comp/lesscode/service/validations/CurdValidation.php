<?php

namespace Imee\Service\Lesscode\Validations;

use Imee\Libs\Validator;

class CurdValidation extends Validator
{
    public function rules(): array
    {
        return [
            'guid' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'guid' => 'Guid',
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
