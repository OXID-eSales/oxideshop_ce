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

use \oxutils;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

class modUtils_oxManufacturerlist extends oxutils
{

    public function seoIsActive($blReset = false, $sShopId = null, $iActLang = null)
    {
        return true;
    }
}

/**
 * Testing oxManufacturerlist class
 */
class ManufacturerlistTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxTestModules::addFunction('oxManufacturer', 'cleanRootManufacturer', '{oxManufacturer::$_aRootManufacturer = array();}');
        oxNew('oxManufacturer')->cleanRootManufacturer();
        oxRemClassModule('modUtils_oxManufacturerlist');

        parent::tearDown();
    }

    /**
     * Test loading simple Manufacturer list by selected language
     */
    public function testLoadManufacturerListByLanguage()
    {
        $myUtils = oxRegistry::getUtils();
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();

        //modConfig::addClassVar("_iLanguageId","1"); //$oManufacturerlist->sLanguage = '1';
        //$myConfig->addClassFunction("getShopLanguage",create_function("","return 1;"));
        oxRegistry::getLang()->setBaseLanguage(1);

        $oManufacturerlist = oxNew('oxManufacturerlist');

        $oManufacturerlist->loadManufacturerList();

        $this->assertTrue((count($oManufacturerlist) > 0), "Manufacturers list not loaded");

        // checking if vendros are the same
        $sQ = 'select oxid, oxtitle_1, oxshortdesc_1 from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '"';
        $rs = $myDB->select($sQ);

        if ($rs != false && $rs->RecordCount() > 0) {
            while (!$rs->EOF) {
                $this->assertEquals($rs->fields[1], $oManufacturerlist[$rs->fields[0]]->oxmanufacturers__oxtitle->value);
                $this->assertEquals(str_replace("'", "&#039;", $rs->fields[2]), $oManufacturerlist[$rs->fields[0]]->oxmanufacturers__oxshortdesc->value);
                $rs->MoveNext();
            }
        } else {
            $this->fail('No records found in Manufacturers table with lang id = 1');
        }
    }

    /**
     * Test loading simple Manufacturer list and counting Manufacturer articles
     */
    public function testLoadManufacturerListAndCountManufacturerArticles()
    {

        $myUtils = oxRegistry::getUtils();

        $this->getConfig()->setConfigParam('bl_perfShowActionCatArticleCnt', true);

        $oManufacturerlist = oxNew('oxManufacturerlist');
        $oManufacturerlist->setShowManufacturerArticleCnt(true);
        $oManufacturerlist->loadManufacturerList();

        foreach ($oManufacturerlist as $sVndId => $value) {
            $iArtCount = $oManufacturerlist[$sVndId]->oxmanufacturers__oxnrofarticles->value;
            $this->assertTrue(($iArtCount > 0), "Manufacturer articles were not counted");
        }
    }

    /**
     * Test creating root for Manufacturer tree, and adding category list fields for each Manufacturer item
     */
    public function testBuildManufacturerTree()
    {
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();

        $oManufacturerlist = $this->getProxyClass("oxManufacturerList"); //oxNew('oxManufacturerlist', 'core');

        // get first Manufacturer id
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . ' "';
        $sFirstManufacturerId = $myDB->getOne($sQ);

        // build Manufacturers and add first Manufacturer to Manufacturers tree path array
        $oManufacturerlist->buildManufacturerTree('manufacturerList', $sFirstManufacturerId, $myConfig->getShopHomeURL());

        //check if root for Manufacturers tree was added
        $aPath = $oManufacturerlist->getPath();


        $this->assertNotNull($oManufacturerlist->getClickManufacturer());
        $this->assertEquals($sFirstManufacturerId, $oManufacturerlist->getClickManufacturer()->getId());
        $this->assertEquals($aPath[0], $oManufacturerlist->getRootCat());
        $this->assertEquals('root', $aPath[0]->getId(), 'Not added root for Manufacturer tree'); //oxManufacturer__oxid->value

        //check if first Manufacturer was added to Manufacturers tree path array
        $this->assertEquals($sFirstManufacturerId, $aPath[1]->getId(), 'Manufacturer was not added to Manufacturers tree path');

        //check if category list fields was added for each Manufacturer item
        foreach ($oManufacturerlist as $sVndId => $value) {
            if (empty($oManufacturerlist[$sVndId]->oxcategories__oxid->value)) {
                $this->fail('Category list fields was not added for each Manufacturer');
            }
        }
    }

    /**
     * Test adding category specific fields to Manufacturer object
     */
    public function testAddCategoryFields()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");
        $myConfig = $this->getConfig();

        $oManufacturerlist = oxNew('oxManufacturerlist');
        $oManufacturerlist->loadManufacturerList();
        $oManufacturer = $oManufacturerlist->current();

        $oManufacturerlist->UNITaddCategoryFields($oManufacturer);

        // check if category specific fields was added to Manufacturer object
        $this->assertEquals($oManufacturer->getId(), $oManufacturer->oxcategories__oxid->value);
        $this->assertEquals($oManufacturer->oxmanufacturers__oxicon, $oManufacturer->oxcategories__oxicon);
        $this->assertEquals($oManufacturer->oxmanufacturers__oxtitle, $oManufacturer->oxcategories__oxtitle);
        $this->assertEquals($oManufacturer->oxmanufacturers__oxshortdesc, $oManufacturer->oxcategories__oxdesc);
        $this->assertEquals($myConfig->getShopHomeURL() . "cl=manufacturerlist&amp;mnid={$oManufacturer->oxcategories__oxid->value}", $oManufacturer->getLink());

        $this->assertTrue($oManufacturer->getIsVisible());
        $this->assertFalse($oManufacturer->hasVisibleSubCats);
    }

    /**
     * Test adding SEO links to Manufacturer object
     */
    public function testSEOsetManufacturerData()
    {
        oxAddClassModule('modUtils_oxManufacturerlist', 'oxutils');

        $oManufacturerlist = $this->getProxyClass("oxManufacturerlist");
        $oManufacturerlist->loadManufacturerList();

        $oManufacturerlist->UNITSeosetManufacturerData();

        //check if SEO link was added for each Manufacturer item
        foreach ($oManufacturerlist as $sVndId => $value) {
            $sManufacturerLink = $oManufacturerlist[$sVndId]->link;
            if (!$sManufacturerLink || strstr($sManufacturerLink, 'index.php') !== false) {
                $this->fail("SEO link was not added to Manufacturer object ({$sManufacturerLink})");
            }
        }

    }

}
