<?php

namespace Imee\Service\Lesscode\Logic\Field;

use Imee\Service\Lesscode\Context\FormFieldContext;

class NumberPickerFieldLogic extends FieldAbstract
{

    public function handle(FormFieldContext $context)
    {
        $this->context = $context;

        // 切勿合并在一起
        $this->sysContext->setParams(['type' => $this->fieldType()]);
        $this->sysContext->setParams(['max_length' => $this->fieldMaxLength()]);

        $this->context->setParams(['sys_context' => $this->sysContext]);

        parent::handleCommon();
    }

    public function validator(FormFieldContext $context)
    {
        // TODO: Implement validator() method.
    }
}