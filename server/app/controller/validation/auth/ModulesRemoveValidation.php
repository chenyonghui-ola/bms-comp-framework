<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;

class ModulesRemoveValidation extends Validator
{
    protected function rules()
    {
        return [
            'module_id' => 'required|integer',
        ];
    }

    /**
     * 属性
     */
    protected function attributes()
    {
        return [
            'module_id' => '模块ID',
        ];
    }

    /**
     * 提示信息
     */
    protected function messages()
    {
        return [];
    }

    /**
     * 返回数据结构
     */
    protected function response()
    {
        return [
            'result' => [
                'success' => true,
                'code' => 0,
                'msg' => '',
                'data' => null,
            ],
        ];
    }
}
