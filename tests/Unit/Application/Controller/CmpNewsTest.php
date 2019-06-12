<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\EshopCommunity\Application\Model\NewsList;

/**
 * oxcmp_news tests
 */
class CmpNewsTest extends \OxidTestCase
{

    /**
     * Testing oxcmp_news::render()
     *
     * @return null
     */
    public function testRenderDisabledNavBars()
    {
        $this->getConfig()->setConfigParam("bl_perfLoadNews", false);

        $oCmp = oxNew('oxcmp_news');
        $this->assertNull($oCmp->render());
    }

    /**
     * Testing oxcmp_news::render()
     *
     * @return null
     */
    public function testRenderPerfLoadNewsOnlyStart()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array("getIsOrderStep", "getClassName"));
        $oView->expects($this->never())->method('getIsOrderStep');
        $oView->expects($this->once())->method('getClassName')->will($this->returnValue("test"));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getConfigParam", "getActiveView"));
        $oConfig->expects($this->at(0))->method('getActiveView')->will($this->returnValue($oView));
        $oConfig->expects($this->at(1))->method('getConfigParam')->with($this->equalTo("bl_perfLoadNews"))->will($this->returnValue(true));
        $oConfig->expects($this->at(2))->method('getConfigParam')->with($this->equalTo("blDisableNavBars"))->will($this->returnValue(false));
        $oConfig->expects($this->at(3))->method('getConfigParam')->with($this->equalTo("bl_perfLoadNewsOnlyStart"))->will($this->returnValue(true));

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\NewsComponent::class, array("getConfig"), array(), '', false);
        $oCmp->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertNull($oCmp->render());
    }

    /**
     * Testing oxcmp_news::render()
     *
     * @return null
     */
    public function testRender()
    {
        $this->getConfig()->setConfigParam("bl_perfLoadNews", true);
        $this->getConfig()->setConfigParam("blDisableNavBars", false);
        $this->getConfig()->setConfigParam("bl_perfLoadNewsOnlyStart", false);

        $oCmp = oxNew('oxcmp_news');
        $this->assertTrue($oCmp->render() instanceof newslist);
    }
}
