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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */
namespace Unit\Application\Model;

use modDB;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use \oxSeoEncoder;
use \Exception;
use \oxField;
use \oxDb;
use \oxRegistry;
use oxSeoEncoderHelper;
use \oxTestModules;

class modSeoEncoder extends oxSeoEncoder
{

    public function setProhibitedID($aProhibitedID)
    {
        $this->_aProhibitedID = $aProhibitedID;
    }

    public function getSeparator()
    {
        return oxSeoEncoder::$_sSeparator;
    }

    public function getSafePrefix()
    {
        return $this->_getSafePrefix();
    }

    public function setAltPrefix($soxID)
    {
        $this->_sAltPrefix = $soxID;
    }

    public function p_prepareTitle($a, $b = false)
    {
        return $this->_prepareTitle($a, $b);
    }

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     */
    public function __call($method, $args)
    {
        if (defined('OXID_PHP_UNIT')) {
            if (substr($method, 0, 4) == "UNIT") {
                $method = str_replace("UNIT", "_", $method);
            }
            if (method_exists($this, $method)) {
                return call_user_func_array(array(& $this, $method), $args);
            }
        }

        throw new Exception("Function '$method' does not exist or is not accessable!" . PHP_EOL);
    }
}

/**
 * Testing oxseoencoder class
 */
class SeoEncoderTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');
        oxDb::getDb()->execute('delete from oxobject2seodata');
        oxDb::getDb()->execute('delete from oxseohistory');

        parent::setUp();

        oxRegistry::get("oxSeoEncoder")->setPrefix('oxid');
        oxRegistry::get("oxSeoEncoder")->setSeparator();
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
        //echo $this->getName()."\n";
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        // deleting seo entries
        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');
        oxDb::getDb()->execute('delete from oxobject2seodata');
        oxDb::getDb()->execute('delete from oxseohistory');

        $this->cleanUpTable('oxcategories');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxarticles');

        // cleanup
        if ($this->getName() == 'testIfArticleMetaDataStoredInSeoTableIsKeptAfterArticleTitleWasChanged') {
            $oArticle = oxNew('oxArticle');
            $oArticle->delete('_testArticle');
        }

        $oConfig = $this->getConfig();

        // restore..
        $cl = oxTestModules::addFunction('oxSeoEncoder', 'clean_aReservedEntryKeys', '{oxSeoEncoder::$_aReservedEntryKeys = null;}');
        $oEncoder = new $cl();
        $oEncoder->setSeparator($oConfig->getConfigParam('sSEOSeparator'));
        $oEncoder->setPrefix($oConfig->getConfigParam('sSEOuprefix'));
        $oEncoder->setReservedWords($oConfig->getConfigParam('aSEOReservedWords'));
        $oEncoder->clean_aReservedEntryKeys();

        parent::tearDown();
    }

    public function __SaveToDbCreatesGoodMd5Callback($sSQL)
    {
        $this->aSQL[] = $sSQL;
        if ($this->aRET && isset($this->aRET[count($this->aSQL) - 1])) {
            return $this->aRET[count($this->aSQL) - 1];
        }
    }

    /**
     * oxSeoEncoder::_getAltUri() test case
     *
     * @return null
     */
    public function testGetAltUri()
    {
        $oEncoder = oxNew('oxSeoEncoder');
        $this->assertNull($oEncoder->UNITgetAltUri("", ""));
    }

    /**
     *
     * @return
     */
    public function testAddSeoEntryForGetAltUriCall()
    {
        $sObjectId = '';
        $iShopId = '';
        $iLang = '';
        $sStdUrl = '';
        $sSeoUrl = false;
        $sType = '';
        $blFixed = '';
        $sKeywords = '';
        $sDescription = '';
        $sParams = '';
        $blExclude = true;
        $sAltObjectId = 'testAltId';

        $oEncoder = $this->getMock("oxSeoEncoder", array("_processSeoUrl", "_trimUrl", "_getAltUri", "_saveToDb"));
        $oEncoder->expects($this->once())->method('_processSeoUrl')->with(
            $this->equalTo('testTrimmedUrl'),
            $this->equalTo($sObjectId),
            $this->equalTo($iLang),
            $this->equalTo($blExclude)
        )
            ->will($this->returnValue('testProcessedSeoUrl'));
        $oEncoder->expects($this->once())->method('_trimUrl')->with($this->equalTo('testAltUri'))->will($this->returnValue('testTrimmedUrl'));
        $oEncoder->expects($this->once())->method('_getAltUri')->with($this->equalTo($sAltObjectId))->will($this->returnValue('testAltUri'));
        $oEncoder->expects($this->once())->method('_saveToDb')->with(
            $this->equalTo($sType),
            $this->equalTo($sObjectId),
            $this->equalTo($sStdUrl),
            $this->equalTo('testProcessedSeoUrl'),
            $this->equalTo($iLang),
            $this->equalTo($iShopId),
            $this->equalTo($blFixed),
            $this->equalTo($sParams)
        );

        $oEncoder->addSeoEntry($sObjectId, $iShopId, $iLang, $sStdUrl, $sSeoUrl, $sType, $blFixed, $sKeywords, $sDescription, $sParams, $blExclude, $sAltObjectId);
    }

    /**
     * Test case for bug entry #1748
     *
     * @return null
     */
    public function testPrepareUriForBugEntry1748()
    {
        $sChars = "\ + * ? [ ^ ] $ ( ) { } = ! < > | :";

        $oEncoder = oxNew('oxSeoEncoder');
        $oEncoder->setSeparator("+");
        $oEncoder->setPrefix("-");
        $oEncoder->setReservedWords(explode(" ", $sChars));

        $this->assertEquals("http/www+oxideshop+com/", $oEncoder->UNITprepareUri("http://www!oxideshop~com"));
    }

    /**
     *
     * @return null
     */
    public function testPrepareUriNonRec()
    {
        $oEncoder = oxNew('oxSeoEncoder');
        $oEncoder->setSeparator("+");
        $oEncoder->setPrefix("-");
        $oEncoder->setReservedWords(array('-'));

        $this->assertEquals("-+-/", $oEncoder->UNITprepareUri("-"));
    }

    /**
     * Test for #0001664
     *
     * @return null
     */
    public function testGetContentLink0001664()
    {
        $iLang = 0;
        oxRegistry::get("oxSeoEncoder")->setPrefix("_");

        $oContent = oxNew('oxContent');
        $oContent->setId("_testContent");

        $this->assertEquals($this->getConfig()->getShopUrl($iLang) . "_/", $oContent->getLink($iLang));
    }

    public function testIfMetaDataIsEncodedCorrectlyWhileSaving()
    {
        $iShopId = $this->getConfig()->getBaseShopId();
        $iLang = 0;
        $sObjectId = 'testobject';

        $sInKeywords = "Laufräder '\"";
        $sInDescription = "Laufräder '\"";

        $sOutKeywords = "Laufräder &#039;&quot;";
        $sOutDescription = "Laufräder &#039;&quot;";

        $oEncoder = oxNew('oxSeoEncoder');
        $oEncoder->addSeoEntry($sObjectId, $iShopId, $iLang, 'stdurl', 'seourl', 'oxarticle', 0, $sInKeywords, $sInDescription);

        $this->assertEquals($sOutKeywords, $oEncoder->getMetaData($sObjectId, 'oxdescription', $iShopId, $iLang));
        $this->assertEquals($sOutDescription, $oEncoder->getMetaData($sObjectId, 'oxkeywords', $iShopId, $iLang));
    }

    public function testIfSeoDataUpdatedWhenSavingArticleSeoInfo()
    {
        $oDb = oxDb::getDb();

        $iShopId = $this->getConfig()->getShopId();
        $iLang = 0;
        $sStdUrl = "index.php?cl=details&amp;anid={$sOxid}";
        $sType = "oxarticle";

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticleIdxxx');
        $oArticle->oxarticles__oxshopid = new oxField($iShopId);
        $oArticle->oxarticles__oxtitle = new oxField('test article title');
        $oArticle->oxarticles__oxactive = new oxField(1);
        $oArticle->save();

        $sOxid = $oArticle->getId();

        $oArticle->getLink();

        $oEncoder = oxRegistry::get("oxSeoEncoder");

        // 1. Categorie "Geschenke" SEO URL "blafusel/" and Fixed URL = On
        $oEncoder->markAsExpired($sOxid, $iShopId, 1, $iLang);
        $oEncoder->addSeoEntry($sOxid, $iShopId, $iLang, $sStdUrl, "blafusel/", $sType, true, null, null, null, true);

        // checking fixed state
        $this->assertEquals(1, $oDb->getOne("select oxfixed from oxseo where oxobjectid='{$sOxid}' and oxshopid='{$iShopId}' and oxlang='{$iLang}' and oxparams=''"));

        // 2. Change SEO URL to "somethingelse/" and Fixed URL = OFF => Save => works fine old and new SEO URL
        $oEncoder->markAsExpired($sOxid, $iShopId, 1, $iLang);
        $oEncoder->addSeoEntry($sOxid, $iShopId, $iLang, $sStdUrl, "somethingelse/", $sType, false, "", "", "", true);

        // checking fixed state
        $this->assertEquals(0, $oDb->getOne("select oxfixed from oxseo where oxobjectid='{$sOxid}' and oxshopid='{$iShopId}' and oxlang='{$iLang}'"));

        // 3. Change back to SEO URL "blafusel/" and Fixed URL = On => Save => "blafusel/" works and "somethingelse/" is deleted from oxseo and isnt accessible anymore
        $oEncoder->markAsExpired($sOxid, $iShopId, 1, $iLang);
        $oEncoder->addSeoEntry($sOxid, $iShopId, $iLang, $sStdUrl, "blafusel/", $sType, true, "", "", "", true);

        // checking fixed state
        $this->assertEquals(1, $oDb->getOne("select oxfixed from oxseo where oxobjectid='{$sOxid}' and oxshopid='{$iShopId}' and oxlang='{$iLang}' and oxparams=''"));
    }

    /**
     *  1. Categorie "Geschenke" SEO URL "blafusel/" and Fixed URL = On
     *  2. Change SEO URL to "somethingelse/" and Fixed URL = OFF => Save => works fine old and new SEO URL
     *  3. Change back to SEO URL "blafusel/" and Fixed URL = On => Save => "blafusel/" works and "somethingelse/" is deleted from oxseo and isnt accessible anymore
     */
    public function testHowHistoryTableIsFilledByUseCase()
    {
        $oDb = oxDb::getDb();
        $oDb->execute("delete from oxseohistory");

        $sOxid = $this->getTestConfig()->getShopEdition() == 'EE' ? "30e44ab82c03c3848.49471214" : "8a142c3e4143562a5.46426637";

        $iShopId = $this->getConfig()->getShopId();
        $iLang = 0;
        $sStdUrl = "index.php?cl=alist&amp;cnid={$sOxid}";
        $sType = "oxcategory";

        $oCategory = oxNew('oxCategory');
        $oCategory->setId('_testCategoryIdxxx');
        $oCategory->oxcategories__oxshopid = new oxField($iShopId);
        $oCategory->oxcategories__oxtitle = new oxField('test article title');
        $oCategory->oxcategories__oxactive = new oxField(1);
        $oCategory->save();

        $sOxid = $oCategory->getId();

        $oCategory->getLink();

        $oEncoder = oxRegistry::get("oxSeoEncoder");

        // 1. Categorie "Geschenke" SEO URL "blafusel/" and Fixed URL = On
        $oEncoder->markAsExpired($sOxid, $iShopId, 1, $iLang);
        $oEncoder->addSeoEntry($sOxid, $iShopId, $iLang, $sStdUrl, "blafusel/", $sType, true, null, null, null, true);

        // one entry should contain history table
        $this->assertEquals(1, $oDb->getOne("select count(*) from oxseohistory where oxobjectid='{$sOxid}'"));

        // 2. Change SEO URL to "somethingelse/" and Fixed URL = OFF => Save => works fine old and new SEO URL
        $oEncoder->markAsExpired($sOxid, $iShopId, 1, $iLang);
        $oEncoder->addSeoEntry($sOxid, $iShopId, $iLang, $sStdUrl, "somethingelse/", $sType, false, "", "", "", true);

        // two entries should contain history table
        $this->assertEquals(2, $oDb->getOne("select count(*) from oxseohistory where oxobjectid='{$sOxid}'"));

        // 3. Change back to SEO URL "blafusel/" and Fixed URL = On => Save => "blafusel/" works and "somethingelse/" is deleted from oxseo and isnt accessible anymore
        $oEncoder->markAsExpired($sOxid, $iShopId, 1, $iLang);
        $oEncoder->addSeoEntry($sOxid, $iShopId, $iLang, $sStdUrl, "blafusel/", $sType, true, "", "", "", true);

        // still two entries should contain history table
        $this->assertEquals(3, $oDb->getOne("select count(*) from oxseohistory where oxobjectid='{$sOxid}'"));
    }

    public function testUpdateSeoUrlWithDifferentCharCases()
    {
        $oConfig = $this->getConfig();
        $oDb = oxDb::getDb();

        $iShopId = $oConfig->getShopId();
        $sOxid = '123';

        $oEncoder = oxNew('oxSeoEncoder');

        // initially writing first one
        $oEncoder->markAsExpired($sOxid, $iShopId, 1, 0);
        $oEncoder->addSeoEntry($sOxid, $iShopId, 0, 'stdurl', 'seourl', 'oxarticle', 0, '', '', '', true);

        // checking
        $this->assertTrue(strcmp('seourl/', $oDb->getOne("select oxseourl from oxseo where oxobjectid='{$sOxid}'")) == 0);

        // checking if value is updated
        $oEncoder->markAsExpired($sOxid, $iShopId, 1, 0);
        $oEncoder->addSeoEntry($sOxid, $iShopId, 0, 'stdurl', 'SeOuRl', 'oxarticle', 0, '', '', '', true);

        // checking
        $this->assertTrue(strcmp('SeOuRl/', $oDb->getOne("select oxseourl from oxseo where oxobjectid='{$sOxid}'")) == 0);
    }

    public function testProcessSeoUrl()
    {
        $sSeoUrl = "seourl/";
        $oEncoder = oxNew('oxSeoEncoder');
        $this->assertEquals($sSeoUrl, $oEncoder->UNITprocessSeoUrl($sSeoUrl, null, 1, true));
        $this->assertEquals("en/" . $sSeoUrl, $oEncoder->UNITprocessSeoUrl($sSeoUrl, null, 1, false));
    }

    public function testLanguagePrefixForSeoUrlForDe()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $oConfig = $this->getConfig();

        // inserting price category for test
        $oPriceCategory = oxNew('oxCategory');
        $oPriceCategory->setId("_testPriceCategoryId");
        $oPriceCategory->oxcategories__oxparentid = new oxField("oxrootid");
        $oPriceCategory->oxcategories__oxrootid = $oPriceCategory->getId();
        $oPriceCategory->oxcategories__oxactive = new oxField(1);
        $oPriceCategory->oxcategories__oxshopid = new oxField($oConfig->getBaseShopId());
        $oPriceCategory->oxcategories__oxtitle = new oxField("Test Price Category DE");
        $oPriceCategory->oxcategories__oxpricefrom = new oxField(0);
        $oPriceCategory->oxcategories__oxpriceto = new oxField(999);
        $oPriceCategory->save();

        $sShopUrl = $oConfig->getShopUrl(0);

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sArticleId = "1849";
            $sArticleSeoUrl = $sShopUrl . "Party/Bar-Equipment/Bar-Butler-6-BOTTLES.html";
            $sArticleVendorSeoUrl = $sShopUrl . "Nach-Lieferant/Hersteller-1/Bar-Butler-6-BOTTLES.html";
            $sArticleManufacturerSeoUrl = $sShopUrl . "Nach-Hersteller/Hersteller-1/Bar-Butler-6-BOTTLES.html";
            $sArticlePriceCatSeoUrl = $sShopUrl . "Test-Price-Category-DE/Bar-Butler-6-BOTTLES.html";
            $sCategoryId = "30e44ab82c03c3848.49471214";
            $sCategorySeoUrl = $sShopUrl . "Fuer-Sie/";
            $sManufacturerId = "88a996f859f94176da943f38ee067984";
            $sManufacturerSeoUrl = $sShopUrl . "Nach-Hersteller/Hersteller-1/";
            $sVendorId = "d2e44d9b31fcce448.08890330";
            $sVendorSeoUrl = $sShopUrl . "Nach-Lieferant/Hersteller-1/";
        } else {
            $sArticleId = "1964";
            $sArticleSeoUrl = $sShopUrl . "Geschenke/Original-BUSH-Beach-Radio.html";
            $sArticleVendorSeoUrl = $sShopUrl . "Nach-Lieferant/Bush/Original-BUSH-Beach-Radio.html";
            $sArticleManufacturerSeoUrl = $sShopUrl . "Nach-Hersteller/Bush/Original-BUSH-Beach-Radio.html";
            $sArticlePriceCatSeoUrl = $sShopUrl . "Test-Price-Category-DE/Original-BUSH-Beach-Radio.html";
            $sCategoryId = "8a142c3e4143562a5.46426637";
            $sCategorySeoUrl = $sShopUrl . "Geschenke/";
            $sManufacturerId = "fe07958b49de225bd1dbc7594fb9a6b0";
            $sManufacturerSeoUrl = $sShopUrl . "Nach-Hersteller/Haller-Stahlwaren/";
            $sVendorId = "68342e2955d7401e6.18967838";
            $sVendorSeoUrl = $sShopUrl . "Nach-Lieferant/Haller-Stahlwaren/";
        }

        $sContentId = "f41427a099a603773.44301043";
        $sContentSeoUrl = $sShopUrl . "Datenschutz/";

        $oCategory = oxNew('oxCategory');
        $oCategory->load($sCategoryId);

        $oView = $this->getMock("oxUBase", array("getActiveCategory"));
        $oView->expects($this->at(0))->method('getActiveCategory')->will($this->returnValue($oCategory));
        $oView->expects($this->at(1))->method('getActiveCategory')->will($this->returnValue($oPriceCategory));

        $oConfig->dropLastActiveView();
        $oConfig->setActiveView($oView);

        $oArticle = oxNew('oxArticle');
        $oArticle->load($sArticleId);
        $oArticle->setLinkType(OXARTICLE_LINKTYPE_CATEGORY);
        $this->assertEquals($sArticleSeoUrl, $oArticle->getLink(0));

        $oArticle->setLinkType(OXARTICLE_LINKTYPE_VENDOR);
        $this->assertEquals($sArticleVendorSeoUrl, $oArticle->getLink(0));

        $oArticle->setLinkType(OXARTICLE_LINKTYPE_MANUFACTURER);
        $this->assertEquals($sArticleManufacturerSeoUrl, $oArticle->getLink(0));

        $oArticle->setLinkType(OXARTICLE_LINKTYPE_PRICECATEGORY);
        $this->assertEquals($sArticlePriceCatSeoUrl, $oArticle->getLink(0));

        $oCategory = oxNew('oxCategory');
        $oCategory->load($sCategoryId);
        $this->assertEquals($sCategorySeoUrl, $oCategory->getLink(0));

        $oContent = oxNew('oxContent');
        $oContent->load($sContentId);
        $this->assertEquals($sContentSeoUrl, $oContent->getLink(0));

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->load($sManufacturerId);
        $this->assertEquals($sManufacturerSeoUrl, $oManufacturer->getLink(0));

        $oVendor = oxNew('oxVendor');
        $oVendor->load($sVendorId);
        $this->assertEquals($sVendorSeoUrl, $oVendor->getLink(0));
        // missing static urls..
    }

    public function testLanguagePrefixForSeoUrlForEn()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $oConfig = $this->getConfig();

        // inserting price category for test
        $oPriceCategory = oxNew('oxCategory');
        $oPriceCategory->setId("_testPriceCategoryId");
        $oPriceCategory->oxcategories__oxparentid = new oxField("oxrootid");
        $oPriceCategory->oxcategories__oxrootid = $oPriceCategory->getId();
        $oPriceCategory->oxcategories__oxactive = new oxField(1);
        $oPriceCategory->oxcategories__oxshopid = new oxField($oConfig->getBaseShopId());
        $oPriceCategory->oxcategories__oxtitle = new oxField("Test Price Category DE");
        $oPriceCategory->oxcategories__oxpricefrom = new oxField(0);
        $oPriceCategory->oxcategories__oxpriceto = new oxField(999);
        $oPriceCategory->save();
        $oPriceCategory->setLanguage(1);
        $oPriceCategory->save();

        $sShopUrl = $oConfig->getShopUrl(0);
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sArticleId = "6b63f459c781fa42edeb889242304014";
            $sArticleSeoUrl = $sShopUrl . "en/Eco-Fashion/Woman/Shirts/Stewart-Brown-Organic-Pima-Edged-Lengthen.html";
            $sArticleVendorSeoUrl = $sShopUrl . "en/By-distributor/true-fashion-com/Stewart-Brown-Organic-Pima-Edged-Lengthen.html";
            $sArticleManufacturerSeoUrl = $sShopUrl . "en/By-manufacturer/Stewart-Brown/Stewart-Brown-Organic-Pima-Edged-Lengthen.html";
            $sArticlePriceCatSeoUrl = $sShopUrl . "en/Test-Price-Category-DE/Stewart-Brown-Organic-Pima-Edged-Lengthen.html";
            $sCategoryId = "30e44ab82c03c3848.49471214";
            $sCategorySeoUrl = $sShopUrl . "en/For-Her/";
            $sManufacturerId = "88a996f859f94176da943f38ee067984";
            $sManufacturerSeoUrl = $sShopUrl . "en/By-manufacturer/Manufacturer-1/";
            $sVendorId = "d2e44d9b31fcce448.08890330";
            $sVendorSeoUrl = $sShopUrl . "en/By-distributor/Manufacturer-1/";
        } else {
            $sArticleId = "1964";
            $sArticleSeoUrl = $sShopUrl . "en/Gifts/Original-BUSH-Beach-Radio.html";
            $sArticleVendorSeoUrl = $sShopUrl . "en/By-distributor/Bush/Original-BUSH-Beach-Radio.html";
            $sArticleManufacturerSeoUrl = $sShopUrl . "en/By-manufacturer/Bush/Original-BUSH-Beach-Radio.html";
            $sArticlePriceCatSeoUrl = $sShopUrl . "en/Test-Price-Category-DE/Original-BUSH-Beach-Radio.html";
            $sCategoryId = "8a142c3e4143562a5.46426637";
            $sCategorySeoUrl = $sShopUrl . "en/Gifts/";
            $sManufacturerId = "fe07958b49de225bd1dbc7594fb9a6b0";
            $sManufacturerSeoUrl = $sShopUrl . "en/By-manufacturer/Haller-Stahlwaren/";
            $sVendorId = "68342e2955d7401e6.18967838";
            $sVendorSeoUrl = $sShopUrl . "en/By-distributor/Haller-Stahlwaren/";
        }

        $sContentId = "f41427a099a603773.44301043";
        $sContentSeoUrl = $sShopUrl . "en/Privacy-Policy/";

        $oCategory = oxNew('oxCategory');
        $oCategory->load($sCategoryId);

        $oView = $this->getMock("oxUBase", array("getActiveCategory"));
        $oView->expects($this->at(0))->method('getActiveCategory')->will($this->returnValue($oCategory));
        $oView->expects($this->at(1))->method('getActiveCategory')->will($this->returnValue($oPriceCategory));

        $oConfig->dropLastActiveView();
        $oConfig->setActiveView($oView);

        $oArticle = oxNew('oxArticle');
        $oArticle->setLinkType(OXARTICLE_LINKTYPE_CATEGORY);
        $oArticle->load($sArticleId);
        $this->assertEquals($sArticleSeoUrl, $oArticle->getLink(1));

        $oArticle->setLinkType(OXARTICLE_LINKTYPE_VENDOR);
        $this->assertEquals($sArticleVendorSeoUrl, $oArticle->getLink(1));

        $oArticle->setLinkType(OXARTICLE_LINKTYPE_MANUFACTURER);
        $this->assertEquals($sArticleManufacturerSeoUrl, $oArticle->getLink(1));

        $oArticle->setLinkType(OXARTICLE_LINKTYPE_PRICECATEGORY);
        $this->assertEquals($sArticlePriceCatSeoUrl, $oArticle->getLink(1));

        $oCategory = oxNew('oxCategory');
        $oCategory->load($sCategoryId);
        $this->assertEquals($sCategorySeoUrl, $oCategory->getLink(1));

        $oContent = oxNew('oxContent');
        $oContent->load($sContentId);
        $this->assertEquals($sContentSeoUrl, $oContent->getLink(1));

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->load($sManufacturerId);
        $this->assertEquals($sManufacturerSeoUrl, $oManufacturer->getLink(1));

        $oVendor = oxNew('oxVendor');
        $oVendor->load($sVendorId);
        $this->assertEquals($sVendorSeoUrl, $oVendor->getLink(1));
        // missing static urls..
    }

    public function testGetReservedEntryKeys()
    {
        oxTestModules::publicize('oxSeoEncoder', '_getReservedEntryKeys');
        oxTestModules::addFunction('oxSeoEncoder', 'setREKCache', '{ oxSeoEncoder::$_aReservedEntryKeys = $aA[0]; }');
        $oE = oxNew('oxSeoEncoder');

        $oE->setREKCache(array('arraa'));
        $this->assertEquals(array('arraa'), $oE->p_getReservedEntryKeys());

        $oE->setREKCache(null);
        $this->assertTrue(is_array($oE->p_getReservedEntryKeys()));
        $this->assertTrue(count($oE->p_getReservedEntryKeys()) > 10);
    }

    public function testGetFullUrl()
    {
        oxTestModules::addFunction('oxUtilsUrl', 'processSeoUrl($url)', '{return "PROC".$url."CORP";}');

        $oConfig = $this->getMock('oxconfig', array('getShopUrl'));
        $oConfig->expects($this->once())->method('getShopUrl')->with($this->equalTo(1))->will($this->returnValue('url/'));

        $oEncoder = $this->getMock('oxseoencoder', array('getConfig'), array(), '', false);
        $oEncoder->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals('PROCurl/seouri/CORP', $oEncoder->UNITgetFullUrl('seouri/', 1));
    }

    public function testSettingEmptyMetaDataWhileUpdatingObjectSeoInfo()
    {
        $iShopId = $this->getConfig()->getBaseShopId();
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $oEncoder = oxNew('oxSeoEncoder');
        $oEncoder->addSeoEntry('testid', $iShopId, 0, 'index.php?cl=std', 'seo/url/', 'oxcategory', 0, 'oxkeywords', 'oxdescription', '');

        $sQ = "select oxkeywords from oxobject2seodata where oxobjectid = 'testid' and oxshopid = '{$iShopId}' and oxlang = 0 ";
        $this->assertEquals('oxkeywords', $oDb->getOne($sQ));

        $sQ = "select oxdescription from oxobject2seodata where oxobjectid = 'testid' and oxshopid = '{$iShopId}' and oxlang = 0 ";
        $this->assertEquals('oxdescription', $oDb->getOne($sQ));

        $oEncoder = oxNew('oxSeoEncoder');
        $oEncoder->addSeoEntry('testid', $iShopId, 0, 'index.php?cl=std', 'seo/url/', 'oxcategory', 0, '', '', '');

        $sQ = "select oxkeywords from oxobject2seodata where oxobjectid = 'testid' and oxshopid = '{$iShopId}' and oxlang = 0 ";
        $this->assertEquals('', $oDb->getOne($sQ));

        $sQ = "select oxdescription from oxobject2seodata where oxobjectid = 'testid' and oxshopid = '{$iShopId}' and oxlang = 0 ";
        $this->assertEquals('', $oDb->getOne($sQ));
    }


    public function testIfArticleMetaDataStoredInSeoTableIsKeptAfterArticleTitleWasChanged()
    {

        // creating some article
        $oArticle = $this->getMock('oxArticle', array('isAdmin', 'canDo', 'getRights'));
        $oArticle->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oArticle->expects($this->any())->method('canDo')->will($this->returnValue(true));
        $oArticle->expects($this->any())->method('getRights')->will($this->returnValue(false));
        $oArticle->setId('_testArticle');
        $oArticle->oxarticles__oxtitle = new oxField('testarticletitle');
        $oArticle->save();

        // saving its meta data
        $oEncoder = oxRegistry::get("oxSeoEncoder");
        $oEncoder->addSeoEntry(
            $oArticle->getId(), $oArticle->getShopId(), $oArticle->getLanguage(), 'http://stdlink',
            $oArticle->getLink(), 'oxarticle', 0, 'oxseo oxkeywords', 'oxseo oxdescription', ''
        );

        // now testing if meta data was stored..
        $this->assertEquals('oxseo oxdescription', $oEncoder->getMetaData($oArticle->getId(), 'oxdescription'));
        $this->assertEquals('oxseo oxkeywords', $oEncoder->getMetaData($oArticle->getId(), 'oxkeywords'));

        // setting new title for product
        $oArticle->oxarticles__oxtitle = new oxField('new testarticletitle');
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticle');
        $oArticle->getLink();

        // testing if meta data was kept the same..
        $this->assertEquals('oxseo oxdescription', $oEncoder->getMetaData($oArticle->getId(), 'oxdescription'));
        $this->assertEquals('oxseo oxkeywords', $oEncoder->getMetaData($oArticle->getId(), 'oxkeywords'));

        // resetting seo
        $oEncoder->markAsExpired(null, $oArticle->getShopId());

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticle');
        $oArticle->getLink();

        // testing if meta data was kept the same..
        $this->assertEquals('oxseo oxdescription', $oEncoder->getMetaData($oArticle->getId(), 'oxdescription'));
        $this->assertEquals('oxseo oxkeywords', $oEncoder->getMetaData($oArticle->getId(), 'oxkeywords'));
    }

    public function testFetchSeoUrl()
    {
        oxTestModules::addFunction("oxUtils", "seoIsActive", "{ return true;}");
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');
        $oArticle->getLink();

        $oCat = $oArticle->getCategory();

        $sStdUrl = 'index.php?cl=details&amp;anid=1126&amp;cnid=' . ($oCat ? $oCat->getId() : '');

        $oEncoder = oxNew('oxSeoEncoder');

        $categoryUrl = $this->getTestConfig()->getShopEdition() == 'EE' ? "Party/Bar-Equipment" : "Geschenke/Bar-Equipment";
        $this->assertEquals("$categoryUrl/Bar-Set-ABSINTH.html", $oEncoder->fetchSeoUrl($sStdUrl));
    }

    public function testFetchSeoUrlNoAvailable()
    {
        $sStdUrl = 'index.php?cl=details&amp;anid=1126';
        $oEncoder = oxNew('oxseoencoder');
        $this->assertFalse($oEncoder->fetchSeoUrl($sStdUrl));
    }

    public function testPrepareSpecificTitle()
    {
        $sTitle = "Wie/bestellen?/" . str_repeat('a', 200) . ' ' . str_repeat('a', 200) . ' ' . str_repeat('a', 200);
        $sResult = "Wie-bestellen-" . str_repeat('a', 200) . '-' . str_repeat('a', 200);

        $oEncoder = oxNew('oxSeoEncoder');
        $this->assertEquals($sResult, $oEncoder->UNITprepareTitle($sTitle));
        $this->assertEquals($sResult, $oEncoder->UNITprepareTitle($sTitle, false));
        $this->assertEquals($sResult . '-' . str_repeat('a', 200), $oEncoder->UNITprepareTitle($sTitle, true));
    }

    public function testSetIdLength()
    {
        $oEncoder = $this->getProxyClass('oxseoencoder');
        $oEncoder->setIdLength(999);

        $this->assertEquals(999, $oEncoder->getNonPublicVar('_iIdLength'));
    }

    /**
     * Testing dyn URL getter
     */
    public function testGetDynamicUrlWhenLoadedExisting()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sStdUrl = 'cl=stdcl';
        $sSeoUrl = 'en/dynseourl/';
        $sShopId = $this->getConfig()->getBaseShopId();
        $iLang = 1;
        $sObjectId = md5(strtolower($sShopId . $sStdUrl));
        $sIdent = md5(strtolower($sSeoUrl));;
        $sType = 'dynamic';

        $sQ = "insert into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype)
               values ('$sObjectId', '$sIdent', '$sShopId', '$iLang', '$sStdUrl', '$sSeoUrl', '$sType')";
        oxDb::getDb()->execute($sQ);

        $oEncoder = oxNew('oxSeoEncoder');

        $sUrl = $this->getConfig()->getShopUrl() . $sSeoUrl;
        $sSeoUrl = $oEncoder->getDynamicUrl($sStdUrl, $sSeoUrl, $iLang);

        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testGetDynamicUrlWhenLoadedExistingButDiffersFromGiven()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sStdUrl = 'cl=stdcl';
        $sSeoUrl = 'dynseourl/';

        $sShopId = $this->getConfig()->getBaseShopId();
        $iLang = 0;
        $sObjectId = md5(strtolower($sShopId . $sStdUrl));
        $sIdent = md5(strtolower('' . $sSeoUrl));
        $sType = 'dynamic';

        $sQ = "insert into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired)
               values ('$sObjectId', '$sIdent', '$sShopId', '$iLang', '$sStdUrl', '$sSeoUrl', '$sType', 1)";
        oxDb::getDb()->execute($sQ);

        $oEncoder = oxNew('oxSeoEncoder');

        $sUrl = $this->getConfig()->getShopUrl() . $sSeoUrl . 'addon/';
        $sSeoUrl = $oEncoder->getDynamicUrl($sStdUrl, $sSeoUrl . 'addon/', $iLang);

        $this->assertEquals($sUrl, $sSeoUrl);

        // checking if entry is moved to distory table
        $this->assertEquals("1", oxDb::getDb()->getOne("select 1 from oxseohistory where oxobjectid = '$sObjectId' "));
    }

    public function testGetDynamicUriExistingButDoesNotMatchPassed()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $iLang = 1;
        $sStdUrl = 'cl=stdcl';
        $sSeoUrl = 'en/dynseourl/';
        $oEncoder = oxNew('oxSeoEncoder');

        $sUrl = $this->getConfig()->getShopUrl() . $sSeoUrl . 'addon/';
        $sSeoUrl = $oEncoder->getDynamicUrl($sStdUrl, $sSeoUrl . 'addon/', $iLang);

        $this->assertEquals($sUrl, $sSeoUrl);
    }

    // now simply checking code call seq.
    public function testGetDynamicUriExistingButPAssingNewSeoUrlCallSeq()
    {
        $sStdUrl = 'stdulr';
        $sSeoUrl = 'en/seourl';
        $iLang = 1;
        $iShopId = $this->getConfig()->getBaseShopId();
        $sObjectId = md5(strtolower($iShopId . $sStdUrl));

        $oEncoder = $this->getMock('oxseoencoder', array('_trimUrl', 'getDynamicObjectId', '_prepareUri', '_loadFromDb', '_copyToHistory', '_processSeoUrl', '_saveToDb'));
        $oEncoder->expects($this->once())->method('_trimUrl')->with($this->equalTo($sStdUrl))->will($this->returnValue($sStdUrl));
        $oEncoder->expects($this->once())->method('getDynamicObjectId')->with($this->equalTo($iShopId), $this->equalTo($sStdUrl))->will($this->returnValue($sObjectId));
        $oEncoder->expects($this->once())->method('_prepareUri')->with($this->equalTo($sSeoUrl))->will($this->returnValue($sSeoUrl));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('dynamic'), $this->equalTo($sObjectId), $this->equalTo($iLang))->will($this->returnValue('oldseourl'));
        $oEncoder->expects($this->once())->method('_copyToHistory')->with($this->equalTo($sObjectId), $this->equalTo($iShopId), $this->equalTo($iLang))->will($this->returnValue('dynamic'));
        $oEncoder->expects($this->once())->method('_processSeoUrl')->with($this->equalTo($sSeoUrl), $this->equalTo($sObjectId), $this->equalTo($iLang))->will($this->returnValue($sSeoUrl));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo('dynamic'), $this->equalTo($sObjectId), $this->equalTo($sStdUrl), $this->equalTo($sSeoUrl), $this->equalTo($iLang), $this->equalTo($iShopId));

        $this->assertEquals($sSeoUrl, $oEncoder->UNITgetDynamicUri($sStdUrl, $sSeoUrl, $iLang));
    }

    public function testGetDynamicUriExistingCallSeq()
    {
        $sStdUrl = 'stdulr';
        $sSeoUrl = 'en/seourl';
        $iLang = 1;
        $iShopId = $this->getConfig()->getBaseShopId();
        $sObjectId = md5(strtolower($iShopId . $sStdUrl));

        $oEncoder = $this->getMock('oxseoencoder', array('_trimUrl', '_prepareUri', '_loadFromDb', '_copyToHistory', '_getUniqueSeoUrl', '_saveToDb'));
        $oEncoder->expects($this->atLeastOnce())->method('_trimUrl')->with($this->equalTo($sStdUrl))->will($this->returnValue($sStdUrl));
        $oEncoder->expects($this->once())->method('_prepareUri')->with($this->equalTo($sSeoUrl))->will($this->returnValue($sSeoUrl));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('dynamic'), $this->equalTo($sObjectId), $this->equalTo($iLang))->will($this->returnValue($sSeoUrl));
        $oEncoder->expects($this->never())->method('_copyToHistory');
        $oEncoder->expects($this->never())->method('_processSeoUrl');
        $oEncoder->expects($this->never())->method('_saveToDb');

        $this->assertEquals($sSeoUrl, $oEncoder->UNITgetDynamicUri($sStdUrl, $sSeoUrl, $iLang));
    }

    //
    // Static url getter test (mostly used in smarty plugin oxgetseourl)
    //
    public function testGetStaticUrl()
    {
        $oEncoder = $this->getMock('oxSeoEncoder', array('_loadFromDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('static'), $this->equalTo(md5('1xxx')), $this->equalTo(1));
        $oEncoder->getStaticUrl('xxx', 1, 1);
        // default params:
        $shop = ShopIdCalculator::BASE_SHOP_ID;
        $oEncoder = $this->getMock('oxSeoEncoder', array('_getStaticUri', '_getFullUrl'));
        $oEncoder->expects($this->once())->method('_getStaticUri')->with($this->equalTo('xxx'), $this->equalTo($shop), $this->equalTo(oxRegistry::getLang()->getEditLanguage()))->will($this->returnValue('seourl'));
        $oEncoder->expects($this->once())->method('_getFullUrl')->with($this->equalTo('seourl'))->will($this->returnValue('fullseourl'));
        $this->assertEquals('fullseourl', $oEncoder->getStaticUrl('xxx'));
    }

    //
    // Static url getter calls _getFullUrl with lang param
    //
    public function testGetStaticUrlCallsGetFullUrlWithLangParam()
    {
        $oEncoder = $this->getMock('oxSeoEncoder', array('_getStaticUri', '_getFullUrl'));
        $oEncoder->expects($this->any())->method('_getStaticUri')->will($this->returnValue('seourl'));
        $oEncoder->expects($this->any())->method('_getFullUrl')->with($this->equalTo('seourl'), $this->equalTo(1));
        $oEncoder->getStaticUrl('xxx', 1);
    }

    public function testAddSeoEntry()
    {
        $sObjectId = 'xxx';
        $iShopId = 'yyy';
        $iLang = '1';
        $sType = 'ggg';

        $sStdUrl = 'stdurl';
        $sSeoUrl = 'seourl';
        $blFixed = '1';
        $sKeywords = 'keyword1, keyword2, keyword3';
        $sDescription = 'superb seo stuff!';
        $sIdent = md5(strtolower($sSeoUrl . "u"));

        $sQ = "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed )
               values ( '$sObjectId', '{$sIdent}', '{$iShopId}', {$iLang}, '{$sStdUrl}', '{$sSeoUrl}', '{$sType}', '{$blFixed}' ) ";
        oxDb::getDb()->Execute($sQ);

        $sQ = "insert into oxobject2seodata ( oxobjectid, oxshopid, oxlang, oxkeywords, oxdescription )
               values ( '$sObjectId', '{$iShopId}', {$iLang}, '{$sKeywords}', '{$sDescription}' ) ";
        oxDb::getDb()->Execute($sQ);

        $oEncoder = $this->getMock('oxSeoEncoder', array('_saveToDb', '_processSeoUrl', '_trimUrl'));
        $oEncoder->expects($this->exactly(1))->method('_saveToDb')->with(
            $this->equalTo('ggg'),
            $this->equalTo('xxx'),
            $this->equalTo('stdurl'),
            $this->equalTo('seourlu'),
            $this->equalTo('1'),
            $this->equalTo('yyy'),
            $this->equalTo('1')
        )->will($this->returnValue(null));
        $oEncoder->expects($this->once())->method('_processSeoUrl')->with($this->equalTo($sSeoUrl . "t"))->will($this->returnValue($sSeoUrl . "u"));
        $oEncoder->expects($this->once())->method('_trimUrl')->with($this->equalTo($sSeoUrl))->will($this->returnValue($sSeoUrl . "t"));

        $oEncoder->addSeoEntry($sObjectId, $iShopId, $iLang, $sStdUrl, $sSeoUrl, $sType, $blFixed, $sKeywords, $sDescription);
    }

    public function testDeleteSeoEntry()
    {
        $oEncoder = oxNew('oxSeoEncoder');

        $dbMock = $this->getDbObjectMock();
        $dbMock->expects($this->any())->method('execute')->will($this->returnCallback(array($this, 'fillDbQueryBuffer')));
        $this->setProtectedClassProperty(Database::getInstance(), 'db' , $dbMock);

        $sObjectId = 'xxx';
        $iShopId = 'yyy';
        $iLang = 'zzz';
        $sType = 'ggg';

        $oEncoder->deleteSeoEntry($sObjectId, $iShopId, $iLang, $sType);

        $sQ = "delete from oxseo where oxobjectid = '{$sObjectId}' and oxshopid = '{$iShopId}' and oxlang = '{$iLang}' and oxtype = '{$sType}' ";

        $this->assertEquals($sQ, $this->dbQueryBuffer[0]);
    }

    //
    // Use case:
    // article title was changed, seo url must regenerate
    //
    public function testEncodeStaticUrlsSimulatingNoValidInput()
    {
        $aStaticUrl = array('oxseo__oxseourl'   => array('de/de', 'eng/eng'),
                            'oxseo__oxstdurl'   => 'xxx',
                            'oxseo__oxobjectid' => '-1'
        );

        $oEncoder = $this->getMock('oxSeoEncoder', array('_processSeoUrl', '_trimUrl'));
        $oEncoder->expects($this->exactly(2))->method('_processSeoUrl')->will($this->returnValue(null));
        $oEncoder->expects($this->exactly(4))->method('_trimUrl')->will($this->returnValue('notnull'));

        $this->aSQL = array();
        $this->aRET = array();

        $oDb = modDB::getInstance();
        $oDb->addClassFunction('execute', array($this, '__SaveToDbCreatesGoodMd5Callback'));

        $oEncoder->encodeStaticUrls($aStaticUrl, 1, 0);

        $this->assertEquals(0, count($this->aSQL));
    }

    public function testEncodeStaticUrlsOnlyDeletingOldRecords()
    {
        $aStaticUrl = array('oxseo__oxseourl'   => array('de/de', 'eng/eng', 'be/be'),
                            'oxseo__oxstdurl'   => 'xxx',
                            'oxseo__oxobjectid' => 'yyy'
        );

        $oEncoder = $this->getMock('oxSeoEncoder', array('_getUniqueSeoUrl', '_trimUrl'));
        $oEncoder->expects($this->exactly(3))->method('_getUniqueSeoUrl')->will($this->returnValue(0));
        $oEncoder->expects($this->atLeastOnce())->method('_trimUrl')->will($this->returnValue('xxx'));

        $dbMock = $this->getDbObjectMock();
        $dbMock->expects($this->any())->method('execute')->will($this->returnCallback(array($this, 'fillDbQueryBuffer')));
        $this->setProtectedClassProperty(Database::getInstance(), 'db' , $dbMock);

        $oEncoder->encodeStaticUrls($aStaticUrl, 1, 0);

        $this->assertEquals(4, count($this->dbQueryBuffer));
        $this->assertEquals("delete from oxseo where oxobjectid in ( 'yyy', '" . md5(strtolower('1' . 'xxx')) . "' )", $this->dbQueryBuffer[3]);
    }

    public function testEncodeStaticUrlsInsertingNewRecords()
    {
        $aStaticUrl = array('oxseo__oxseourl'   => array('de/de', 'eng/eng'),
                            'oxseo__oxstdurl'   => 'xxx',
                            'oxseo__oxobjectid' => '-1'
        );

        $oEncoder = $this->getMock('oxSeoEncoder', array('_getUniqueSeoUrl', '_trimUrl'));
        $oEncoder->expects($this->exactly(2))->method('_getUniqueSeoUrl')->will($this->returnValue('yyy'));
        $oEncoder->expects($this->atLeastOnce())->method('_trimUrl')->will($this->returnValue('yyy'));

        $dbMock = $this->getDbObjectMock();
        $dbMock->expects($this->any())->method('execute')->will($this->returnCallback(array($this, 'fillDbQueryBuffer')));
        $this->setProtectedClassProperty(Database::getInstance(), 'db' , $dbMock);

        $oEncoder->encodeStaticUrls($aStaticUrl, 1, 0);

        $this->assertEquals(1, count($this->dbQueryBuffer));
        $sQ = "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype ) values ";
        $sQ .= "( '" . md5('1yyy') . "', '" . md5('yyy') . "', '1', '0', 'yyy', 'yyy', 'static' ), ";
        $sQ .= "( '" . md5('1yyy') . "', '" . md5('yyy') . "', '1', '1', 'yyy', 'yyy', 'static' ) ";

        $this->assertEquals($sQ, $this->dbQueryBuffer[0]);
    }

    public function testCopyStaticUrlsForBaseShop()
    {
        // checking if new records are copied
        $iPreCnt = oxDb::getDb()->getOne('select count(oxobjectid) from oxseo where oxshopid = "1" ');

        $oEncoder = oxNew('oxSeoEncoder');
        $oEncoder->copyStaticUrls($this->getConfig()->getBaseShopId());

        $this->assertEquals($iPreCnt, oxDb::getDb()->getOne('select count(oxobjectid) from oxseo where oxshopid = "1" '));
    }

    /**
     * Test case:
     * cookies are cleaned up, object seo urls are not written
     */
    public function testIfSeoUrlsAreFine()
    {
        // preparing environment
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
        oxTestModules::addFunction("oxutils", "isSearchEngine", "{return false;}");
        $this->getConfig()->setConfigParam('blSessionUseCookies', false);

        // cleaning up
        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');

        $oArticle = oxNew('oxArticle');
        $articleId = $this->getTestConfig()->getShopEdition() == 'EE' ? '1889' : '1126';
        $oArticle->load($articleId);
        $oArticle->getLink();

        $oCat = $oArticle->getCategory();

        $this->assertEquals('index.php?cl=details&amp;anid=' . $oArticle->getId() . '&amp;cnid=' . ($oCat ? $oCat->getId() : ''), oxDb::getDb()->getOne('select oxstdurl from oxseo where oxobjectid = "' . $oArticle->getId() . '"'));
    }


    //
    // Test case:
    // updating static seo url and changing its seo and static urls
    //
    public function testStaticAndSeoUrlChange()
    {
        $oDB = oxDb::getDb();

        $iShopId = 1;
        $iLang = 1;
        $sStdUrl = 'index.php?cl=info&amp;tpl=test_info.tpl&amp;someone=someone';
        $sSeoUrl = 'en/customer-information/something/';

        $sObjectId = md5(strtolower($iShopId . $sStdUrl));
        $sIdent = md5(strtolower($sSeoUrl));

        // inserting test SEO url
        $sQ = "INSERT INTO `oxseo` (`OXOBJECTID`, `OXIDENT`, `OXSHOPID`, `OXLANG`, `OXSTDURL`, `OXSEOURL`, `OXTYPE`, `oxexpired`)
               VALUES ('{$sObjectId}', '{$sIdent}', {$iShopId}, {$iLang}, '{$sStdUrl}', '{$sSeoUrl}', 'static', '1') ";
        $oDB->Execute($sQ);

        $aStaticUrl = array('oxseo__oxstdurl'   => $sStdUrl . '&amp;something=something',
                            'oxseo__oxobjectid' => $sObjectId,
                            'oxseo__oxseourl'   => array(1 => $sSeoUrl . 'someparam/')
        );

        $oEncoder = oxNew('oxSeoEncoder');
        $oEncoder->encodeStaticUrls($aStaticUrl, $iShopId, 1);

        // checkign if old one is gone
        $this->assertFalse($oDB->getOne("select 1 from oxseo where oxobjectid = '$sObjectId' "));

        // checking is new entry is written - new ident and new objectid
        $this->assertTrue('1' == $oDB->getOne("select 1 from oxseo where oxident = '" . md5(strtolower($sSeoUrl . 'someparam/')) . "' and oxobjectid = '" . md5(strtolower($iShopId . $sStdUrl . '&amp;something=something')) . "' "));

        // checking if seo history contains right entry - new objectid + old ident
        $sIdent = md5(strtolower($sSeoUrl));
        $this->assertTrue('1' == $oDB->getOne("select 1 from oxseohistory where oxident = '$sIdent' and oxobjectid = '" . md5(strtolower($iShopId . $sStdUrl . '&amp;something=something')) . "' "));
    }

    public function testSaveToDbMarkedAsExpiredButUrlsStillTheSame()
    {
        $oDB = oxDb::getDb();
        $oDB->Execute(
            'insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired )
                                              values ( "999", "' . md5('seourl') . '", 999, 999, "stdurl", "seourl", "oxarticle", "1" )'
        );

        $this->assertTrue('1' == $oDB->getOne('select oxexpired from oxseo where oxobjectid = "999"'));

        $oEncoder = oxNew('oxSeoEncoder');
        $oEncoder->UNITsaveToDb("oxarticle", '999', 'stdurt', 'seourl', '999', '999');

        $this->assertTrue('0' == $oDB->getOne('select oxexpired from oxseo where oxobjectid = "999"'));
    }

    /*
     * Testing if updating expired seo links for same article which is in more
     * than one root category updates record with correct root catgory id
     * (M:1187)
     */
    public function testSaveToDb_forExpiredLinksAndRootCateogoriesIds()
    {
        $iShopId = $this->getConfig()->getBaseShopId();
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        // seo urls
        $sObjectId = '_testId1';

        $sStdUrl1 = 'index.php?test_1';
        $sSeoUrl1 = 'seo/testSeoUrl_1';
        $sRootId1 = '_testRootId1';

        // inserting seo data
        $oDb->Execute(
            "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxexpired, oxtype, oxparams )
                                   values ( '{$sObjectId}', '" . md5(strtolower("de/$sSeoUrl1")) . "', '{$iShopId}', '0', '{$sStdUrl1}', '{$sSeoUrl1}', '1', 'oxarticle', '{$sRootId1}' )"
        );

        // seo urls
        $sStdUrl2 = 'index.php?test_2';
        $sSeoUrl2 = 'seo/testSeoUrl_2';
        $sRootId2 = '_testRootId2';

        // inserting seo data
        $oDb->Execute(
            "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxexpired, oxtype, oxparams )
                                   values ( '{$sObjectId}', '" . md5(strtolower("de/$sSeoUrl2")) . "', '{$iShopId}', '0', '{$sStdUrl2}', '{$sSeoUrl2}', '1', 'oxarticle', '{$sRootId2}' )"
        );

        // seo urls
        $sStdUrl3 = 'index.php?test_3';
        $sSeoUrl3 = 'seo/testSeoUrl_3';
        $sRootId3 = '_testRootId3';

        // inserting seo data
        $oDb->Execute(
            "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxexpired, oxtype, oxparams )
                                   values ( '{$sObjectId}', '" . md5(strtolower("de/$sSeoUrl3")) . "', '{$iShopId}', '0', '{$sStdUrl3}', '{$sSeoUrl3}', '1', 'oxarticle', '{$sRootId3}' )"
        );

        $oEncoder = oxNew('oxSeoEncoder');;
        $oEncoder->UNITsaveToDb('oxarticle', $sObjectId, $sStdUrl3, $sSeoUrl3, 0, $iShopId, null, $sRootId3);

        $sSql = " select oxobjectid, oxparams, oxexpired from oxseo where oxobjectid= '{$sObjectId}' and oxexpired = '0' ";
        $aRows = $oDb->getAll($sSql);

        $this->assertEquals(1, count($aRows));
        $this->assertEquals($sObjectId, $aRows[0]['oxobjectid']);
        $this->assertEquals($sRootId3, $aRows[0]['oxparams']);
    }


    public function testSaveToDbMovingToHistory()
    {
        $oDB = oxDb::getDb();
        $oDB->Execute(
            'insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired )
                                              values ( "999", "' . md5('seourl') . '", 999, 999, "stdurl", "seourl", "oxarticle", "1" )'
        );

        $oEncoder = oxNew('oxSeoEncoder');
        $oEncoder->UNITsaveToDb("oxarticle", '999', 'newstdurt', 'seourl', '999', '999');

        $this->assertTrue('newstdurt' == $oDB->getOne('select oxstdurl from oxseo where oxobjectid = "999"'));
        $this->assertTrue('1' == $oDB->getOne('select 1 from oxseohistory where oxobjectid = "999"'));
    }

    public function testcopyToHistory()
    {
        $oDB = oxDb::getDb();
        $oDB->Execute(
            'insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired )
                                              values ( "999", "999", 999, 999, "stdurl", "seourl", "oxarticle", "1" )'
        );

        $oEncoder = oxNew('oxSeoEncoder');
        $oEncoder->UNITcopyToHistory('999', '999', '999');

        // testing if record is stored to db
        $this->assertTrue('1' == $oDB->getOne('select 1 from oxseohistory where oxobjectid = "999" and oxident = MD5( LOWER( "seourl" ) ) and oxshopid = "999" and oxlang = "999" '));
    }

    public function testMarkAsExpired()
    {
        $oDB = oxDb::getDb();
        $oDB->Execute(
            'insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype )
                                              values ( "999", "999", 999, 999, "stdurl", "seourl", "oxarticle" )'
        );

        $this->assertFalse($oDB->getOne('select 1 from oxseo where oxexpired = "1" and oxobjectid = "999" and oxident = "999" and oxshopid = "999" and oxlang = "999" '));

        $oEncoder = oxNew('oxSeoEncoder');
        $oEncoder->markAsExpired('999');

        $this->assertTrue('1' == $oDB->getOne('select 1 from oxseo where oxexpired = "1" and oxobjectid = "999" and oxident = "999" and oxshopid = "999" and oxlang = "999" '));
    }

    //
    // Set SEO separator if it is not set in config
    //
    public function testSetSeparator()
    {
        $oEncoder = new modSeoEncoder();
        $oEncoder->setSeparator(null);

        $this->assertEquals("-", $oEncoder->getSeparator());
    }

    //
    // Set SEO separator from config
    //
    public function testSetSeparatorFromConfig()
    {
        $oEncoder = new modSeoEncoder();
        $oEncoder->setSeparator("\$");

        $this->assertEquals("\$", $oEncoder->getSeparator());
    }

    public function testPrepareTitle()
    {
        $oEncoder = new modSeoEncoder();
        $sTitleIn = '///AA keyword1 keyword2 ä  ö ü Ü Ä Ö ß' . str_repeat(' a', 300);
        $oEncoder->setSeparator();
        $sTitleOut = $oEncoder->p_prepareTitle($sTitleIn);

        $this->assertEquals('AA-keyword1-keyword2-ae-oe-ue-Ue-Ae-Oe-ss' . str_repeat('-a', 107), $sTitleOut);

        $sTitleOut = $oEncoder->p_prepareTitle('');
        $this->assertEquals('oxid', $sTitleOut);
    }

    public function testSetPrefix()
    {
        oxTestModules::addFunction('oxseoencoder', 'getPrefix', '{return oxseoencoder::$_sPrefix;}');
        $oEncoder = oxNew('oxseoencoder');

        $oEncoder->setPrefix('test2');
        $this->assertEquals('test2', $oEncoder->getPrefix());

        $oEncoder->setPrefix('');
        $this->assertEquals('oxid', $oEncoder->getPrefix());
    }

    public function testIsFixed()
    {
        $oDb = oxDb::getDb();
        $e = null;
        try {
            // starting test
            $oDb->Execute('replace into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired, oxfixed) values ("test", "test", 1, 0, "stdurl", "seourl", "static", 1, 0)');
            $oEncoder = oxNew('oxSeoEncoder');
            $this->assertFalse($oEncoder->UNITisFixed('static', 'test', 0, 1));
            $oDb->Execute('replace into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired, oxfixed) values ("test", "test", 1, 0, "stdurl", "seourl", "static", 1, 1)');

            oxSeoEncoderHelper::cleanup();

            $oEncoder = oxNew('oxSeoEncoder');
            $this->assertTrue($oEncoder->UNITisFixed('static', 'test', 0, 1));

            oxSeoEncoderHelper::cleanup();

            $oEncoder = oxNew('oxSeoEncoder');
            $this->assertTrue($oEncoder->UNITisFixed('static', 'test', 0, 1, 0, 0));
            $oDb->Execute('delete from oxseo where oxident="test"');

            oxSeoEncoderHelper::cleanup();

            $oEncoder = oxNew('oxSeoEncoder');
            $this->assertFalse($oEncoder->UNITisFixed('static', 'test', 0, 1));
            // test finished
        } catch (Exception $e) {
            // will be thrown again soon
        }
        $oDb->Execute('delete from oxseo where oxident="test"');
        if ($e) {
            throw $e;
        }
    }

    public function testLoadFromDbStaticUrl()
    {
        $oEncoder = $this->getMock('oxSeoEncoder', array("_loadFromCache", "_saveInCache"));
        $oEncoder->expects($this->atLeastOnce())->method('_loadFromCache')->will($this->returnValue(false));
        $oEncoder->expects($this->atLeastOnce())->method('_saveInCache');

        $oDb = oxDb::getDb();
        $e = null;
        try {
            $oDb->Execute('replace into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired) values ("test", "test", 1, 0, "stdurl", "seourl", "static", 1)');
            // starting test
            $this->assertEquals('1', $oDb->getOne('select oxexpired from oxseo where oxobjectid="test"'));
            $this->assertEquals('seourl', $oEncoder->UNITloadFromDb('static', 'test', 0, 1));
            $this->assertEquals('0', $oDb->getOne('select oxexpired from oxseo where oxobjectid="test"'));
            // test finished
        } catch (Exception $e) {
            // will be thrown again soon
        }
        $oDb->Execute('delete from oxseo where oxident="test"');
        if ($e) {
            throw $e;
        }
    }

    public function testLoadFromDb111()
    {
        $oEncoder = $this->getMock('oxSeoEncoder', array("_loadFromCache", '_saveInCache'));
        $oEncoder->expects($this->atLeastOnce())->method('_loadFromCache')->will($this->returnValue(false));
        $oEncoder->expects($this->atLeastOnce())->method('_saveInCache');

        $oDb = oxDb::getDb();
        $e = null;
        try {
            $oDb->Execute('replace into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype) values ("test", "test", 1, 0, "stdurl", "seourl", "oxarticle")');
            // starting test
            $this->assertEquals('seourl', $oEncoder->UNITloadFromDb('oxarticle', 'test', 0, 1));
            $this->assertSame(false, $oEncoder->UNITloadFromDb('oxarticle', 'test', 0, 2));
            // test finished
        } catch (Exception $e) {
            // will be thrown again soon
        }
        $oDb->Execute('delete from oxseo where oxident="test"');
        if ($e) {
            throw $e;
        }
    }

    // expired seo entry
    public function testLoadFromDbExpiredEntry()
    {
        oxDb::getDb()->Execute('replace into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired ) values ("test", "test", 1, 0, "stdurl", "seourl", "oxarticle", "1" )');

        $oEncoder = $this->getMock('oxSeoEncoder', array("_loadFromCache", '_saveInCache'));
        $oEncoder->expects($this->atLeastOnce())->method('_loadFromCache')->will($this->returnValue(false));
        $oEncoder->expects($this->atLeastOnce())->method('_saveInCache');

        $this->assertFalse($oEncoder->UNITloadFromDb('oxarticle', 'test', 0, 1));
    }

    // expired seo entry, but fixed and will still be used
    public function testLoadFromDbExpiredButFixedEntry()
    {
        oxDb::getDb()->Execute('replace into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired, oxfixed) values ("test", "test", 1, 0, "stdurl", "seourl", "oxarticle", "1", "1")');

        $oEncoder = $this->getMock('oxSeoEncoder', array("_loadFromCache", '_saveInCache'));
        $oEncoder->expects($this->atLeastOnce())->method('_loadFromCache')->will($this->returnValue(false));
        $oEncoder->expects($this->atLeastOnce())->method('_saveInCache');

        $this->assertEquals('seourl', $oEncoder->UNITloadFromDb('oxarticle', 'test', 0, 1));
    }

    public function testloadFromDbWithStrictParamsCheck()
    {
        oxDb::getDb()->Execute('replace into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired, oxfixed, oxparams) values ("test", "test1", 1, 0, "stdurl", "seourl2", "oxarticle", "1", "1", "param1")');
        oxDb::getDb()->Execute('replace into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired, oxfixed, oxparams) values ("test", "test2", 1, 0, "stdurl", "seourl1", "oxarticle", "1", "1", "param2")');

        $oEncoder = $this->getMock('oxSeoEncoder', array("_loadFromCache", '_saveInCache'));
        $oEncoder->expects($this->atLeastOnce())->method('_loadFromCache')->will($this->returnValue(false));
        $oEncoder->expects($this->atLeastOnce())->method('_saveInCache');

        $this->assertEquals('seourl1', $oEncoder->UNITloadFromDb('oxarticle', 'test', 0, 1, 'param2'));
    }

    public function testloadFromDbNoStrictParamsCheck()
    {
        oxDb::getDb()->Execute('replace into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired, oxfixed, oxparams) values ("test", "test1", 1, 0, "stdurl", "seourl", "oxarticle", "1", "1", "param2")');
        oxDb::getDb()->Execute('replace into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxexpired, oxfixed, oxparams) values ("test", "test2", 1, 0, "stdurl", "seourl", "oxarticle", "1", "1", "param1")');

        $oEncoder = $this->getMock('oxSeoEncoder', array("_loadFromCache", '_saveInCache'));
        $oEncoder->expects($this->atLeastOnce())->method('_loadFromCache')->will($this->returnValue(false));
        $oEncoder->expects($this->atLeastOnce())->method('_saveInCache');

        $this->assertEquals('seourl', $oEncoder->UNITloadFromDb('oxarticle', 'test', 0, 1, 'param1', false));
    }

    public function testSaveToDb()
    {
        $oEncoder = $this->getProxyClass('oxSeoEncoder');
        $oEncoder->UNITsaveToDb("static", 'test', 'http://std', 'http://seo', 0, 0);
        $oDb = oxDb::getDb();
        $oDb->Execute('delete from oxseo where oxobjectid="test"');
        $this->assertEquals(1, $oDb->affectedRows());
    }

    public function testSaveToDbKeyCollision()
    {
        $oEncoder = $this->getProxyClass('oxSeoEncoder');

        //add entry
        $oEncoder->UNITsaveToDb("static", 'test', 'http://std', 'http://seo', 0, 0);
        //override entry by using the same url
        $oEncoder->UNITsaveToDb("static", 'testOtherId', 'http://std', 'http://seo', 0, 0);

        $oDb = oxDb::getDb();

        $oDb->Execute('delete from oxseo where oxobjectid="test"');
        //assert 0 rows to be found with oxobjectid = test because the entry was overridden
        //by an other object with the same url
        $this->assertEquals(0, $oDb->affectedRows());

        $oDb->Execute('delete from oxseo where oxobjectid="testOtherId"');
        //assert 1 rows to be found with oxobjectid = testOtherId because the entry was saved
        $this->assertEquals(1, $oDb->affectedRows());
    }

    public function testTrimUrl()
    {
        $sBaseUrl = $this->getConfig()->getConfigParam("sShopURL");
        $sSslUrl = str_replace("http:", "https:", $sBaseUrl);

        $oConfig = $this->getMock("oxStdClas", array("getShopURL", "getSslShopUrl", "getConfigParam"));
        $oConfig->expects($this->any())->method('getShopURL')->will($this->returnValue($sBaseUrl));
        $oConfig->expects($this->any())->method('getSslShopUrl')->will($this->returnValue($sSslUrl));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(0));

        $oE = $this->getMock("oxseoencoder", array("getConfig"), array(), '', false);
        $oE->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals('aa?a=2', $oE->UNITtrimUrl($sBaseUrl . 'aa?sid=as23.&a=2', 0));
        $this->assertEquals('aa', $oE->UNITtrimUrl($sBaseUrl . 'aa?sid=as23.', 1));
        $this->assertEquals('aa', $oE->UNITtrimUrl($sBaseUrl . 'aa?sid=as23.&', 1));

        $this->assertEquals('aa?a=2', $oE->UNITtrimUrl($sBaseUrl . 'aa?force_sid=as23.&a=2', 0));
        $this->assertEquals('aa', $oE->UNITtrimUrl($sBaseUrl . 'aa?force_sid=as23.', 1));
        $this->assertEquals('aa', $oE->UNITtrimUrl($sBaseUrl . 'aa?force_sid=as23.&', 1));
        $this->assertEquals('aa?force_something=1', $oE->UNITtrimUrl($sBaseUrl . 'aa?force_something=1&sid=as23.&', 1));
        $this->assertEquals('index.php?cl=details&amp;anid=762b1c44c95cd81dd1396b089982a568', $oE->UNITtrimUrl($sBaseUrl . 'index.php?force_sid=as23&cl=details&amp;anid=762b1c44c95cd81dd1396b089982a568', 1));
        //#M1423: Problems with article seo url, if admin is ssl
        $this->assertEquals('index.php?cl=details&amp;anid=762b1c44c95cd81dd1396b089982a568', $oE->UNITtrimUrl($sBaseUrl . 'index.php?force_admin_sid=as23&cl=details&amp;anid=762b1c44c95cd81dd1396b089982a568', 1));

        $this->assertEquals('aa?a=2', $oE->UNITtrimUrl($sSslUrl . 'aa?sid=as23.&a=2', 0));
        $this->assertEquals('aa', $oE->UNITtrimUrl($sSslUrl . 'aa?sid=as23.', 1));
        $this->assertEquals('aa', $oE->UNITtrimUrl($sSslUrl . 'aa?sid=as23.&', 1));

        $this->assertEquals('aa?a=2', $oE->UNITtrimUrl($sSslUrl . 'aa?cur=5&a=2', 0));

        // checking length
        $sUrl = 'aa?a=' . str_repeat("1", 3000);
        $this->assertEquals(substr($sUrl, 0, 2048), $oE->UNITtrimUrl($sUrl, 0));
    }

    public function testSaveToDbCreatesGoodMd5()
    {
        oxTestModules::publicize('oxSeoEncoder', '_saveToDb');
        oxTestModules::addFunction('oxSeoEncoder', '_trimUrl', '{return ltrim($aA[0], "u");}');
        $oE = oxNew('oxSeoEncoder');
        $this->aSQL = array();
        $oDb = modDB::getInstance();
        $oDb->addClassFunction('Execute', array($this, '__SaveToDbCreatesGoodMd5Callback'));

        try {
            $goodMd5 = '241b4e9d8fe73920dcd544dbabfa0cb1';
            $oE->p_saveToDb('test', 'test', 'stdurl', 'uWohnen/Lampen/', 0, 0);

            $sQ = "replace into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxkeywords, oxdescription, oxparams) values ( 'test', '$goodMd5', '0', 0, 'stdurl', 'Wohnen/Lampen/', 'test', '0', '0', '', '', \"\" )";
            $sQ = preg_replace('/\W/', '', $sQ);
            $this->aSQL[1] = preg_replace('/\W/', '', $sQ);

            $this->assertEquals($sQ, $this->aSQL[1]);
        } catch (Exception $e) {
        }

        $oDb->cleanup();

        if ($e) {
            throw $e;
        }
    }

    public function testGetUniqueSeoUrl()
    {
        $iShopId = $this->getConfig()->getBaseShopId();
        $oDb = oxDb::getDb();
        $oEncoder = oxNew('oxseoencoder');

        $this->assertEquals('uaaA/', $oEncoder->UNITgetUniqueSeoUrl('uaaA'));
        $oDb->execute("insert into oxseo (`oxobjectid`, `oxident`, `oxshopid`, `oxlang`, `oxtype` ) values( '" . md5(uniqid(rand(), true)) . "', '" . $oEncoder->UNITgetSeoIdent($oEncoder->UNITtrimUrl('uaaa/')) . "', '$iShopId', 0, 'oxcategory' )");

        $this->assertEquals('uaaa-oxid/', $oEncoder->UNITgetUniqueSeoUrl('uaaa'));
        $oDb->execute("insert into oxseo (`oxobjectid`, `oxident`, `oxshopid`, `oxlang`, `oxtype` ) values( '" . md5(uniqid(rand(), true)) . "', '" . $oEncoder->UNITgetSeoIdent($oEncoder->UNITtrimUrl('uaaa-oxid/')) . "', '$iShopId', 0, 'oxcategory' )");
        $this->assertEquals('uaaa-oxid-1/', $oEncoder->UNITgetUniqueSeoUrl('uaaa'));
        $oDb->execute("insert into oxseo (`oxobjectid`, `oxident`, `oxshopid`, `oxlang`, `oxtype` ) values( '" . md5(uniqid(rand(), true)) . "', '" . $oEncoder->UNITgetSeoIdent($oEncoder->UNITtrimUrl('uaaa-oxid-1/')) . "', '$iShopId', 0, 'oxcategory' )");
        $this->assertEquals('uaaa-oxid-2/', $oEncoder->UNITgetUniqueSeoUrl('uaaa'));

        $this->assertEquals('uaaa.html', $oEncoder->UNITgetUniqueSeoUrl('uaaa.html'));
        $oDb->execute("insert into oxseo (`oxobjectid`, `oxident`, `oxshopid`, `oxlang`, `oxtype` ) values( '" . md5(uniqid(rand(), true)) . "', '" . $oEncoder->UNITgetSeoIdent($oEncoder->UNITtrimUrl('uaaa.html')) . "', '$iShopId', 0, 'oxcategory' )");
        $this->assertEquals('uaaa-oxid.html', $oEncoder->UNITgetUniqueSeoUrl('uaaa.html'));

        $this->assertEquals('uaaa.htm', $oEncoder->UNITgetUniqueSeoUrl('uaaa.htm'));
        $oDb->execute("insert into oxseo (`oxobjectid`, `oxident`, `oxshopid`, `oxlang`, `oxtype` ) values( '" . md5(uniqid(rand(), true)) . "', '" . $oEncoder->UNITgetSeoIdent($oEncoder->UNITtrimUrl('uaaa.htm')) . "', '$iShopId', 0, 'oxcategory' )");
        $this->assertEquals('uaaa-oxid.htm', $oEncoder->UNITgetUniqueSeoUrl('uaaa.htm'));
    }

    public function testPrepareUriFiltersRootFilesAndReservedKeywords()
    {
        oxTestModules::publicize('oxSeoEncoder', '_prepareUri');
        oxTestModules::addFunction('oxSeoEncoder', '_trimUrl', '{return ltrim($aA[0], "u");}');

        $oDb = modDB::getInstance();
        $oDb->addClassFunction('GetOne', array($this, '__SaveToDbCreatesGoodMd5Callback'));

        $iShopId = $this->getConfig()->getBaseShopId();

        $oE = oxNew('oxseoencoder');
        $this->aSQL = array();
        $this->aRET = array(false);
        $this->assertEquals('admin-oxid/aa.html', $oE->p_prepareUri('admin/aa.html'), '');

        $oE = oxNew('oxseoencoder');
        $this->aSQL = array();
        $this->aRET = array(false);
        $this->assertEquals('index-php/aa/', $oE->p_prepareUri('index.php/aa'), '');

        $oE = oxNew('oxseoencoder');
        $this->aSQL = array();
        $this->aRET = array(false);
        $this->assertEquals('index-php/', $oE->p_prepareUri('index.php'), '');

        $oE = oxNew('oxseoencoder');
        $this->aSQL = array();
        $this->aRET = array(false);
        $this->assertEquals('/index-oxid/aa.html', $oE->p_prepareUri('/index/aa.html'), '');

        $oE = oxNew('oxseoencoder');
        $this->aSQL = array();
        $this->aRET = array(false);
        $this->assertEquals('/index-php/aa.html', $oE->p_prepareUri('/index.php/aa.html'), '');

        $oE = oxNew('oxseoencoder');
        $this->aSQL = array();
        $this->aRET = array(false);
        $this->assertEquals('/index-oxid/', $oE->p_prepareUri('/index/'), '');

        $oE = oxNew('oxseoencoder');
        $this->aSQL = array();
        $this->aRET = array(false);
        $this->assertEquals('/index-oxid/', $oE->p_prepareUri('/index'), '');

        $oE = oxNew('oxseoencoder');
        $this->aSQL = array();
        $this->aRET = array(false);
        $this->assertEquals('index-oxid/', $oE->p_prepareUri('--index'), '');

        $oE = oxNew('oxseoencoder');
        $oE->setSeparator('/');
        $this->aSQL = array();
        $this->aRET = array(false);
        $this->assertEquals('/index_oxid/aa.html', $oE->p_prepareUri('/index/aa.html'), '');

        $cl = oxTestModules::addFunction('oxSeoEncoder', 'clean_aReservedEntryKeys', '{oxSeoEncoder::$_aReservedEntryKeys = null;}');
        $oE = new $cl();
        $oE->clean_aReservedEntryKeys();
        $oE->setReservedWords(array('keyword1', 'keyword2'));
        $oE->setSeparator('+');
        $this->aSQL = array();
        $this->aRET = array(false);
        $this->assertEquals('/keyword1+oxid/keyword1+s+keyword2/aa+keyword1.html', $oE->p_prepareUri('/keyword1/keyword1-s.keyword2/aa-keyword1.html'), '');
        $this->assertEquals('/keyword2+oxid/', $oE->p_prepareUri('/keyword2'), '');
    }

    public function testGetMetaData()
    {
        $iShopId = $this->getConfig()->getBaseShopId();
        $oDb = oxDb::getDb();
        $oDb->execute("insert into oxobject2seodata (`oxobjectid`, `oxkeywords`, `oxshopid`, `oxlang`) values( 'xxx', 'yyy', '$iShopId', 0)");

        $oEncoder = oxNew('oxSeoEncoder');

        $this->assertEquals('yyy', $oEncoder->getMetaData('xxx', 'oxkeywords'));
    }

    public function testGetSeoIdent()
    {
        $oE = oxNew('oxSeoEncoder');

        $this->assertEquals(md5('aaa'), $oE->UNITgetSeoIdent('aAa', 0));
        $this->assertEquals(md5('a1aa'), $oE->UNITgetSeoIdent('a1Aa', 1));
        $this->assertEquals(md5(''), $oE->UNITgetSeoIdent('', 1));
    }


    public function testGetGetPageUriEntryExistsInDb()
    {
        $iLang = 1;
        $oObject = oxNew('oxI18n');
        $oObject->setLanguage($iLang);
        $oObject->setId('yyy');

        $iShopId = $this->getConfig()->getBaseShopId();
        $sParams = 'zzz';

        $sSeoUrl = 'seourl';
        $sStdUrl = 'stdurl';
        $sType = 'xxx';

        $oEncoder = $this->getMock('oxSeoEncoder', array('_loadFromDb', '_saveToDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')
            ->with(
                $this->equalTo($sType),
                $this->equalTo($oObject->getId()),
                $this->equalTo($iLang),
                $this->equalTo($iShopId),
                $this->equalTo($sParams)
            )
            ->will($this->returnValue($sSeoUrl));

        $oEncoder->expects($this->never())->method('_saveToDb');

        $this->assertEquals($sSeoUrl, $oEncoder->UNITgetPageUri($oObject, $sType, $sStdUrl, $sSeoUrl, $sParams));
    }

    public function testGetGetPageUriWithLangParam()
    {
        $iLang = 1;
        $oObject = oxNew('oxI18n');
        $oObject->setLanguage(0);
        $oObject->setId('yyy');

        $iShopId = $this->getConfig()->getBaseShopId();
        $sParams = 'zzz';

        $sSeoUrl = 'seourl';
        $sStdUrl = 'stdurl';
        $sType = 'xxx';

        $oEncoder = $this->getMock('oxSeoEncoder', array('_loadFromDb', '_saveToDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')
            ->with(
                $this->equalTo($sType),
                $this->equalTo($oObject->getId()),
                $this->equalTo($iLang),
                $this->equalTo($iShopId),
                $this->equalTo($sParams)
            )
            ->will($this->returnValue($sSeoUrl));

        $oEncoder->expects($this->never())->method('_saveToDb');

        $this->assertEquals($sSeoUrl, $oEncoder->UNITgetPageUri($oObject, $sType, $sStdUrl, $sSeoUrl, $sParams, $iLang));
    }

    public function testGetPageUriEntryDoesNotExistInDbAndWillBeCreated()
    {
        $iLang = 1;
        $oObject = oxNew('oxI18n');
        $oObject->setLanguage($iLang);
        $oObject->setId('yyy');

        $iShopId = $this->getConfig()->getBaseShopId();
        $sParams = 'zzz';

        $sSeoUrl = 'seourl';
        $sStdUrl = 'stdurl';
        $sType = 'xxx';

        $oDb = oxDb::getDb();

        $oEncoder = $this->getMock('oxSeoEncoder', array('_loadFromDb', '_processSeoUrl', '_saveToDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')
            ->with(
                $this->equalTo($sType),
                $this->equalTo($oObject->getId()),
                $this->equalTo($iLang),
                $this->equalTo($iShopId),
                $this->equalTo($sParams)
            )
            ->will($this->returnValue(false));

        $oEncoder->expects($this->once())->method('_processSeoUrl')
            ->with(
                $this->equalTo($sSeoUrl),
                $this->equalTo($oObject->getId()),
                $this->equalTo($iLang)
            )
            ->will($this->returnValue($sSeoUrl));

        $oEncoder->expects($this->once())->method('_saveToDb')
            ->with(
                $this->equalTo($sType),
                $this->equalTo($oObject->getId()),
                $this->equalTo($sStdUrl),
                $this->equalTo($sSeoUrl),
                $this->equalTo($iLang),
                $this->equalTo($iShopId),
                $this->equalTo(0),
                $this->equalTo($sParams)
            );

        $this->assertEquals($sSeoUrl, $oEncoder->UNITgetPageUri($oObject, $sType, $sStdUrl, $sSeoUrl, $sParams));
    }

    /**
     * Test for #0001641: Same name category and page
     *
     * @return null
     */
    public function testForCase0001641()
    {
        $categoryId = '30e44ab8593023055.23928895';

        $oParentCategory = oxNew('oxCategory');
        $oParentCategory->load($categoryId);

        // creating and assigning sub category named "2"
        $oCategory = oxNew('oxCategory');
        $oCategory->oxcategories__oxtitle = new oxField("2");
        $oCategory->oxcategories__oxparentid = new oxField($categoryId);
        $oCategory->oxcategories__oxactive = new oxField(1);
        $oCategory->oxcategories__oxshopid = new oxField($oParentCategory->oxcategories__oxshopid->value);
        $oCategory->save();

        // now fetching parent category page nr 2 and comparing to subcategory - they should not match
        $sParentUrl = oxRegistry::get("oxSeoEncoderCategory")->getCategoryPageUrl($oParentCategory, 1, $oParentCategory->getLanguage());
        $this->assertNotEquals($oCategory->getLink(), $sParentUrl);
    }

    public function testEncodeString()
    {
        $sString = '&quot;&lt;Flaschenöffner&#039;&amp;quot;';
        $sEncodedString = "\"<Flaschenoeffner'";
        $sPartEncodedString = '"<Flaschenöffner\'';

        $oEncoder = oxNew('oxSeoEncoder');
        $this->assertEquals($sEncodedString, $oEncoder->encodeString($sString));
        $this->assertEquals($sPartEncodedString, $oEncoder->encodeString($sString, false));
    }

    /**
     * Testing fetchSeoUrl() method. Bug #1640.
     *
     */
    public function testFetchSeoUrlMultishop()
    {
        oxDb::getDb()->execute("delete from oxseo where oxident = '_testIdent'");
        $sQ = "insert into oxseo (oxident, oxstdurl, oxseourl, oxshopid) values('_testIdent', 'index.php?cl=account', 'testSeoUrl', 5) ";
        oxDb::getDb()->execute($sQ);
        $oEncoder = oxNew('oxSeoEncoder');
        $sSeoUrl = $oEncoder->fetchSeoUrl('index.php?cl=account');
        $sExpUrl = 'mein-konto/';
        $this->assertEquals($sExpUrl, $sSeoUrl);
        oxDb::getDb()->execute("delete from oxseo where oxident = '_testIdent'");
    }

    /**
     * Test caseo for oxSeoEncoder::_getCacheKey()
     *
     * @return null
     */
    public function testGetCacheKey()
    {
        // admin
        $oEncoder = $this->getMock('oxSeoEncoder', array('isAdmin', 'getConfig'), array(), '', false);
        $oEncoder->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oEncoder->expects($this->never())->method('getConfig');
        $this->assertFalse($oEncoder->UNITgetCacheKey("any"));

        oxSeoEncoderHelper::cleanup();

        $sViewId = "viewId";
        $oView = $this->getMock('oxView', array('getViewId'));
        $oView->expects($this->once())->method('getViewId')->will($this->returnValue($sViewId));

        $oConfig = $this->getMock('oxConfig', array('getActiveView'));
        $oConfig->expects($this->once())->method('getActiveView')->will($this->returnValue($oView));

        // non admin
        $oEncoder = $this->getMock('oxSeoEncoder', array('isAdmin', 'getConfig'), array(), '', false);
        $oEncoder->expects($this->exactly(3))->method('isAdmin')->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertEquals(md5($sViewId) . "seo", $oEncoder->UNITgetCacheKey("oxarticle"));

        // + cache check
        $this->assertEquals(md5($sViewId) . "seo", $oEncoder->UNITgetCacheKey("oxarticle"));

        // #3381
        $this->assertEquals("any00seo", $oEncoder->UNITgetCacheKey("any", 0, 0, ''));
    }

    /**
     * Test case for oxSeoEncoder::_saveInCache() && ::_loadFromCache()
     *
     * @return null
     */
    public function testSaveInCacheAndLoadFromCache()
    {
        $sCache = "testCache";
        $sCacheKey = "sCacheKey";
        $sCacheIdent = "testCacheIdent";

        // no cache key - not saved to cache
        $oEncoder = $this->getMock('oxSeoEncoder', array('_getCacheKey'), array(), '', false);
        $oEncoder->expects($this->any())->method('_getCacheKey')->will($this->returnValue(false));

        $this->assertFalse($oEncoder->UNITsaveInCache($sCacheIdent, $sCache, "any"));
        $this->assertFalse($oEncoder->UNITloadFromCache($sCacheIdent, "any"));

        // cache key + saved to cache
        $oEncoder = $this->getMock('oxSeoEncoder', array('_getCacheKey'), array(), '', false);
        $oEncoder->expects($this->any())->method('_getCacheKey')->will($this->returnValue($sCacheKey));

        $this->assertTrue($oEncoder->UNITsaveInCache($sCacheIdent, $sCache, "any"));
        $this->assertEquals($sCache, $oEncoder->UNITloadFromCache($sCacheIdent, "any"));
    }

    /**
     * Test lower casing of urls with config param blSEOLowerCaseUrls
     *
     * @return null
     */
    public function testLowerCasingOfUrls()
    {
        $sSeoUrlBefore = 'Foo/Bar.html';
        $sSeoUrlAfter = 'foo/bar.html';

        $oEncoder = oxNew('oxSeoEncoder');

        $this->getConfig()->setConfigParam('blSEOLowerCaseUrls', true);
        $this->assertEquals($sSeoUrlAfter, $oEncoder->UNITprepareUri($sSeoUrlBefore));

        $this->getConfig()->setConfigParam('blSEOLowerCaseUrls', false);
        $this->assertEquals($sSeoUrlBefore, $oEncoder->UNITprepareUri($sSeoUrlBefore));
    }

    /**
     * This test was written for the bug
     * https://bugs.oxid-esales.com/view.php?id=6407
     */
    public function testAddLanguageParam()
    {
        $baseId = 2;
        $oLang = $this->getMock('oxlang', array('getLanguageIds'));
        $oLang
            ->expects($this->any())
            ->method('getLanguageIds')
            ->will($this->returnValue(array($baseId => 'en_US')));
        oxRegistry::set('oxLang', $oLang);

        $sUrl = "Angebote/Transportcontainer-THE-BARREL.html";
        $oEncoder = oxNew('oxSeoEncoder');

        // The addLanguageParam() method should add the language code to the uri only once irrespective of the
        // number of times the method gets called.
        // Hence calling the same method twice in the below code.
        $sUri = $oEncoder->UNITprepareUri($oEncoder->addLanguageParam($sUrl, $baseId), $baseId);
        $this->assertEquals("en-US/Angebote/Transportcontainer-THE-BARREL.html", $sUri);

        $sUri = $oEncoder->UNITprepareUri($oEncoder->addLanguageParam($sUrl, $baseId), $baseId);
        $this->assertEquals("en-US/Angebote/Transportcontainer-THE-BARREL.html", $sUri);
    }
}
