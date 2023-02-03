<?php

namespace Imee\Service\Lesscode\Logic\Schema;

use Imee\Service\Helper;
use Imee\Service\Lesscode\Context\Schema\ParseContext;

class ParseLogic
{
    /**
     * @var ParseContext
     */
    protected $context;

    private $schema = [];

    private $stringArr = [];

    private $defaultCardTitle = '填写功能名称（拖动组件都要在此卡片内）';

    /**
     * 下拉框组件匹配
     * @var string[]
     */
    private $selectFieldNameArr = [
        // 完整匹配
        'app_id', 'kf_id', 'ka_tag', 'state', 'status', 'sex', 'deleted', 'build_al_status',

        // 匹配前缀
        'is_', 'has_',

        // 匹配后缀
        '_state', '_status', '_map', '_type'
    ];

    /**
     * 时间组件匹配
     * @var string[]
     */
    private $timeFieldNameArr = [
        'create_time', 'update_time', 'create_at', 'update_at', '_time',
    ];

    /**
     * 上传组件匹配后缀
     * @var string[]
     */
    private $uploadFieldNameSuffixArr = [
        '_url'
    ];

    private $stringTypeArr = ['char', 'varchar', 'decimal', 'text', 'tinytext', 'mediumtext', 'longtext'];
    private $textTypeArr   = ['text', 'tinytext', 'mediumtext', 'longtext'];
    private $intTypeArr    = ['int', 'bigint', 'tinyint', 'smallint', 'mediumint', 'float', 'double'];


    public function __construct(ParseContext $context)
    {
        $this->context = $context;
    }

    public function handle(): array
    {
        if (!$this->validation()) {
            return [];
        }

        $this->setForm();
        $this->setSchema();

        return ['schema' => json_encode($this->schema, JSON_UNESCAPED_UNICODE)];
    }

    private function setForm()
    {
        $this->schema['form'] = [
            'labelCol'   => 6,
            'wrapperCol' => 12,
        ];
    }

    private function setSchema()
    {
        $xDesignableId = $this->getRandomString();

        $schema = [
            'type'            => 'object',
            'properties'      => [],
            'x-designable-id' => $xDesignableId
        ];

        $cardXDesignableId = $this->getRandomString();
        $card              = [
            'type'              => 'void',
            'x-component'       => 'Card',
            'x-component-props' => [
                'title' => !empty($this->context->comment) ? $this->context->comment : $this->defaultCardTitle,
            ],
            'x-designable-id'   => $cardXDesignableId,
            'x-index'           => 0,
            'properties'        => [],
        ];

        $properties = [];

        foreach (array_values($this->context->fields) as $k => $field) {

            $fieldName = $field['pk'] == true ? $field['name'] . '|pk' : $field['name'];
            $propertie = [];

            $this->setFieldType($propertie, $field);
            $this->setFieldTitle($propertie, $field);
            $this->setFieldDecorator($propertie, $field);
            $this->setFieldComponent($propertie, $field);
            $this->setFieldValidator($propertie, $field);
            $this->setFieldComponentProps($propertie, $field);
            $this->setFieldDecoratorProps($propertie, $field);
            $this->setFieldName($propertie, $field);
            $this->setFieldEnum($propertie, $field);
            $this->setFieldDesignableId($propertie, $field);

            $propertie['x-index']   = $k;
            $properties[$fieldName] = $propertie;
        }

        $card['properties']   = $properties;
        $schema['properties'] = [$cardXDesignableId => $card];
        $this->schema['schema'] = $schema;
    }

    private function setFieldType(&$propertie, $field): void
    {
        if (in_array($field['data_type'], $this->stringTypeArr)) {
            $propertie['type'] = 'string';
            return;
        }

        if (in_array($field['data_type'], $this->intTypeArr)) {

            // 判断是否时间组件
            foreach ($this->timeFieldNameArr as $suffix)
            {
                if (false !== stripos($field['name'], $suffix)) {
                    $propertie['type'] = 'string';
                    return;
                }
            }

            $propertie['type'] = 'number';
            return;
        }

        foreach ($this->uploadFieldNameSuffixArr as $suffix)
        {
            if (false !== stripos($field['name'], $suffix)) {
                $propertie['type'] = 'Array<object>';
                return;
            }
        }

    }

    private function setFieldTitle(&$propertie, $field): void
    {
        $propertie['title'] = isset($field['comment']) && !empty($field['comment']) ? $field['comment'] : $field['name'];
    }

    private function setFieldDecorator(&$propertie, $field): void
    {
        $propertie['x-decorator'] = 'FormItem';
    }

    private function setFieldComponent(&$propertie, $field): void
    {
        foreach ($this->selectFieldNameArr as $suffix) {
            if (false !== stripos($field['name'], $suffix)) {
                $propertie['x-component'] = 'Select';
                return;
            }
        }

        foreach ($this->timeFieldNameArr as $suffix) {
            if ($propertie['type'] == 'string' && false !== stripos($field['name'], $suffix)) {
                $propertie['x-component'] = 'DatePicker';
                return;
            }
        }

        foreach ($this->uploadFieldNameSuffixArr as $suffix) {
            if (false !== stripos($field['name'], $suffix)) {
                $propertie['x-component'] = 'Upload';
                return;
            }
        }

        if (in_array($field['data_type'], $this->textTypeArr)) {
            $propertie['x-component'] = 'Input.TextArea';
            return;
        }

        $propertie['x-component'] = 'Input';
    }

    private function setFieldValidator(&$propertie, $field): void
    {
        $propertie['x-validator'] = [];
    }

    private function setFieldComponentProps(&$propertie, $field): void
    {
        if (in_array($propertie['x-component'], ['Upload'])) {
            $propertie['x-component-props'] = [
                'textContent' => 'Upload'
            ];
        } else {
            $propertie['x-component-props'] = new \stdClass();
        }
    }

    private function setFieldDecoratorProps(&$propertie, $field): void
    {
        $propertie['x-decorator-props'] = new \stdClass();
    }

    private function setFieldName(&$propertie, $field): void
    {
        $propertie['name'] = $field['name'];
    }

    private function setFieldEnum(&$propertie, $field): void
    {
        if (in_array(strtolower($propertie['x-component']), ['select', 'radio.group', 'checkbox.group'])) {
            $propertie['enum'] = [];
        }
    }

    private function setFieldDesignableId(&$propertie, $field): void
    {
        $propertie['x-designable-id'] = $this->getRandomString();
    }

    private function getRandomString(): string
    {
        $str = '';

        for ($i = 0; $i <= 6; ++ $i)
        {
            $str = Helper::getRandomString(11);

            if (isset($this->stringArr[$str])) {
                continue;
            }

            $this->stringArr[$str] = $str;
            break;
        }

        return !empty($str) ? $str : uniqid();
    }

    private function validation(): bool
    {
        if (empty($this->context->fields)) {
            return false;
        }

        return true;
    }
}