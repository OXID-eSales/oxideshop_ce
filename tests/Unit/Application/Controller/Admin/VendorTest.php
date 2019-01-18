<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Vendor class
 */
class VendorTest extends \OxidTestCase
{

    /**
     * Vendor::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Vendor');
        $this->assertEquals('vendor.tpl', $oView->render());
    }
}
