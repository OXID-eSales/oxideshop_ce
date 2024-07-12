<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Discount class
 */
class DiagnosticsListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Discount::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Diagnostics_List');
        $this->assertSame('diagnostics_list', $oView->render());
    }
}
