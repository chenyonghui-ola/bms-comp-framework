<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Libs\QRcode;

class ShowSaltProcess
{
    private $userInfo;
    public function __construct($userInfo)
    {
        $this->userInfo = $userInfo;
    }

    public function handle()
    {
        QRcode::png("otpauth://totp/{$this->userInfo['user_email']}?secret={$this->userInfo['salt']}&issuer=" .
            urlencode('lolfi admin'), false, QR_ECLEVEL_L, 10, 1);
        exit();
    }
}
