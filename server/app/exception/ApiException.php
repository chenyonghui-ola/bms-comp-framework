<?php

namespace Imee\Exception;

class ApiException extends \Exception
{
    //在此扩充错误码
    const SUCCESS = 0;

    const VALIDATION_ERROR = 101;
    const PARAMS_ERROR = 102;

    const NSQ_SEND_ERROR = 201;

    const TOKEN_INVALID_ERROR = 401;
    const NO_LOGIN_ERROR = 402;
    const NO_PERMISS_ERROR = 403;
    const NO_FOUND_ERROR = 404;

    protected $codeMsgList = [
        self::NO_FOUND_ERROR    => 'NO FOUND',
        self::NO_LOGIN_ERROR    => '未登录',
        self::NO_PERMISS_ERROR    => '你没有权限进行此项操作:(%s)',
        self::TOKEN_INVALID_ERROR => 'token过期',
        self::VALIDATION_ERROR    => '验证错误',
        self::PARAMS_ERROR        => '参数错误:(%s)',
        self::NSQ_SEND_ERROR      => 'NSQ发送失败',
        self::SUCCESS             => 'ok',
    ];

    private $data = [];
    private $params = [];

    public function __construct($code = self::SUCCESS, $params = [], $data = [])
    {
        $this->data = $data;
        $this->params = $params;
        $this->code = $code;

        parent::__construct('Api Exception', $code);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getMsg(): string
    {
        return sprintf($this->codeMsgList[$this->code], ...$this->params);
    }
}
