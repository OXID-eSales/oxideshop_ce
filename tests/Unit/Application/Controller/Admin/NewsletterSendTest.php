<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxField;
use \oxGroups;
use \oxDb;
use \oxTestModules;

/**
 * Tests for Newsletter_Send class
 */
class NewsletterSendTest extends \OxidTestCase
{

    /**
     * Tear down..
     *
     * @return null
     */
    protected function tearDown()
    {
        // cleanup
        $this->cleanUpTable("oxnewssubscribed");
        $this->cleanUpTable("oxobject2group");

        parent::tearDown();
    }

    /**
     * Newsletter_Send::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxNewsLetter', 'getGroups', '{ return array(); }');
        oxTestModules::addFunction('oxNewsLetter', 'send', '{ return true; }');
        oxTestModules::addFunction('oxNewsLetter', 'prepare', '{ return true; }');

        $this->setRequestParameter("id", "testId");
        $this->getConfig()->setConfigParam('iCntofMails', 3);

        $oNewsSubscribed = oxNew('oxbase');
        $oNewsSubscribed->init("oxnewssubscribed");
        $oNewsSubscribed->setId("_test1");
        $oNewsSubscribed->oxnewssubscribed__oxuserid = new oxField("oxdefaultadmin");
        $oNewsSubscribed->oxnewssubscribed__oxsal = new oxField("MR");
        $oNewsSubscribed->oxnewssubscribed__oxfname = new oxField("John");
        $oNewsSubscribed->oxnewssubscribed__oxlname = new oxField("Doe");
        $oNewsSubscribed->oxnewssubscribed__oxemail = new oxField("admin@myoxideshop.com");
        $oNewsSubscribed->oxnewssubscribed__oxdboptin = new oxField("1");
        $oNewsSubscribed->oxnewssubscribed__oxemailfailed = new oxField(0);
        $oNewsSubscribed->oxnewssubscribed__oxsubscribed = new oxField("2005-07-26 19:16:09");
        $oNewsSubscribed->save();

        $oNewsSubscribed = oxNew('oxbase');
        $oNewsSubscribed->init("oxnewssubscribed");
        $oNewsSubscribed->setId("_test2");
        $oNewsSubscribed->oxnewssubscribed__oxemail = new oxField("testadmin@myoxideshop.com");
        $oNewsSubscribed->oxnewssubscribed__oxuserid = new oxField("oxtestadmin");
        $oNewsSubscribed->oxnewssubscribed__oxsal = new oxField("MR");
        $oNewsSubscribed->oxnewssubscribed__oxfname = new oxField("John");
        $oNewsSubscribed->oxnewssubscribed__oxlname = new oxField("Doe");
        $oNewsSubscribed->oxnewssubscribed__oxdboptin = new oxField("1");
        $oNewsSubscribed->oxnewssubscribed__oxemailfailed = new oxField(0);
        $oNewsSubscribed->oxnewssubscribed__oxsubscribed = new oxField("2005-07-26 19:16:09");
        $oNewsSubscribed->save();

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NewsletterSend::class, array("_setupNavigation"));
        $oView->expects($this->once())->method("_setupNavigation");
        $this->assertEquals('newsletter_send.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['iStart']));
        $this->assertTrue(isset($aViewData['iSend']));
        $this->assertNotNull(oxDb::getDb()->getOne("select oxtext from oxremark where oxparentid='_test1'"));
    }

    /**
     * Newsletter_Send::Render() test case
     *
     * @return null
     */
    public function testRenderAlt()
    {
        oxTestModules::addFunction('oxNewsLetter', 'getGroups', '{ $oGroup1 = new oxGroups();$oGroup1->oxgroups__oxid = new oxField("oxidadmin"); $oGroup2 = new oxGroups();$oGroup2->oxgroups__oxid = new oxField("oxidcustomer"); return array( $oGroup1, $oGroup2 ); }');
        oxTestModules::addFunction('oxNewsLetter', 'send', '{ return false; }');
        oxTestModules::addFunction('oxNewsLetter', 'prepare', '{ return true; }');

        $this->setRequestParameter("id", "testId");
        $this->getConfig()->setConfigParam('iCntofMails', 3);

        // test data
        $oNewsSubscribed = oxNew('oxbase');
        $oNewsSubscribed->init("oxnewssubscribed");
        $oNewsSubscribed->setId("_test1");
        $oNewsSubscribed->oxnewssubscribed__oxuserid = new oxField("oxdefaultadmin");
        $oNewsSubscribed->oxnewssubscribed__oxsal = new oxField("MR");
        $oNewsSubscribed->oxnewssubscribed__oxfname = new oxField("John");
        $oNewsSubscribed->oxnewssubscribed__oxlname = new oxField("Doe");
        $oNewsSubscribed->oxnewssubscribed__oxemail = new oxField("admin@myoxideshop.com");
        $oNewsSubscribed->oxnewssubscribed__oxdboptin = new oxField("1");
        $oNewsSubscribed->oxnewssubscribed__oxemailfailed = new oxField(0);
        $oNewsSubscribed->oxnewssubscribed__oxsubscribed = new oxField("2005-07-26 19:16:09");
        $oNewsSubscribed->save();

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NewsletterSend::class, array("_setupNavigation", "getUserCount"));
        $oView->expects($this->exactly(2))->method("_setupNavigation");
        $oView->expects($this->exactly(2))->method("getUserCount")->will($this->returnValue(2));
        $this->assertEquals('newsletter_send.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['iStart']));
        $this->assertTrue(isset($aViewData['iSend']));
        $this->assertEquals(2, $aViewData['iStart']);
        $this->assertEquals(2, $aViewData['iSend']);

        $this->setRequestParameter("iStart", $aViewData['iStart']);
        $this->setRequestParameter("iSend", $aViewData['iSend']);

        $this->assertEquals('newsletter_done.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['iStart']));
        $this->assertTrue(isset($aViewData['iSend']));
    }

    /**
     * Newsletter_Send::getMailErrors() test case
     *
     * @return null
     */
    public function testGetMailErrors()
    {
        // testing..
        $oView = $this->getProxyClass("Newsletter_Send");
        $oView->setNonPublicVar("_aMailErrors", "testerror");

        $this->assertEquals("testerror", $oView->getMailErrors());
    }

    /**
     * Newsletter_Send::_setupNavigation() test case
     *
     * @return null
     */
    public function testSetupNavigation()
    {
        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, array("getTabs", "getActiveTab"));
        $oNavigation->expects($this->once())->method("getTabs")->will($this->returnValue("getTabs"));
        $oNavigation->expects($this->exactly(2))->method("getActiveTab")->will($this->returnValue("getActiveTab"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NewsletterSend::class, array("getNavigation"));
        $oView->expects($this->once())->method("getNavigation")->will($this->returnValue($oNavigation));
        $oView->UNITsetupNavigation("something");

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['editnavi']));
        $this->assertTrue(isset($aViewData['actlocation']));
        $this->assertTrue(isset($aViewData['default_edit']));
        $this->assertTrue(isset($aViewData['actedit']));
    }
}
