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
class VendorListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing oxwVendorList::render()
     */
    public function testRender()
    {
        $oVendorList = oxNew('oxwVendorList');
        $this->assertSame('widget/footer/vendorlist', $oVendorList->render());
    }

    /**
     * Testing oxwVendorList::getVendorlist()
     */
    public function testGetVendorlistNoVendors()
    {
        $oVendorList = oxNew('oxwVendorList');
        $oList = $oVendorList->getVendorlist();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\VendorList::class, $oList);
        $this->assertSame(3, $oList->count());
    }
}
