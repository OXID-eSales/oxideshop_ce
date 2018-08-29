<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop_System class
 */
class ShopSystemTest extends \OxidTestCase
{

    /**
     * Shop_System::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Shop_System');
        $this->assertEquals('shop_system.tpl', $oView->render());
    }
}
