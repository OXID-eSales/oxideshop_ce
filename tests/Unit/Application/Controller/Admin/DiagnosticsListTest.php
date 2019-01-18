<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Discount class
 */
class DiagnosticsListTest extends \OxidTestCase
{

    /**
     * Discount::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Diagnostics_List');
        $this->assertEquals('diagnostics_list.tpl', $oView->render());
    }
}
