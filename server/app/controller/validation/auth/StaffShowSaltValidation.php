<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;

class StaffShowSaltValidation extends Validator
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
        return [];
    }
}
