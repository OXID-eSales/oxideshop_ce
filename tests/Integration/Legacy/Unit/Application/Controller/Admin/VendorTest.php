<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Vendor class
 */
class VendorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Vendor::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Vendor');
        $this->assertEquals('vendor', $oView->render());
    }
}
