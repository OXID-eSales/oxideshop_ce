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
     *
     * @return null
     */
    public function testRender()
    {
        $oVendorList = oxNew('oxwVendorList');
        $this->assertEquals('widget/footer/vendorlist.tpl', $oVendorList->render());
    }

    /**
     * Testing oxwVendorList::getVendorlist()
     *
     * @return null
     */
    public function testGetVendorlistNoVendors()
    {
        $oVendorList = oxNew('oxwVendorList');
        $oList = $oVendorList->getVendorlist();
        $this->assertTrue($oList instanceof VendorList);
        $this->assertEquals(3, $oList->count());
    }
}
