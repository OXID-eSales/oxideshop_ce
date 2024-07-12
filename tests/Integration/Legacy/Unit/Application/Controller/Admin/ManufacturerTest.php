<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Manufacturer class
 */
class ManufacturerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Manufacturer::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Manufacturer');
        $this->assertSame('manufacturer', $oView->render());
    }
}
