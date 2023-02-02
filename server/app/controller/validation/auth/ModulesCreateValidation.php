<?php

namespace Imee\Controller\Validation\Auth;

use Imee\Libs\Validator;

class ModulesCreateValidation extends Validator
{
    protected function rules()
    {
        return [
            'module_name' => 'required|string',
            'icon' => 'string',
            'parent_module_id' => 'integer',
            'type' => 'required|in:menu,page',
            // 'm_type' => 'required|in:1,2',
            'controller' => 'string|required_if:type,page',
            'action' => 'string|required_if:type,page',
            // 'points' => 'array|required_if:type,page',
            // 'points.*.module_name' => 'string|required_if:type,page',
            // 'points.*.controller' => 'string|required_if:type,page',
            // 'points.*.action' => 'string|required_if:type,page',
        ];
    }

    /**
     * 属性
     */
    protected function attributes()
    {
        return [
            'module_name' => '模块名称',
            'parent_module_id' => '父级模块ID',
            'icon' => '图标',
            // 'points' => '子页面',
            // 'points.*.module_name' => '子页面模块名称',
            // 'points.*.controller' => '子页面controller',
            // 'points.*.action' => '子页面action',

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
