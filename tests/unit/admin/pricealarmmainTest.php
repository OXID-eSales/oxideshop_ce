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
 * ext Smarty class for testing
 */
class Unit_Admin_PriceAlarmMainTest_smarty
{

    /**
     * Logging call data
     *
     * @param string $sName   called method
     * @param array  $aParams parameters
     *
     * @return null
     */
    public function __call($sName, $aParams)
    {
    }
}

/**
 * Tests for PriceAlarm_Main class
 */
class Unit_Admin_PriceAlarmMainTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        $this->tearDown();
        parent::setUp();

        modConfig::getInstance()->setConfigParam('blEnterNetPrice', false);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxpricealarm');
        $this->cleanUpTable('oxarticles');

        parent::tearDown();
    }

    /**
     * PriceAlarm_Main::Render() test case
     *
     * @return null
     */
    public function testRender__()
    {
        oxTestModules::addFunction('oxpricealarm', 'load', '{ $this->oxpricealarm__oxuserid = new oxField( "oxdefaultadmin" ); return true; }');
        oxTestModules::addFunction('oxUtilsView', 'getSmarty', '{ return new Unit_Admin_PriceAlarmMainTest_smarty(); }');
        oxTestModules::addFunction('oxarticle', 'load', '{ $this->oxarticles__oxparentid = new oxField( "parentid" ); $this->oxarticles__oxtitle = new oxField(""); return true; }');
        modConfig::setRequestParameter("oxid", "testId");

        $oEmail = $this->getMock('oxEmail', array("sendPricealarmToCustomer"));
        $oEmail->expects($this->once())->method('sendPricealarmToCustomer')->will($this->returnValue(true));
        oxTestModules::addModuleObject("oxEmail", $oEmail);

        // testing..
        $oView = new PriceAlarm_Main();
        $this->assertEquals('pricealarm_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof oxpricealarm);
    }

    /**
     * Statistic_Main::Render() test case - counting arlarm articles
     *
     * @return null
     */
    public function testRender_countinPriceAlarmArticles()
    {
        $myConfig = oxRegistry::getConfig();
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $sInsert = "insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`,`OXSTOCKFLAG`,`OXSTOCK`,`OXPRICE`)
                    values ('_testArticleId1','" . $myConfig->getShopId() . "','testArticleTitle','2','20','11')";
        $this->addToDatabase($sInsert, 'oxarticles');

        $sInsert = "insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`,`OXSTOCKFLAG`,`OXSTOCK`,`OXPRICE`)
                    values ('_testArticleId2','" . $myConfig->getShopId() . "','testArticleTitle','2','20','150')";

        $this->addToDatabase($sInsert, 'oxarticles');
        $this->addTeardownSql("delete from oxarticles where oxid like '%_testArticle%'");

        $sInsert = "insert into oxpricealarm (`OXID`,`OXSHOPID`,`OXARTID`,`OXPRICE`)
                    values ('_testAlarmId1','" . $myConfig->getShopId() . "','_testArticleId1','12')";

        $this->addToDatabase($sInsert, 'oxpricealarm');

        $sInsert = "insert into oxpricealarm (`OXID`,`OXSHOPID`,`OXARTID`,`OXPRICE`)
                    values ('_testAlarmId2','" . $myConfig->getShopId() . "','_testArticleId2','151')";

        $this->addToDatabase($sInsert, 'oxpricealarm');
        $this->addTeardownSql("delete from oxpricealarm where oxid like '%_testAlarm%'");

        // testing..
        $oView = new PriceAlarm_Main();
        $oView->render();

        $aViewData = $oView->getViewData();
        $this->assertEquals("2", $aViewData['iAllCnt']);
    }

    /**
     * PriceAlarm_Main::Render() test case - checking if editable mail body is
     * taken from post variable
     *
     * @return null
     */
    public function testRender_checkingMailBody()
    {
        oxTestModules::addFunction('oxpricealarm', 'load', '{ $this->oxpricealarm__oxuserid = new oxField( "oxdefaultadmin" ); return true; }');
        oxTestModules::addFunction('oxUtilsView', 'getSmarty', '{ return new Unit_Admin_PriceAlarmMainTest_smarty(); }');
        oxTestModules::addFunction('oxarticle', 'load', '{ $this->oxarticles__oxparentid = new oxField( "parentid" ); $this->oxarticles__oxtitle = new oxField(""); return true; }');

        modConfig::setRequestParameter("oxid", "testId");
        modConfig::setRequestParameter("editval", array("oxpricealarm__oxlongdesc" => "test Mail Body"));

        // testing..
        $oView = new PriceAlarm_Main();
        $oView->render();

        $aViewData = $oView->getViewData();

        $this->assertTrue(strpos($aViewData["editor"], "test Mail Body") > 0);
    }

    /**
     * Statistic_Main::Render() - checking counting articles with prise alarm
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        modConfig::setRequestParameter("oxid", "-1");

        // testing..
        $oView = new PriceAlarm_Main();
        $this->assertEquals('pricealarm_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * PriceAlarm_Main::Send() test case
     *
     * @return null
     */
    public function testSendNoOxidSet()
    {
        modConfig::setRequestParameter("oxid", null);

        // testing..
        $oView = new PriceAlarm_Main();
        $this->assertNull($oView->send());
        $this->assertEquals(1, $oView->getViewDataElement("mail_err"));
        $this->assertNull($oView->getViewDataElement("mail_succ"));
    }

    /**
     * PriceAlarm_Main::Send() test case
     *
     * @return null
     */
    public function testSend()
    {
        modConfig::setRequestParameter("oxid", "testId");
        oxTestModules::addFunction('oxpricealarm', 'load', '{ return true; }');
        oxTestModules::addFunction('oxpricealarm', 'save', '{ return true; }');
        oxTestModules::addFunction('oxemail', 'send', '{ return true; }');

        $oEmail = $this->getMock('oxEmail', array("sendPricealarmToCustomer"));
        $oEmail->expects($this->once())->method('sendPricealarmToCustomer')->will($this->returnValue(true));

        oxTestModules::addModuleObject("oxEmail", $oEmail);

        $oPriceAlarm = $this->getMock('oxpricealarm', array("load", "save"));
        $oPriceAlarm->expects($this->once())->method('load');
        $oPriceAlarm->expects($this->once())->method('save');
        $oPriceAlarm->oxpricealarm__oxsended = new oxField();

        oxTestModules::addModuleObject("oxpricealarm", $oPriceAlarm);

        // testing..
        $oView = new PriceAlarm_Main();
        $this->assertNull($oView->send());
        $this->assertEquals(1, $oView->getViewDataElement("mail_succ"));
        $this->assertNull($oView->getViewDataElement("mail_err"));
    }

    /**
     * PriceAlarm_Main::Send() test case - parse through smarty
     *
     * @return null
     */
    public function testSend_parseThroughSmarty()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $myConfig = oxRegistry::getConfig();

        $sInsert = "insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`,`OXSTOCKFLAG`,`OXSTOCK`,`OXPRICE`)
                    values ('_testArticleId1','" . $myConfig->getShopId() . "','testArticleTitle','2','20','11')";

        $oDb->execute($sInsert);

        $sInsert = "insert into oxpricealarm (`OXID`,`OXSHOPID`,`OXARTID`,`OXPRICE`)
                    values ('_testAlarmId1','" . $myConfig->getShopId() . "','_testArticleId1','12')";

        $oDb->execute($sInsert);

        $oUtilsView = $this->getMock('oxUtilsView', array('parseThroughSmarty'));
        $oUtilsView->expects($this->once())->method('parseThroughSmarty')->with($this->equalTo("test Mail Body"), $this->equalTo("_testAlarmId1"));

        oxTestModules::addModuleObject("oxUtilsView", $oUtilsView);

        modConfig::setRequestParameter("oxid", "_testAlarmId1");
        modConfig::setRequestParameter("editval", array("oxpricealarm__oxlongdesc" => "test Mail Body"));

        $oEmail = $this->getMock('oxEmail', array("sendPricealarmToCustomer"));
        $oEmail->expects($this->once())->method('sendPricealarmToCustomer')->will($this->returnValue(true));

        oxTestModules::addModuleObject("oxEmail", $oEmail);

        // testing..
        $oView = new PriceAlarm_Main();
        $this->assertNull($oView->send());
    }
}
