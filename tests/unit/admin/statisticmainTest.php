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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests for Statistic_Main class
 */
class Unit_Admin_StatisticMainTest extends OxidTestCase
{

    /**
     * Unset mocked registry entry.
     *
     * @see OxidTestCase::tearDown()
     */
    function tearDown()
    {
        oxRegistry::set("oxUtilsView", null);
        parent::tearDown();
    }


    protected function _getAllReports()
    {
        $aReportFiles = array(
            array('filename' => 'report_canceled_orders.php', 'name' => 'Bestellabbrueche'),
            array('filename' => 'report_conversion_rate.php', 'name' => 'Conversion Rate'),
            array('filename' => 'report_searchstrings.php', 'name' => 'Suchwörter'),
            array('filename' => 'report_top_clicked_categories.php', 'name' => 'Top geklickte Kategorien'),
            array('filename' => 'report_top_viewed_products.php', 'name' => 'Top angesehene Artikel'),
            array('filename' => 'report_user_per_group.php', 'name' => 'Kunden nach Benutzergruppen'),
            array('filename' => 'report_visitor_absolute.php', 'name' => 'Kunden/Besucher'),
        );

        $aExpAllReports = array();
        foreach ($aReportFiles as $afile) {
            $oStd = new stdClass();
            $oStd->filename = $afile['filename'];
            $oStd->name = $afile['name'];
            $aExpAllReports[] = $oStd;
        }

        return $aExpAllReports;
    }

    /**
     * Statistic_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParam("oxid", "testId");

        // testing..
        $oView = new Statistic_Main();
        $this->assertEquals('statistic_main.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof oxstatistic);

        $sAllReports = $this->getSessionParam("allstat_reports");
        $sReports = $this->getSessionParam("stat_reports_testId");
        $this->assertEquals($this->_getAllReports(), $sAllReports);
        $this->assertFalse($sReports);
        $this->assertNull($aViewData['ireports']);
    }

    /**
     * Statistic_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParam("oxid", "-1");

        // testing..
        $oView = new Statistic_Main();
        $this->assertEquals('statistic_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    public function testRenderWithSomeReports()
    {
        // testing..
        $oView = new Statistic_Main();
        $this->setRequestParam("oxid", "testId");

        $oStatMock = $this->getMock("oxstatistic", array("load", "getReports"));
        $oStatMock->expects($this->once())->method("load")->with("testId");
        $oStatMock->expects($this->once())->method("getReports")->will($this->returnValue(array("testRes")));
        oxTestModules::addModuleObject('oxstatistic', $oStatMock);

        $this->assertEquals('statistic_main.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof oxstatistic);

        $sAllReports = $this->getSessionParam("allstat_reports");
        $sReports = $this->getSessionParam("stat_reports_testId");
        $this->assertEquals($this->_getAllReports(), $sAllReports);
        $this->assertEquals(array("testRes"), $sReports);
        $this->assertEquals(1, $aViewData['ireports']);
    }

    /**
     * Statistic_Main::Render() test case
     *
     * @return null
     */
    public function testRenderPopup()
    {
        $this->setRequestParam("aoc", true);

        $oStatMock = $this->getMock("statistic_main_ajax", array("getColumns"));
        $oStatMock->expects($this->once())->method("getColumns")->will($this->returnValue("testRes"));
        oxTestModules::addModuleObject('statistic_main_ajax', $oStatMock);

        // testing..
        $oView = new Statistic_Main();
        $this->assertEquals('popups/statistic_main.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxajax']));
        $this->assertEquals("testRes", $aViewData['oxajax']);
    }

    /**
     * Statistic_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        // testing..
        $oSubj = $this->getProxyClass("Statistic_Main");
        $this->setRequestParam("oxid", "testId");

        $aTestParams = array();
        $aTestParams["testParam"] = "testValue";

        $this->setRequestParam("editval", $aTestParams);

        $aTestParams["oxstatistics__oxshopid"] = $this->getConfig()->getBaseShopId();

        $oStatMock = $this->getMock("oxstatistic", array("load", "assign", "save"));
        $oStatMock->expects($this->once())->method("load")->with("testId");
        $oStatMock->expects($this->once())->method("assign")->with($aTestParams);
        $oStatMock->expects($this->once())->method("save");
        oxTestModules::addModuleObject('oxstatistic', $oStatMock);

        $oSubj->save();

        $aViewData = $oSubj->getNonPublicVar("_aViewData");
        $this->assertEquals($aViewData["updatelist"], 1);
    }

    /**
     * Statistic_Main::generate() test case
     *
     * @return null
     */
    public function testGenerate()
    {
        // Think if need test those cases:
        // 1 case with getParameter: time_from and time_to
        // 2 case without getParameter: time_from and time_to

        // Mock oxStatistics. oxStatistics method getReports will return array of files to generate report from.
        $sSomeClassName = 'oxSomeClass';
        $aAllreports = array($sSomeClassName . '.php');
        $oStatistic = $this->getMock('oxStatistic', array('getReports', 'load'));
        // Id load with test id getReports() return corect value.
        $oStatistic->expects($this->once())->method('load')->with('_test_id');
        $oStatistic->expects($this->once())->method('getReports')->will($this->returnValue($aAllreports));
        // Mock oxNew to return mocked oxStatistics
        oxTestModules::addModuleObject('oxstatistic', $oStatistic);

        // Mock some object to chek if it is called when returned from oxStatistics method getReports.
        $sTemplateName = 'somefile.tpl';
        $oSomeObject = $this->getMock('oxView', array('setSmarty', 'render'));
        $oSomeObject->expects($this->once())->method('setSmarty')->will($this->returnValue(true));
        $oSomeObject->expects($this->once())->method('render')->will($this->returnValue($sTemplateName));
        // Mock oxNew to return mocked object when creating object from oxStatistics method getReports in method generate.
        oxTestModules::addModuleObject($sSomeClassName, $oSomeObject);

        // Mock Statistic_Main.
        $oStatistic_Main = $this->getMock('Statistic_Main', array('getEditObjectId'));
        // getEditObjectId() return test id for oxStatistics.
        $oStatistic_Main->expects($this->once())->method('getEditObjectId')->will($this->returnValue('_test_id'));

        // Mock Smarty to check if result from oxStatistics getReports() are used.
        // Mock Smarty to check if report_pagehead.tpl and report_bottomitem.tpl are parsed.
        $oSmarty = $this->getMock('Smarty', array('fetch'));
        $oSmarty->expects($this->at(0))->method('fetch')->with('report_pagehead.tpl')->will($this->returnValue(''));
        $oSmarty->expects($this->at(1))->method('fetch')->with($sTemplateName)->will($this->returnValue(''));
        $oSmarty->expects($this->at(2))->method('fetch')->with('report_bottomitem.tpl')->will($this->returnValue(''));

        // Mock oxUtilsView to get mocked Smarty object
        $oUtilsView = $this->getMock('oxUtilsView', array('getSmarty'));
        $oUtilsView->expects($this->once())->method('getSmarty')->will($this->returnValue($oSmarty));
        oxRegistry::set('oxUtilsView', $oUtilsView);

        $oStatistic_Main->generate();
    }

}
