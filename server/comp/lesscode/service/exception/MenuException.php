<?php

namespace Imee\Service\Lesscode\Exception;

class MenuException extends BaseException
{
    protected $serviceCode = '95';

    const MENU_GUID_INVALID  = ['00', '无效的 GUID'];
    const MENU_INVALID  = ['01', '无效的菜单'];
    const MENU_EXISTED  = ['02', '菜单已存在，请删除在添加'];
    const NO_DATA_ERROR = ['03', '数据不存在'];
}
