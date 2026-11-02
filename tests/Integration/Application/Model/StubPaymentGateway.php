<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\PaymentGateway;

class StubPaymentGateway extends PaymentGateway
{
    public int $calls = 0;

    public function __construct(
        private readonly bool $paid = true,
        private readonly string $error = '',
        private readonly int $errorNumber = 0
    ) {
        parent::__construct();
    }

    public function executePayment($dAmount, &$oOrder)
    {
        $this->calls++;
        $this->_sLastError = $this->error;
        $this->_iLastErrorNo = $this->errorNumber;

        return $this->paid;
    }
}
