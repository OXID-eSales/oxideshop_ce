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
namespace Unit\Application\Model;

use \oxDb;
use \oxRegistry;
use \oxTestModules;
use oxUtilsHelper;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxUtilsHelper.php';
/**
 * Testing oxvendorlist class
 */
class VendorlistTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxTestModules::addFunction('oxVendor', 'cleanRootVendor', '{oxVendor::$_aRootVendor = array();}');
        oxNew('oxvendor')->cleanRootVendor();
        oxRemClassModule('oxUtilsHelper');

        parent::tearDown();
    }

    /**
     * Test loading simple vendor list by selected language
     */
    public function test_loadVendorListByLanguage()
    {
        $myUtils = oxRegistry::getUtils();
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();

        oxRegistry::getLang()->setBaseLanguage(1);

        $oVendorlist = oxNew('oxvendorlist');

        $oVendorlist->loadVendorList();

        $this->assertTrue((count($oVendorlist) > 0), "Vendors list not loaded");

        // checking if vendros are the same
        $sQ = 'select oxid, oxtitle_1, oxshortdesc_1 from oxvendor where oxvendor.oxshopid = "' . $myConfig->getShopID() . '"';
        $rs = $myDB->select($sQ);

        if ($rs != false && $rs->count() > 0) {
            while (!$rs->EOF) {
                $this->assertEquals($rs->fields[1], $oVendorlist[$rs->fields[0]]->oxvendor__oxtitle->value);
                $this->assertEquals($rs->fields[2], $oVendorlist[$rs->fields[0]]->oxvendor__oxshortdesc->value);
                $rs->MoveNext();
            }
        } else {
            $this->fail('No records found in vendors table with lang id = 1');
        }
    }

    /**
     * Test loading simple vendor list and counting vendor articles
     */
    public function test_loadVendorListAndCountVendorArticles()
    {
        $myUtils = oxRegistry::getUtils();

        $this->getConfig()->setConfigParam('bl_perfShowActionCatArticleCnt', true);

        $oVendorlist = oxNew('oxvendorlist');
        $oVendorlist->setShowVendorArticleCnt(true);
        $oVendorlist->loadVendorList();

        foreach ($oVendorlist as $sVndId => $value) {
            $iArtCount = $oVendorlist[$sVndId]->oxvendor__oxnrofarticles->value;
            $this->assertTrue(($iArtCount > 0), "Vendor articles were not counted");
        }
    }

    /**
     * Test creating root for vendor tree, and adding category list fields for each vendor item
     */
    public function test_BuildVendorTree()
    {
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();

        $oVendorlist = $this->getProxyClass("oxvendorList"); //oxNew('oxvendorlist', 'core');

        // get first vendor id
        $sQ = 'select oxid from oxvendor where oxvendor.oxshopid = "' . $myConfig->getShopID() . ' "';
        $sFirstVendorId = $myDB->getOne($sQ);

        // build vendors and add first vendor to vendors tree path array
        $oVendorlist->buildVendorTree('vendorList', $sFirstVendorId, $myConfig->getShopHomeURL());

        //check if root for vendors tree was added
        $aPath = $oVendorlist->getPath();


        $this->assertNotNull($oVendorlist->getClickVendor());
        $this->assertEquals($sFirstVendorId, $oVendorlist->getClickVendor()->getId());
        $this->assertEquals($aPath[0], $oVendorlist->getRootCat());
        $this->assertEquals('root', $aPath[0]->getId(), 'Not added root for vendor tree'); //oxvendor__oxid->value

        //check if first vendor was added to vendors tree path array
        $this->assertEquals($sFirstVendorId, $aPath[1]->getId(), 'Vendor was not added to vendors tree path');

        //check if category list fields was added for each vendor item
        foreach ($oVendorlist as $sVndId => $value) {
            if (empty($oVendorlist[$sVndId]->oxcategories__oxid->value)) {
                $this->fail('Category list fields was not added for each vendor');
            }
        }
    }

    /**
     * Test adding category specific fields to vendor object
     */
    public function test_addCategoryFields()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");
        $myConfig = $this->getConfig();

        $oVendorlist = oxNew('oxvendorlist');
        $oVendorlist->loadVendorList();
        $oVendor = $oVendorlist->current();

        $oVendorlist->UNITaddCategoryFields($oVendor);

        // check if category specific fields was added to vendor object
        $this->assertEquals("v_" . $oVendor->getId(), $oVendor->oxcategories__oxid->value);
        $this->assertEquals($oVendor->oxvendor__oxicon, $oVendor->oxcategories__oxicon);
        $this->assertEquals($oVendor->oxvendor__oxtitle, $oVendor->oxcategories__oxtitle);
        $this->assertEquals($oVendor->oxvendor__oxshortdesc, $oVendor->oxcategories__oxdesc);
        $this->assertEquals($myConfig->getShopHomeURL() . "cl=vendorlist&amp;cnid={$oVendor->oxcategories__oxid->value}", $oVendor->getLink());

        $this->assertTrue($oVendor->getIsVisible());
        $this->assertFalse($oVendor->getHasVisibleSubCats());
    }

    /**
     * Test adding SEO links to vendor object
     */
    public function test_SEOsetVendorData()
    {
        oxUtilsHelper::$sSeoIsActive = true;
        oxAddClassModule('oxUtilsHelper', 'oxutils');

        $oVendorlist = $this->getProxyClass("oxvendorlist");
        $oVendorlist->loadVendorList();

        $oVendorlist->UNITSeosetVendorData();

        //check if SEO link was added for each vendor item
        foreach ($oVendorlist as $sVndId => $value) {
            $sVendorLink = $oVendorlist[$sVndId]->getLink();
            if (!$sVendorLink || strstr($sVendorLink, 'index.php') !== false) {
                $this->fail("SEO link was not added to vendor object ({$sVendorLink})");
            }
        }

    }

}
