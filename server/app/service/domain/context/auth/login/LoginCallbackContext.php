<?php

namespace Imee\Service\Domain\Context\Auth\Login;

use Imee\Service\Domain\Context\BaseContext;

class LoginCallbackContext extends BaseContext
{
    /**
     * @var string code
     */
    protected $ucToken;
}
