<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;

class StaffListValidation extends Validator
{
    protected function rules()
    {
        return [
            'user_name' => 'string',
            'user_id' => 'integer',
            'user_status' => 'integer|in:0,1',
            'is_salt' => 'integer|in:0,1',
            'page' => 'required|integer',
            'limit' => 'required|integer|between:1,1000',
            'sort' => 'string',
            'dir' => 'string|in:asc,desc',
        ];
    }

    /**
     * 属性
     */
    protected function attributes()
    {
        return [
            'user_name' => '用户名',
            'user_id' => '用户ID',
            'user_status' => '用户有效状态',
            'is_salt' => '是否有二次验证',
            'page' => '页码',
            'limit' => '每页数量',
            'sort' => '排序字段',
            'dir' => '正序/倒序',
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
                'total' => 1,
                'data' => [
                    [
                        'user_id' => 1,
                        'user_email' => 'abc@qq.com',
                        'user_name' => '大帅逼',
                        'user_status' => 1,
                        'last_login_time' => '2021-06-11 17:51:24',
                        'is_salt' => 0,
                        'app' => '1,2',
                        'system_permission' => '1',
                        'display_app' => ['伴伴','皮队友'],
                        'display_system_permission' => '管理后台',
                        'display_user_status' => '有效',
                        'display_is_salt' => '无二次验证',
                        'display_roles' => ['管理员']
                    ]
                ],
            ],
        ];
    }
}
