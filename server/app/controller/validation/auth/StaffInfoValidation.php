<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;

class StaffInfoValidation extends Validator
{
    protected function rules()
    {
        return [
            'user_id' => 'required|integer',
        ];
    }

    /**
     * 属性
     */
    protected function attributes()
    {
        return [
            'user_id' => '用户ID',
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
                    'user_id' => 1,
                    'user_email' => 'abc@qq.com',
                    'user_name' => '大帅逼',
                    'user_status' => 1,
                    
                    'app' => '1,2',
                    'role_ids' => [1],
                    'display_roles' => ['管理员']
                
                ],
            ],
        ];
    }
}
