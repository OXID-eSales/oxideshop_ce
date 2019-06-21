<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_Wrapping class
 */
class AdminWrappingTest extends \OxidTestCase
{

    /**
     * Admin_Wrapping::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Admin_Wrapping');
        $this->assertEquals('wrapping.tpl', $oView->render());
    }
}
