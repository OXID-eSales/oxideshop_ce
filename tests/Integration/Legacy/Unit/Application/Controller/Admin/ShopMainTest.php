<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use \oxTestModules;

/**
 * Tests for Shop_Main class
 */
class ShopMainTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Shop_Main::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Shop_Main');

        $this->setRequestParameter("oxid", $this->getConfig()->getBaseShopId());
        $this->assertEquals('shop_main', $oView->render());
    }

    /**
     * Shop_Main::Save() test case
     */
    public function testSaveSuccess()
    {
        // testing..
        oxTestModules::addFunction('oxshop', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Shop_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "error in Shop_Main::save()");

            return;
        }

        $this->fail("error in Shop_Main::save()");
    }
}
