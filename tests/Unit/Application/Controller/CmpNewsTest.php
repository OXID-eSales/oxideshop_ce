<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller;

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
        $oView = $this->getMock("oxView", array("getIsOrderStep", "getClassName"));
        $oView->expects($this->never())->method('getIsOrderStep');
        $oView->expects($this->once())->method('getClassName')->will($this->returnValue("test"));

        $oConfig = $this->getMock("oxConfig", array("getConfigParam", "getActiveView"));
        $oConfig->expects($this->at(0))->method('getActiveView')->will($this->returnValue($oView));
        $oConfig->expects($this->at(1))->method('getConfigParam')->with($this->equalTo("bl_perfLoadNews"))->will($this->returnValue(true));
        $oConfig->expects($this->at(2))->method('getConfigParam')->with($this->equalTo("blDisableNavBars"))->will($this->returnValue(false));
        $oConfig->expects($this->at(3))->method('getConfigParam')->with($this->equalTo("bl_perfLoadNewsOnlyStart"))->will($this->returnValue(true));

        $oCmp = $this->getMock("oxcmp_news", array("getConfig"), array(), '', false);
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

