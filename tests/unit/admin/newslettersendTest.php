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
 * Tests for Newsletter_Send class
 */
class Unit_Admin_NewsletterSendTest extends OxidTestCase
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

        modConfig::setRequestParameter("id", "testId");
        modConfig::getInstance()->setConfigParam('iCntofMails', 3);

        $oNewsSubscribed = new oxbase();
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

        $oNewsSubscribed = new oxbase();
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
        $oView = $this->getMock("Newsletter_Send", array("_setupNavigation"));
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

        modConfig::setRequestParameter("id", "testId");
        modConfig::getInstance()->setConfigParam('iCntofMails', 3);

        // test data
        $oNewsSubscribed = new oxbase();
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
        $oView = $this->getMock("Newsletter_Send", array("_setupNavigation", "getUserCount"));
        $oView->expects($this->exactly(2))->method("_setupNavigation");
        $oView->expects($this->exactly(2))->method("getUserCount")->will($this->returnValue(2));
        $this->assertEquals('newsletter_send.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['iStart']));
        $this->assertTrue(isset($aViewData['iSend']));
        $this->assertEquals(2, $aViewData['iStart']);
        $this->assertEquals(2, $aViewData['iSend']);

        modConfig::setRequestParameter("iStart", $aViewData['iStart']);
        modConfig::setRequestParameter("iSend", $aViewData['iSend']);

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
        $oNavigation = $this->getMock("oxnavigationtree", array("getTabs", "getActiveTab"));
        $oNavigation->expects($this->once())->method("getTabs")->will($this->returnValue("getTabs"));
        $oNavigation->expects($this->exactly(2))->method("getActiveTab")->will($this->returnValue("getActiveTab"));

        $oView = $this->getMock("Newsletter_Send", array("getNavigation"));
        $oView->expects($this->once())->method("getNavigation")->will($this->returnValue($oNavigation));
        $oView->UNITsetupNavigation("something");

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['editnavi']));
        $this->assertTrue(isset($aViewData['actlocation']));
        $this->assertTrue(isset($aViewData['default_edit']));
        $this->assertTrue(isset($aViewData['actedit']));
    }
}
