<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;

class RoleInfoValidation extends Validator
{
    protected function rules()
    {
        return [
            'role_id' => 'required|integer',
        ];
    }

    /**
     * 属性
     */
    protected function attributes()
    {
        return [
            'role_id' => '角色ID',
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
                'data' => [
                    'role_id' => 1,
                    'role_name' => 'abc',
                    'types' => '-1',
                    'module_ids' => [1,2],
                ],
            ],
        ];
    }
}
