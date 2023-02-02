<?php


namespace Imee\Service\Lesscode\Logic\Field;


use Imee\Service\Lesscode\Constant\FieldTypeConstant;
use Imee\Service\Lesscode\Context\FormFieldContext;
use Imee\Service\Lesscode\Context\SysFieldContext;
use Imee\Service\Lesscode\HelperService;

abstract class FieldAbstract
{
    /**
     * @var FormFieldContext
     */
    protected $context;

    /**
     * @var SysFieldContext
     */
    protected $sysContext;

    public function __construct()
    {
        $this->sysContext = new SysFieldContext([]);
    }

    abstract public function handle(FormFieldContext $context);

    /**
     * 字段验证处理
     * @return mixed
     */
    abstract public function validator(FormFieldContext $context);

    /**
     * 公共处理
     */
    protected function handleCommon()
    {
        $this->sysContext->setParams([
            'name'    => $this->context->name,
            'title'   => $this->context->title,
            'default' => $this->context->default,
        ]);
    }

    /**
     * 转化字段type
     */
    protected function fieldType()
    {
        // 枚举类型 全部使用 varchar
        if (HelperService::isEnum($this->context->xComponent)) {
            return $this->fieldTypeEnum();
        }

        if (HelperService::isInt($this->context->type)) {
            return FieldTypeConstant::TYPE_INT;
        }

        // 时间类型转成int类型
        if (HelperService::isTime($this->context->xComponent)) {
            return FieldTypeConstant::TYPE_INT;
        }

        // 默认使用varchar
        return FieldTypeConstant::TYPE_VARCHAR;
    }

    /**
     * 分析enum字段类型
     * @return string
     */
    protected function fieldTypeEnum()
    {
        if (empty($this->context->enum)) {
            return FieldTypeConstant::DEFAULT_TYPE_VARCHAR;
        }

        $num = [];

        foreach ($this->context->enum as $value) {
            // 只要有一个不是字符串 那么就直接使用varchar
            if (is_string($value['value']) && !is_numeric($value['value'])) {
                return FieldTypeConstant::TYPE_VARCHAR;
            }

            if (is_numeric($value['value'])) {
                $num[] = $value['value'];
            }
        }

        $minNum = min($num);
        $maxNum = max($num);

        if ($minNum >= - 128 && $maxNum <= 127) {
            return FieldTypeConstant::TYPE_TINYINT;
        }

        return FieldTypeConstant::TYPE_INT;
    }

    /**
     * 部分字段是enum类型，格式化数据
     */
    protected function fieldEnum()
    {
        if (empty($this->context->enum)) {
            return [];
        }

        $data = [];

        foreach ($this->context->enum as $item) {
            $data[] = [$item['label'], $item['value']];
            // todo children 比较复杂的部分先不处理
        }

        return $data;
    }

    /**
     * 获取长度
     * @return int
     */
    protected function fieldMaxLength()
    {
        if (isset($this->context->xComponentProps['maxLength']) && $this->context->xComponentProps['maxLength'] > 0) {
            return $this->context->xComponentProps['maxLength'];
        }

        $map = [
            FieldTypeConstant::TYPE_TINYINT => 1,
            FieldTypeConstant::TYPE_INT     => 10,
            FieldTypeConstant::TYPE_VARCHAR => 255,
            FieldTypeConstant::TYPE_BIGINT  => 20,
        ];

        if (isset($map[$this->sysContext->type])) {
            return $map[$this->sysContext->type];
        }

        // 默认给255长度
        return 255;
    }
}