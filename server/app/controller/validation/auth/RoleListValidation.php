<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;

class RoleListValidation extends Validator
{
    protected function rules()
    {
        return [
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
                        'role_id' => 1,
                        'role_name' => 'abc',
                        'types' => '大功能',
                    ]
                ],
            ],
        ];
    }
}
