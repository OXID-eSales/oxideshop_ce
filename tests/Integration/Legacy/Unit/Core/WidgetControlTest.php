<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use modDB;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Controller\BaseController;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use \oxRegistry;

class WidgetControlTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        modDB::getInstance()->cleanup();
    }

    /**
     * Testing oxShopControl::start()
     */
    public function testStart()
    {
        $oControl = $this->getMock(\OxidEsales\Eshop\Core\WidgetControl::class, ["runOnce", "runLast", "process"], [], '', false);
        $oControl->expects($this->once())->method('runOnce');
        $oControl->expects($this->once())->method('runLast');
        $oControl->expects($this->once())->method('process')->with($this->equalTo(\OxidEsales\Eshop\Application\Controller\StartController::class), $this->equalTo("testFnc"), $this->equalTo("testParams"), $this->equalTo("testViewsChain"));
        $oControl->start("start", "testFnc", "testParams", "testViewsChain");
    }

    /**
     * Testing oxShopControl::_runLast()
     */
    public function testRunLast()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["hasActiveViewsChain"]);
        $oConfig->expects($this->any())->method('hasActiveViewsChain')->will($this->returnValue(true));

        $oConfig->setActiveView("testView1");
        $oConfig->setActiveView("testView2");

        $this->assertEquals(["testView1", "testView2"], $oConfig->getActiveViewsList());


        $oControl = $this->getMock(\OxidEsales\Eshop\Core\WidgetControl::class, ["getConfig"]);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $oControl->runLast();

        $template = $this->getContainer()->get(TemplateRendererInterface::class);

        $this->assertEquals(["testView1"], $oConfig->getActiveViewsList());
        $globals = $template->getTemplateEngine()->getGlobals();
        $this->assertEquals("testView1", $globals["oView"]);
    }

    /**
     * Testing oxShopControl::_initializeViewObject()
     */
    public function testInitializeViewObject()
    {
        $oControl = oxNew("oxWidgetControl");
        $oView = $oControl->initializeViewObject("oxwCookieNote", "testFunction", ["testParam" => "testValue"]);

        //checking widget object
        $this->assertEquals("oxwCookieNote", $oView->getClassKey());
        $this->assertEquals("testFunction", $oView->getFncName());
        $this->assertEquals("testValue", $oView->getViewParameter("testParam"));

        // checking active view object
        $this->assertEquals(1, count(Registry::getConfig()->getActiveViewsList()));
        $this->assertEquals("oxwCookieNote", Registry::getConfig()->getActiveView()->getClassKey());
    }

    /**
     * Testing oxShopControl::_initializeViewObject()
     */
    public function testInitializeViewObject_hasViewChain()
    {
        $oControl = oxNew("oxWidgetControl");
        $oView = $oControl->initializeViewObject("oxwCookieNote", "testFunction", ["testParam" => "testValue"], ["account", "oxubase"]);

        //checking widget object
        $this->assertEquals("oxwCookieNote", $oView->getClassKey());
        $this->assertEquals("testFunction", $oView->getFncName());
        $this->assertEquals("testValue", $oView->getViewParameter("testParam"));

        // checking active view objects
        $aActiveViews = Registry::getConfig()->getActiveViewsList();

        $this->assertEquals(3, count($aActiveViews));
        $this->assertEquals("account", $aActiveViews[0]->getClassKey());
        $this->assertInstanceOf(BaseController::class, $aActiveViews[1]);
        $this->assertEquals("oxwCookieNote", $aActiveViews[2]->getClassKey());

        $this->assertEquals("oxwCookieNote", Registry::getConfig()->getActiveView()->getClassKey());
    }

    /**
     * @internal
     *
     * @return \Psr\Container\ContainerInterface
     */
    private function getContainer()
    {
        return \OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance()->getContainer();
    }
}
