<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;

class ModulesPointListValidation extends Validator
{
    protected function rules()
    {
        return [
            'parent_module_id' => 'required|integer',
        ];
    }

    /**
     * 属性
     */
    protected function attributes()
    {
        return [
            'parent_module_id' => '父模块ID'
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
                      'action' => '',
                      'leaf' => true,
                      'children' => [
                          [
                            'module_id' => 2,
                            'module_name' => 'a2',
                            'parent_module_id' => 1,
                            'is_action' => 1,
                            'controller' => 'auth/demo',
                            'action' => 'index',
                            'leaf' => '',
                          ],
                      ],
                    ]
                ],
            ],
        ];
    }
}
