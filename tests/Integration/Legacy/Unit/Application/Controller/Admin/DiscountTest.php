<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Discount class
 */
class DiscountTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Discount::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Discount');
        $this->assertSame('discount', $oView->render());
    }
}
