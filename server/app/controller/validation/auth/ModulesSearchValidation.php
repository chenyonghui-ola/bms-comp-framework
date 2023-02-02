<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;

class ModulesSearchValidation extends Validator
{
    protected function rules()
    {
        return [
            'page' => 'required|string',
        ];
    }

    /**
     * 属性
     */
    protected function attributes()
    {
        return [
            'page' => '顶层模块名称'
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
                    [
                      'module_id' => 1,
                      'module_name' => 'abc',
                      'parent_module_id' => 0,
                      'is_action' => 1,
                      'controller' => '',
                    ]
                ],
            ],
        ];
    }
}
