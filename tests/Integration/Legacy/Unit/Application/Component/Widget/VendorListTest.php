<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

use OxidEsales\EshopCommunity\Application\Model\VendorList;

/**
 * Tests for oxwVendorList class
 */
class VendorListTest extends \OxidTestCase
{

    /**
     * Testing oxwVendorList::render()
     */
    public function testRender()
    {
        $oVendorList = oxNew('oxwVendorList');
        $this->assertEquals('widget/footer/vendorlist', $oVendorList->render());
    }

    /**
     * Testing oxwVendorList::getVendorlist()
     */
    public function testGetVendorlistNoVendors()
    {
        $oVendorList = oxNew('oxwVendorList');
        $oList = $oVendorList->getVendorlist();
        $this->assertTrue($oList instanceof VendorList);
        $this->assertEquals(3, $oList->count());
    }
}
