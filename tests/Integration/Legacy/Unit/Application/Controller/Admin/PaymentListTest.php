<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Payment_List class
 */
class PaymentListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Payment_List::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Payment_List');
        $this->assertEquals('payment_list', $oView->render());
    }
}
