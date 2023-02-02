<?php

namespace Imee\Libs;

use Imee\Exception\ApiException;

abstract class Validator
{
    private static $factory;

    /**
     * 规则
     */
    abstract protected function rules();

    /**
     * 属性
     */
    abstract protected function attributes();

    /**
     * 返回数据结构
     */
    abstract protected function response();

    /**
     * 提示信息
     */
    abstract protected function messages();

    public static function make(): Validator
    {
        if (self::$factory === null) {
            $translationPath = __DIR__.'/lang';
            
            $translationFileLoader = new \Illuminate\Translation\FileLoader(
                new \Illuminate\Filesystem\Filesystem(),
                $translationPath
            );
            $translator = new \Illuminate\Translation\Translator($translationFileLoader, VALIDATION_LANG);
            self::$factory = new \Illuminate\Validation\Factory($translator);
        }
        return new static();
    }

    /**
     * @param array $data 验证数据
     * @return bool
     * @throws ApiException
     */
    public function validators(array $data): bool
    {
        $validator = self::$factory->make(
            $data,
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );

        if ($validator->fails()) {
            throw new ApiException(ApiException::VALIDATION_ERROR);
        }

        return true;
    }
}
