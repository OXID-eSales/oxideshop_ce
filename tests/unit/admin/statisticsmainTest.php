<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: dynscreenlistTest.php 25334 2010-01-22 07:14:37Z tomas $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for Statistics_Main class
 */
class Unit_Admin_StatisticsMainTest extends OxidTestCase
{
    /**
     * Statistics_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oSubj = $this->getProxyClass("Statistic_Main");
        modConfig::setParameter("oxid", "testId");

        $oStatMock = $this->getMock("oxstatistic", array("load", "getReports"));
        $oStatMock->expects($this->once())->method("load")->with("testId");
        $oStatMock->expects($this->once())->method("getReports")->will($this->returnValue("testRes"));

        $oUtilsObj = $this->getMock('oxUtilsObject', array('oxNew'));
        $oUtilsObj->expects($this->any())->method('oxNew')->with($this->equalTo("oxstatistic"))->will($this->returnValue($oStatMock));
        modInstances::addMod('oxUtilsObject', $oUtilsObj);

        $this->assertEquals( 'statistic_main.tpl', $oSubj->render() );

        $aViewData = $oSubj->getNonPublicVar("_aViewData");
        $this->assertEquals($aViewData["edit"], $oStatMock);
    }

    public function testRenderWithSavedId()
    {
        // testing..
        $oSubj = $this->getProxyClass("Statistic_Main");
        modConfig::setParameter("saved_oxid", "testId");

        $this->assertEquals( 'statistic_main.tpl', $oSubj->render() );
    }

    public function testSave()
    {
        // testing..
        $oSubj = $this->getProxyClass("Statistic_Main");
        modConfig::setParameter("oxid", "testId");

        $aTestParams = array();
        $aTestParams["testParam"] = "testValue";

        modConfig::getInstance()->setParameter("editval", $aTestParams);

        $aTestParams["oxstatistics__oxshopid"] = oxConfig::getInstance()->getBaseShopId();

        $oStatMock = $this->getMock("oxstatistic", array("load", "assign", "save"));
        $oStatMock->expects($this->once())->method("load")->with("testId");
        $oStatMock->expects($this->once())->method("assign")->with($aTestParams);
        $oStatMock->expects($this->once())->method("save");

        $oUtilsObj = $this->getMock('oxUtilsObject', array('oxNew'));
        $oUtilsObj->expects($this->any())->method('oxNew')->with($this->equalTo("oxstatistic"))->will($this->returnValue($oStatMock));
        modInstances::addMod('oxUtilsObject', $oUtilsObj);

        $oSubj->save();

        $aViewData = $oSubj->getNonPublicVar("_aViewData");
        $this->assertEquals($aViewData["updatelist"], 1);
    }

    public function testGenerate()
    {
        /*
        // testing..
        $oSubj = $this->getProxyClass("Statistic_Main");
        modConfig::setParameter("oxid", "testId");

        //oxstatistics mock
        $oStatMock = $this->getMock("oxstatistic", array("load", "getReports"));
        $oStatMock->expects($this->once())->method("load")->with("testId");
        $oStatMock->expects($this->once())->method("getReports")->will($this->returnValue(array()));

        $oUtilsObj = $this->getMock('oxUtilsObject', array('oxNew'));
        $oUtilsObj->expects($this->at(0))->method('oxNew')->with($this->equalTo("oxstatistic"))->will($this->returnValue($oStatMock));
        $oUtilsObj->expects($this->at(1))->method('oxNew')->with($this->equalTo("oxshop"))->will($this->returnValue(new oxShop()));
        modInstances::addMod('oxUtilsObject', $oUtilsObj);

        $oxUtilsUrl = $this->getMock("oxUtilsUrl", array("processUrl"));
        $oxUtilsUrl->expects($this->once("processUrl"))->method("processUrl");
        modInstances::addMod('oxUtilsUrl', $oxUtilsUrl);


        //smarty mock
        $oSmartyMock = $this->getMock("Smarty", array("assign"));
        $oSmartyMock->expects($this->once())->method("assign")->with("time_from", "testTime1 23:59:59");
        $oSmartyMock->expects($this->once())->method("assign")->with("time_to", "testTime2 23:59:59");
        $oSmartyMock->expects($this->once())->method("assign");
        $oSmartyMock->expects($this->once())->method("fetch")->with("report_pagehead.tpl");

        $oUtilsView = $this->getMock('oxUtilsView', array('getSmarty'));
        $oUtilsView->expects($this->any())->method('getSmarty')->will($this->returnValue($oSmartyMock));
        modInstances::addMod('oxUtilsView', $oUtilsView);

        $oSubj->generate();*/
    }
}
