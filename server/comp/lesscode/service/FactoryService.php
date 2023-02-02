<?php

namespace Imee\Service\Lesscode;


use Imee\Helper\Traits\FactoryTrait;

use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;

use Imee\Service\Lesscode\Context\DiffContext;
use Imee\Service\Lesscode\Context\DiffFileContext;
use Imee\Service\Lesscode\Context\FileCreateContext;

use Imee\Service\Lesscode\Logic\Field\InputTextAreaFieldLogic;
use Imee\Service\Lesscode\Logic\Field\UploadFieldLogic;
use Imee\Service\Lesscode\Logic\SchemaConfigLogic;
use Imee\Service\Lesscode\Logic\Curd\ListConfigLogic;
use Imee\Service\Lesscode\Logic\Curd\ListFieldsLogic;
use Imee\Service\Lesscode\Schema\FieldService;
use Imee\Service\Lesscode\Schema\FormCreateService;

use Imee\Service\Lesscode\Logic\FormCreateLogic;
use Imee\Service\Lesscode\Logic\FormUpdateLogic;
use Imee\Service\Lesscode\Logic\FormCheckLogic;
use Imee\Service\Lesscode\Logic\TemplateLogic;

use Imee\Service\Lesscode\Logic\Field\DatePickerFieldLogic;
use Imee\Service\Lesscode\Logic\Field\InputFieldLogic;
use Imee\Service\Lesscode\Logic\Field\RadioGroupFieldLogic;
use Imee\Service\Lesscode\Logic\Field\SelectFieldLogic;
use Imee\Service\Lesscode\Logic\Field\NumberPickerFieldLogic;
use Imee\Service\Lesscode\Logic\Field\CheckboxGroupFieldLogic;


use Imee\Service\Lesscode\Data\SchemaConfigData;
use Imee\Service\Lesscode\Schema\TableService;


class FactoryService
{
    use FactoryTrait;

    public static $classMap = [
        // context
        'fileCreateContext'      => FileCreateContext::class,
        'diffFileContext'        => DiffFileContext::class,

        // service
        'FilterService'          => FilterService::class,
        'FieldService'           => FieldService::class,
        'formCreateService'      => FormCreateService::class,
        'fileService'            => FileService::class,
        'menuService'            => MenuService::class,
        'tableService'           => TableService::class,

        // logic
        'formCreateLogic'        => FormCreateLogic::class,
        'formUpdateLogic'        => FormUpdateLogic::class,
        'formCheckLogic'         => FormCheckLogic::class,
        'templateLogic'          => TemplateLogic::class,
        'schemaConfigLogic'      => SchemaConfigLogic::class,
        'listConfigLogic'        => ListConfigLogic::class,
        'listFieldsLogic'        => ListFieldsLogic::class,

        // logic field
        'InputFieldLogic'         => InputFieldLogic::class,
        'SelectFieldLogic'        => SelectFieldLogic::class,
        'RadioGroupFieldLogic'    => RadioGroupFieldLogic::class,
        'DatePickerFieldLogic'    => DatePickerFieldLogic::class,
        'NumberPickerFieldLogic'  => NumberPickerFieldLogic::class,
        'InputTextAreaFieldLogic' => InputTextAreaFieldLogic::class,
        'UploadFieldLogic'        => UploadFieldLogic::class,
        'CheckboxGroupFieldLogic' => CheckboxGroupFieldLogic::class,

        // data
        'schemaConfigData'       => SchemaConfigData::class,

        // model
        'schemaConfigModel'      => LesscodeSchemaConfig::class,
        'schemaMenuModel'        => LesscodeMenu::class,
    ];
}