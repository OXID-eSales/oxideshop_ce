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

use \Exception;
use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use \oxTestModules;

/**
 * Tests for Shop_Seo class
 */
class ShopSeoTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->getOne("delete from oxseo where oxobjectid = 'testObjectId' and oxshopid = '1'");
        parent::tearDown();
    }

    /**
     * Shop_Seo::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $shopId = ShopIdCalculator::BASE_SHOP_ID;
        if ($this->getConfig()->getEdition() === 'EE') {
            $shopId = 1;
        }
        $oView = $this->getProxyClass("Shop_Seo");
        $oView->setNonPublicVar("_sEditObjectId", $shopId);
        $this->assertEquals('shop_seo.tpl', $oView->render());
    }

    /**
     * Shop_Seo::LoadActiveUrl() test case
     *
     * @return null
     */
    public function testLoadActiveUrl()
    {
        $aData = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll("select oxseourl, oxlang, oxobjectid, oxshopid from oxseo limit 1");

        // defining parameters
        $sObjectId = $aData[0]['oxobjectid'];
        $iShopId = $aData[0]['oxshopid'];
        $sSeoUrl = $aData[0]['oxseourl'];
        $iLangId = $aData[0]['oxlang'];

        $this->setRequestParameter('aStaticUrl', array("oxseo__oxobjectid" => $sObjectId));

        // testing..
        $oView = $this->getProxyClass("Shop_Seo");
        $oView->setNonPublicVar("_sActSeoObject", $sObjectId);
        $oView->UNITloadActiveUrl($iShopId);
        $aUrlData = $oView->getViewDataElement("aSeoUrls");

        $this->assertEquals($sObjectId, $oView->getViewDataElement("sActSeoObject"));
        $this->assertTrue(isset($aUrlData[$iLangId]));
        $this->assertEquals($sObjectId, $aUrlData[$iLangId][0]);
        $this->assertEquals($sSeoUrl, $aUrlData[$iLangId][1]);

        //
        $oView->setNonPublicVar("_sActSeoObject", null);
        $oView->UNITloadActiveUrl($iShopId);
        $aUrlData = $oView->getViewDataElement("aSeoUrls");

        $this->assertEquals($sObjectId, $oView->getViewDataElement("sActSeoObject"));
        $this->assertTrue(isset($aUrlData[$iLangId]));
        $this->assertEquals($sObjectId, $aUrlData[$iLangId][0]);
        $this->assertEquals($sSeoUrl, $aUrlData[$iLangId][1]);
    }

    /**
     * Shop_Seo::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxSeoEncoder', 'encodeStaticUrls', '{ throw new Exception( "encodeStaticUrls" ); }');
        oxTestModules::addFunction('oxshop', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxshop', 'setLanguage', '{ return true; }');
        oxTestModules::addFunction('oxshop', 'assign', '{ return true; }');
        oxTestModules::addFunction('oxshop', 'setLanguage', '{ return true; }');
        oxTestModules::addFunction('oxshop', 'save', '{ return true; }');

        $this->setRequestParameter('aStaticUrl', array("staticUrl"));

        $aTasks = array("saveConfVars", "resetContentCache");

        // testing..
        try {
            $oView = $this->getMock("Shop_Seo", $aTasks);
            foreach ($aTasks as $sMethodName) {
                $oView->expects($this->any())->method($sMethodName);
            }
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("encodeStaticUrls", $oExcp->getMessage(), "Error in Shop_Seo::save()");

            return;
        }
        $this->fail("Error in Shop_Seo::save()");
    }

    /**
     * Shop_Seo::ProcessUrls() test case
     *
     * @return null
     */
    public function testProcessUrls()
    {
        // defining parameters
        $aUrls = array('oxseo__oxstdurl' => "stdurl",
                       'oxseo__oxseourl' => array("seourl1", "seourl2"));

        // testing..
        $oView = $this->getMock("Shop_Seo", array("_cleanupUrl"));
        $oView->expects($this->at(0))->method('_cleanupUrl')->with($this->equalTo("stdurl"));
        $oView->expects($this->at(1))->method('_cleanupUrl')->with($this->equalTo("seourl1"));
        $oView->expects($this->at(2))->method('_cleanupUrl')->with($this->equalTo("seourl2"));

        $aUrls = $oView->UNITprocessUrls($aUrls);
    }

    /**
     * Shop_Seo::CleanupUrl() test case
     *
     * @return null
     */
    public function testCleanupUrl()
    {
        // testing..
        $oView = oxNew('Shop_Seo');
        $this->assertEquals("&amp;", $oView->UNITcleanupUrl("&amp;&amp;&&"));
    }

    /**
     * Shop_Seo::DropSeoIds() test case
     *
     * @return null
     */
    public function testDropSeoIds()
    {
        //
        oxTestModules::addFunction('oxSeoEncoder', 'markAsExpired', '{ throw new Exception( "markAsExpired" ); }');

        // testing..
        try {
            $oView = oxNew('Shop_Seo');
            $oView->dropSeoIds();
        } catch (Exception $oExcp) {
            $this->assertEquals("markAsExpired", $oExcp->getMessage(), "error in Shop_Seo::dropSeoIds()");

            return;
        }
        $this->fail("error in Shop_Seo::dropSeoIds()");
    }

    /**
     * Shop_Seo::DeleteStaticUrl() test case
     *
     * @return null
     */
    public function testDeleteStaticUrl()
    {
        $this->setRequestParameter('aStaticUrl', array("oxseo__oxobjectid" => "testObjectId"));
        $this->setRequestParameter('oxid', "1");

        $oDb = oxDb::getDb();

        // inserting test record
        $oDb->execute("insert into oxseo (`OXOBJECTID`, `OXIDENT`, `OXSHOPID`, `OXLANG`, `OXSTDURL`, `OXSEOURL`, `OXTYPE`, `OXFIXED`, `OXEXPIRED`, `OXPARAMS`) values( 'testObjectId', 'testident', '1', '0', 'teststdurl', 'testseourl', 'static', '0', '', '' )");
        $this->assertEquals(1, $oDb->getOne("select 1 from oxseo where oxobjectid = 'testObjectId' and oxshopid = '1'"));

        // testing..
        $oView = oxNew('Shop_Seo');
        $oView->deleteStaticUrl();

        $this->assertFalse($oDb->getOne("select 1 from oxseo where oxobjectid = 'testObjectId' and oxshopid = '1'"));
    }
}
