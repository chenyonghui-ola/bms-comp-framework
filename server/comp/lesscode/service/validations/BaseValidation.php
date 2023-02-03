<?php


namespace Imee\Service\Lesscode\Validations;

use Imee\Helper\Traits\ValidationTrait;


abstract class BaseValidation
{
    use ValidationTrait;

    protected $rule;

    protected $scene;
}