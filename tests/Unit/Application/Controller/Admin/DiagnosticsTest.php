<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Discount class
 */
class DiagnosticsTest extends \OxidTestCase
{

    /**
     * Discount::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Diagnostics');
        $this->assertEquals('diagnostics.tpl', $oView->render());
    }
}
