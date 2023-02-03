<?php

namespace Imee\Service\Lesscode\Validations;

use Imee\Libs\Validator;

class GuidValidation extends Validator
{
    protected function rules()
    {
        return [
            'guid' => [
                'required',
                'alpha',
//                'regex:/^([x81-xfe][x40-xfe])+$/',
                // todo lesscode 看是否需要更严格的验证 毕竟会生成很多数据
            ]
        ];
    }

    /**
     * 属性
     */
    protected function attributes()
    {
        return [
            'guid' => '标识',
        ];
    }

    /**
     * 提示信息
     */
    protected function messages()
    {
        return [
            'required' => ':attribute 不能为空',
            'regex'    => ':attribute 不可以是中文',
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
                'code'    => 0,
                'msg'     => '',
                'data'    => null,
            ],
        ];
    }
}
