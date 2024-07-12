<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Vendor_List class
 */
class VendorListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Vendor::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Vendor_List');
        $this->assertEquals('vendor_list', $oView->render());
    }
}
