<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for Tools class
 */
class ToolsTest extends \OxidTestCase
{

    /**
     * Tools::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Tools');
        $this->assertEquals('tools', $oView->render());
    }

    /**
     * Tools::Render() test case
     */
    public function testRenderDemoshop()
    {
        oxTestModules::addFunction('oxUtils', 'showMessageAndExit', '{ return "Access denied !"; }');


        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isDemoShop"]);
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(true));

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ToolsController::class, ["getConfig"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $this->assertEquals("Access denied !", $oView->render());
    }
}
