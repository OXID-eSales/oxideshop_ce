<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use modDB;
use OxidEsales\EshopCommunity\Core\Controller\BaseController;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use \oxRegistry;

class WidgetControlTest extends \OxidTestCase
{
    protected function tearDown()
    {
        parent::tearDown();

        modDB::getInstance()->cleanup();
    }

    /**
     * Testing oxShopControl::start()
     *
     * @return null
     */
    public function testStart()
    {
        $oControl = $this->getMock(\OxidEsales\Eshop\Core\WidgetControl::class, array("_runOnce", "_runLast", "_process"), array(), '', false);
        $oControl->expects($this->once())->method('_runOnce');
        $oControl->expects($this->once())->method('_runLast');
        $oControl->expects($this->once())->method('_process')->with($this->equalTo(\OxidEsales\Eshop\Application\Controller\StartController::class), $this->equalTo("testFnc"), $this->equalTo("testParams"), $this->equalTo("testViewsChain"));
        $oControl->start("start", "testFnc", "testParams", "testViewsChain");
    }

    /**
     * Testing oxShopControl::_runLast()
     *
     * @return null
     */
    public function testRunLast()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("hasActiveViewsChain"));
        $oConfig->expects($this->any())->method('hasActiveViewsChain')->will($this->returnValue(true));

        $oConfig->setActiveView("testView1");
        $oConfig->setActiveView("testView2");

        $this->assertEquals(array("testView1", "testView2"), $oConfig->getActiveViewsList());


        $oControl = $this->getMock(\OxidEsales\Eshop\Core\WidgetControl::class, array("getConfig"));
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $oControl->UNITrunLast();

        $template = $this->getContainer()->get(TemplateRendererInterface::class);

        $this->assertEquals(array("testView1"), $oConfig->getActiveViewsList());
        $globals = $template->getTemplateEngine()->getGlobals();
        $this->assertEquals("testView1", $globals["oView"]);}

    /**
     * Testing oxShopControl::_initializeViewObject()
     *
     * @return null
     */
    public function testInitializeViewObject()
    {
        $oControl = oxNew("oxWidgetControl");
        $oView = $oControl->UNITinitializeViewObject("oxwCookieNote", "testFunction", array("testParam" => "testValue"));

        //checking widget object
        $this->assertEquals("oxwCookieNote", $oView->getClassName());
        $this->assertEquals("testFunction", $oView->getFncName());
        $this->assertEquals("testValue", $oView->getViewParameter("testParam"));

        // checking active view object
        $this->assertEquals(1, count($oControl->getConfig()->getActiveViewsList()));
        $this->assertEquals("oxwCookieNote", $oControl->getConfig()->getActiveView()->getClassName());
    }

    /**
     * Testing oxShopControl::_initializeViewObject()
     *
     * @return null
     */
    public function testInitializeViewObject_hasViewChain()
    {
        $oControl = oxNew("oxWidgetControl");
        $oView = $oControl->UNITinitializeViewObject("oxwCookieNote", "testFunction", array("testParam" => "testValue"), array("account", "oxubase"));

        //checking widget object
        $this->assertEquals("oxwCookieNote", $oView->getClassName());
        $this->assertEquals("testFunction", $oView->getFncName());
        $this->assertEquals("testValue", $oView->getViewParameter("testParam"));

        // checking active view objects
        $aActiveViews = $oControl->getConfig()->getActiveViewsList();

        $this->assertEquals(3, count($aActiveViews));
        $this->assertEquals("account", $aActiveViews[0]->getClassName());
        $this->assertInstanceOf(BaseController::class, $aActiveViews[1]);
        $this->assertEquals("oxwCookieNote", $aActiveViews[2]->getClassName());

        $this->assertEquals("oxwCookieNote", $oControl->getConfig()->getActiveView()->getClassName());
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
