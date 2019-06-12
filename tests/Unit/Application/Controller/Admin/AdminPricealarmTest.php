<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_Pricealarm class
 */
class AdminPricealarmTest extends \OxidTestCase
{

    /**
     * Admin_Pricealarm::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Admin_Pricealarm');
        $this->assertEquals('admin_pricealarm.tpl', $oView->render());
    }
}
