<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Order;

class CheckoutTestOrder extends Order
{
    public StubPaymentGateway $gateway;

    public int $confirmations = 0;

    public function __construct()
    {
        parent::__construct();

        $this->gateway = new StubPaymentGateway();
    }

    public function validateDelivery($oBasket)
    {
        return null;
    }

    public function validatePayment($oBasket, $oUser = null)
    {
        return null;
    }

    protected function sendOrderByEmail($oUser = null, $oBasket = null, $oPayment = null)
    {
        $this->_oUser = $oUser;
        $this->_oBasket = $oBasket;
        $this->_oPayment = $oPayment;
        $this->confirmations++;

        return self::ORDER_STATE_OK;
    }

    protected function getGateway()
    {
        return $this->gateway;
    }
}
