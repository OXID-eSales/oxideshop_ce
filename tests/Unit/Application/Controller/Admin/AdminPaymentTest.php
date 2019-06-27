<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_Payment class
 */
class AdminPaymentTest extends \OxidTestCase
{

    /**
     * Admin_Payment::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $oView = oxNew('Admin_Payment');
        $this->assertEquals('admin_payment.tpl', $oView->render());
    }
}
