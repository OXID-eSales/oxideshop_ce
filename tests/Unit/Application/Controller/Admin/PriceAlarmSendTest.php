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
namespace Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for PriceAlarm_Send class
 */
class PriceAlarmSendTest extends \OxidTestCase
{

    /**
     * PriceAlarm_Send::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = $this->getMock("PriceAlarm_Send", array("_setupNavigation"));
        $oView->expects($this->once())->method('_setupNavigation');
        $this->assertEquals('pricealarm_done.tpl', $oView->render());
    }

    /**
     * PriceAlarm_Send::SetupNavigation() test case
     *
     * @return null
     */
    public function testSetupNavigation()
    {
        // testing..
        $sNode = "pricealarm_list";
        $this->setRequestParameter("menu", $sNode);
        $this->setRequestParameter('actedit', 1);

        $oNavigation = $this->getMock("oxnavigationtree", array("getTabs", "getActiveTab"));
        $oNavigation->expects($this->any())->method('getActiveTab')->will($this->returnValue("testEdit"));
        $oNavigation->expects($this->once())->method('getTabs')->with($this->equalTo($sNode), $this->equalTo(1))->will($this->returnValue("editTabs"));

        $oView = $this->getMock("PriceAlarm_Send", array("getNavigation"));
        $oView->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));

        $oView->UNITsetupNavigation($sNode);
        $this->assertEquals("editTabs", $oView->getViewDataElement("editnavi"));
        $this->assertEquals("testEdit", $oView->getViewDataElement("actlocation"));
        $this->assertEquals("testEdit", $oView->getViewDataElement("default_edit"));
        $this->assertEquals(1, $oView->getViewDataElement("actedit"));
    }

    /**
     * PriceAlarm_Send::SendeMail() test case
     *
     * @return null
     */
    public function testSendeMail()
    {
        $oAlarm = $this->getMock('oxpricealarm', array('save', 'load'));
        $oAlarm->expects($this->once())->method('load')
            ->with($this->equalTo("paid"))
            ->will($this->returnValue(true));
        $oAlarm->expects($this->once())->method('save')
            ->will($this->returnValue(true));

        oxTestModules::addModuleObject('oxpricealarm', $oAlarm);

        $oEmail = $this->getMock('oxemail', array('sendPricealarmToCustomer'));
        $oEmail->expects($this->once())->method('sendPricealarmToCustomer')
            ->with($this->equalTo("info@example.com"), $this->isInstanceOf('\OxidEsales\EshopCommunity\Application\Model\PriceAlarm'))
            ->will($this->returnValue(true));

        oxTestModules::addModuleObject('oxemail', $oEmail);

        // testing..
        $oView = oxNew('PriceAlarm_Send');
        $oView->sendeMail("info@example.com", "", "paid", "");
    }
}
