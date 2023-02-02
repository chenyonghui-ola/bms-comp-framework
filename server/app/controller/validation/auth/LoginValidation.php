<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;

class LoginValidation extends Validator
{
    protected function rules()
    {
        return [
            'username' => 'required',
            'password' => 'required',
            'repassword' => 'required',
        ];
    }

    /**
     * 属性
     */
    protected function attributes()
    {
        return [
            'username' => '用户名',
            'password' => '用户密码',
            'repassword' => '二次验证',
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
