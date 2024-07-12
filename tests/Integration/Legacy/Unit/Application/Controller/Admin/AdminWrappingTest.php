<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_Wrapping class
 */
class AdminWrappingTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Admin_Wrapping::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Admin_Wrapping');
        $this->assertSame('wrapping', $oView->render());
    }
}
