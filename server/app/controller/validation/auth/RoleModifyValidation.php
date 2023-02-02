<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;

class RoleModifyValidation extends Validator
{
    protected function rules()
    {
        return [
            'role_name' => 'required|string',
            'role_id' => 'required|integer',
            'module_ids' => 'required|array',
            'module_ids.*' => 'integer',
            'tree' => 'required|json',
        ];
    }

    /**
     * 属性
     */
    protected function attributes()
    {
        return [
            'role_name' => '角色名称',
            'role_id' => '角色ID',
            'module_ids' => '模块ids',
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
