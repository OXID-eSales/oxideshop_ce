<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxField;
use \oxDb;
use OxidEsales\EshopCommunity\Application\Model\PriceAlarm;
use \oxTestModules;

/**
 * ext Smarty class for testing
 */
class PriceAlarmMainTest_smarty
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
class PriceAlarmMainTest extends \OxidTestCase
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

        $this->getConfig()->setConfigParam('blEnterNetPrice', false);
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
        oxTestModules::addFunction('oxUtilsView', 'getSmarty', '{ return new \\OxidEsales\\EshopCommunity\\Tests\\Unit\\Application\\Controller\\Admin\\PriceAlarmMainTest_smarty(); }');
        oxTestModules::addFunction('oxarticle', 'load', '{ $this->oxarticles__oxparentid = new oxField( "parentid" ); $this->oxarticles__oxtitle = new oxField(""); return true; }');
        $this->setRequestParameter("oxid", "testId");

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("sendPricealarmToCustomer"));
        $oEmail->expects($this->once())->method('sendPricealarmToCustomer')->will($this->returnValue(true));
        oxTestModules::addModuleObject("oxEmail", $oEmail);

        // testing..
        $oView = oxNew('PriceAlarm_Main');
        $this->assertEquals('pricealarm_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof pricealarm);
    }

    /**
     * Statistic_Main::Render() test case - counting arlarm articles
     *
     * @return null
     */
    public function testRender_countinPriceAlarmArticles()
    {
        $myConfig = $this->getConfig();
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
        $oView = oxNew('PriceAlarm_Main');
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
        oxTestModules::addFunction('oxUtilsView', 'getSmarty', '{ return new \\OxidEsales\\EshopCommunity\\Tests\\Unit\\Application\\Controller\\Admin\\PriceAlarmMainTest_smarty(); }');
        oxTestModules::addFunction('oxarticle', 'load', '{ $this->oxarticles__oxparentid = new oxField( "parentid" ); $this->oxarticles__oxtitle = new oxField(""); return true; }');

        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("editval", array("oxpricealarm__oxlongdesc" => "test Mail Body"));

        // testing..
        $oView = oxNew('PriceAlarm_Main');
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
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('PriceAlarm_Main');
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
        $this->setRequestParameter("oxid", null);

        // testing..
        $oView = oxNew('PriceAlarm_Main');
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
        $this->setRequestParameter("oxid", "testId");
        oxTestModules::addFunction('oxpricealarm', 'load', '{ return true; }');
        oxTestModules::addFunction('oxpricealarm', 'save', '{ return true; }');
        oxTestModules::addFunction('oxemail', 'send', '{ return true; }');

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("sendPricealarmToCustomer"));
        $oEmail->expects($this->once())->method('sendPricealarmToCustomer')->will($this->returnValue(true));

        oxTestModules::addModuleObject("oxEmail", $oEmail);

        $oPriceAlarm = $this->getMock(\OxidEsales\Eshop\Application\Model\PriceAlarm::class, array("load", "save"));
        $oPriceAlarm->expects($this->once())->method('load');
        $oPriceAlarm->expects($this->once())->method('save');
        $oPriceAlarm->oxpricealarm__oxsended = new oxField();

        oxTestModules::addModuleObject("oxpricealarm", $oPriceAlarm);

        // testing..
        $oView = oxNew('PriceAlarm_Main');
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
        $myConfig = $this->getConfig();

        $sInsert = "insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`,`OXSTOCKFLAG`,`OXSTOCK`,`OXPRICE`)
                    values ('_testArticleId1','" . $myConfig->getShopId() . "','testArticleTitle','2','20','11')";

        $oDb->execute($sInsert);

        $sInsert = "insert into oxpricealarm (`OXID`,`OXSHOPID`,`OXARTID`,`OXPRICE`)
                    values ('_testAlarmId1','" . $myConfig->getShopId() . "','_testArticleId1','12')";

        $oDb->execute($sInsert);

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('parseThroughSmarty'));
        $oUtilsView->expects($this->once())->method('parseThroughSmarty')->with($this->equalTo("test Mail Body"), $this->equalTo("_testAlarmId1"));

        oxTestModules::addModuleObject("oxUtilsView", $oUtilsView);

        $this->setRequestParameter("oxid", "_testAlarmId1");
        $this->setRequestParameter("editval", array("oxpricealarm__oxlongdesc" => "test Mail Body"));

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("sendPricealarmToCustomer"));
        $oEmail->expects($this->once())->method('sendPricealarmToCustomer')->will($this->returnValue(true));

        oxTestModules::addModuleObject("oxEmail", $oEmail);

        // testing..
        $oView = oxNew('PriceAlarm_Main');
        $this->assertNull($oView->send());
    }
}
