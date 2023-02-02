<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;
use Imee\Service\Helper;

class StaffModifyValidation extends Validator
{
    protected function rules()
    {
        return [
            'user_id' => 'required|integer',
            'user_name' => 'required|string',
            'user_email' => 'required|email',
            'password' => 'regex:/^(?=.*[0-9].*)(?=.*[A-Z].*)(?=.*[a-z].*).{6,}$/',
            'user_status' => 'required|in:1,0',
            'role_ids' => 'required|array',
            'role_ids.*' => 'integer',
            'language' => 'array',
            'bigarea' => 'array',
        ];
    }

    /**
     * 属性
     */
    protected function attributes()
    {
        return [
            'user_id' => '用户ID',
            'user_name' => '用户名称',
            'user_email' => '用户邮箱地址',
            'password' => '用户密码',
            'user_status' => '用户状态',
            'role_ids' => '角色',
            'language' => '语言',
            'bigarea' => '大区',
        ];
    }

    /**
     * 提示信息
     */
    protected function messages()
    {
        return [
            'password.regex' => '密码必须大于6位，且含有大小写字母以及数字',
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
                'code' => 0,
                'msg' => '',
                'data' => null,
            ],
        ];
    }
}
