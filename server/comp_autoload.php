<?php

//基础公用函数，必须
require_once(ROOT . DS . 'comp/common/support/helpers.php');

//翻译
if (is_file(ROOT . DS . 'comp/common/message/helpers.php')) {
    require_once(ROOT . DS . 'comp/common/message/helpers.php');
}