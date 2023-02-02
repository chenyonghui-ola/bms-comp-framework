<?php

namespace Imee\Schema;

use Imee\Schema\Traits\SchemaTrait;
use Imee\Schema\Traits\DataTrait;
use Imee\Schema\Traits\SetDataTrait;
use Imee\Service\Lesscode\Exception\CurdException;

class BaseSchema
{
	use SchemaTrait, DataTrait, SetDataTrait;

    protected $guid;

    /**
     * @var string 用于判断schema单例是否改变 重新实例化
     */
    protected $randomToken;

    /**
     * @var static
     */
    private static $instance;

    public function __construct($guid = null)
    {
        if (!is_null($guid)) {
            $this->guid = $guid;
            $this->randomToken = $this->setRandomToken();

            $this->validation();
            $this->setData();
        }
    }

    public function __get($name)
	{
		return $this->json2Array($this->$name);
	}

	public function __set($name, $value)
	{
		return $this->{$name} = $value;
	}

	private function validation()
    {
        if (empty($this->guid)) {
            [$code, $msg] = CurdException::SCHEMA_LACK_GUID_ERROR;
            throw new CurdException($msg, $code);
        }
    }

    public static function getInstance($params, $isNew = false)
    {
        if (!self::$instance || true === $isNew) {
            self::$instance = new static($params);
        }

        return self::$instance;
    }

    protected function setRandomToken()
    {
        return md5(uniqid(mt_rand(), true));
    }

    public function getRandomToken(): string
    {
        return $this->randomToken;
    }
}