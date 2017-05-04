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

class modSeoEncoderArticle extends oxSeoEncoderArticle
{

    public function setProhibitedID($aProhibitedID)
    {
        $this->_aProhibitedID = $aProhibitedID;
    }

    public function getSeparator()
    {
        return $this->_sSeparator;
    }

    public function getSafePrefix()
    {
        return $this->_getSafePrefix();
    }

    public function setAltPrefix($sOXID)
    {
        $this->_sAltPrefix = $sOXID;
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
class Unit_Core_oxSeoEncoderArticleTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        oxRegistry::get("oxSeoEncoder")->setPrefix('oxid');
        oxRegistry::get("oxSeoEncoder")->setSeparator();
        oxTestModules::cleanUp();

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
        modDB::getInstance()->cleanup();
        // deleting seo entries
        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');
        oxDb::getDb()->execute('delete from oxobject2seodata');
        oxDb::getDb()->execute('delete from oxseohistory');

        $this->cleanUpTable('oxcategories');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxrecommlists');

        oxDb::getDb()->execute('delete from oxarticles where oxid = "testart"');
        oxDb::getDb()->execute('delete from oxobject2category where oxobjectid = "testart"');

        //oxRegistry::getConfig()->setActiveView( null );

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
     * oxSeoEncoderArticle::_getAltUri() test case
     *
     * @return null
     */
    public function testGetAltUriVendor()
    {
        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getListType", "getArticleVendorUri", "getArticleManufacturerUri", "getArticleTagUri", "getArticleUri"));
        $oEncoder->expects($this->once())->method('_getListType')->will($this->returnValue("vendor"));
        $oEncoder->expects($this->once())->method('getArticleVendorUri')->will($this->returnValue("vendorUri"));
        $oEncoder->expects($this->never())->method('getArticleManufacturerUri');
        $oEncoder->expects($this->never())->method('getArticleTagUri');
        $oEncoder->expects($this->never())->method('getArticleUri');

        $this->assertEquals("vendorUri", $oEncoder->UNITgetAltUri('1126', 0));
    }

    /**
     * oxSeoEncoderArticle::_getAltUri() test case
     *
     * @return null
     */
    public function testGetAltUriManufacturer()
    {
        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getListType", "getArticleVendorUri", "getArticleManufacturerUri", "getArticleTagUri", "getArticleUri"));
        $oEncoder->expects($this->once())->method('_getListType')->will($this->returnValue("manufacturer"));
        $oEncoder->expects($this->never())->method('getArticleVendorUri');
        $oEncoder->expects($this->once())->method('getArticleManufacturerUri')->will($this->returnValue("manufacturerUri"));
        $oEncoder->expects($this->never())->method('getArticleTagUri');
        $oEncoder->expects($this->never())->method('getArticleUri');

        $this->assertEquals("manufacturerUri", $oEncoder->UNITgetAltUri('1126', 0));
    }

    /**
     * oxSeoEncoderArticle::_getAltUri() test case
     *
     * @return null
     */
    public function testGetAltUriTag()
    {
        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getListType", "getArticleVendorUri", "getArticleManufacturerUri", "getArticleTagUri", "getArticleUri"));
        $oEncoder->expects($this->once())->method('_getListType')->will($this->returnValue("tag"));
        $oEncoder->expects($this->never())->method('getArticleVendorUri');
        $oEncoder->expects($this->never())->method('getArticleManufacturerUri');
        $oEncoder->expects($this->once())->method('getArticleTagUri')->will($this->returnValue("tagUri"));
        $oEncoder->expects($this->never())->method('getArticleUri');

        $this->assertEquals("tagUri", $oEncoder->UNITgetAltUri('1126', 0));
    }

    /**
     * oxSeoEncoderArticle::_getAltUri() test case
     *
     * @return null
     */
    public function testGetAltUriDefault()
    {
        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getListType", "getArticleVendorUri", "getArticleManufacturerUri", "getArticleTagUri", "getArticleUri"));
        $oEncoder->expects($this->once())->method('_getListType');
        $oEncoder->expects($this->never())->method('getArticleVendorUri');
        $oEncoder->expects($this->never())->method('getArticleManufacturerUri');
        $oEncoder->expects($this->never())->method('getArticleTagUri');
        $oEncoder->expects($this->once())->method('getArticleUri')->will($this->returnValue("defaultUri"));


        $this->assertEquals("defaultUri", $oEncoder->UNITgetAltUri('1126', 0));
    }

    /**
     * #0001472: / in article title
     *
     * @return null
     */
    public function testForBugEntry1472()
    {
        $oArticle = new oxArticle();
        $oArticle->setId("testArticleId");
        $oArticle->oxarticles__oxtitle = new oxField("'DIN lang 1/3 A4 / A6' ? 'EIN Fach' ? '1/3 A4 Prospektständer BIO'", oxField::T_RAW);

        $sUrl = oxRegistry::getConfig()->getConfigParam("sShopURL") . "DIN-lang-1-3-A4-A6-EIN-Fach-1-3-A4-Prospektstaender-BIO.html";
        $this->assertEquals($sUrl, $oArticle->getLink());
    }

    public function testGetArticleUrlRecommType()
    {
        $oEncoder = $this->getMock(
            "oxSeoEncoderArticle", array("getArticleVendorUri", "getArticleManufacturerUri",
                                         "getArticleTagUri", "getArticleRecommUri",
                                         "getArticleUri", "getArticleMainUri", "_getFullUrl")
        );

        $oEncoder->expects($this->never())->method('getArticleVendorUri');
        $oEncoder->expects($this->never())->method('getArticleManufacturerUri');
        $oEncoder->expects($this->never())->method('getArticleTagUri');
        $oEncoder->expects($this->never())->method('getArticleUri');
        $oEncoder->expects($this->never())->method('getArticleMainUri');

        $oEncoder->expects($this->once())->method('getArticleRecommUri')->will($this->returnValue("testRecommUrl"));
        $oEncoder->expects($this->once())->method('_getFullUrl')->will($this->returnValue("testFullRecommUrl"));

        $this->assertEquals("testFullRecommUrl", $oEncoder->getArticleUrl(new oxArticle, 0, OXARTICLE_LINKTYPE_RECOMM));
    }

    /**
     * Testing if recomm list is taken from view
     *
     * @return
     */
    public function testGetRecomm()
    {
        $oView = $this->getMock("oxUBase", array("getActiveRecommList"));
        $oView->expects($this->once())->method('getActiveRecommList')->will($this->returnValue("testRecommList"));

        oxRegistry::getConfig()->setActiveView($oView);

        $oEncoder = new oxSeoEncoderArticle();
        $this->assertEquals("testRecommList", $oEncoder->UNITgetRecomm(new oxarticle, 0));
    }

    /**
     * Testing if tag is taken from view
     *
     * @return
     */
    public function testGetTag()
    {
        $oView = $this->getMock("oxUBase", array("getTag"));
        $oView->expects($this->once())->method('getTag')->will($this->returnValue("testTag"));

        oxRegistry::getConfig()->setActiveView($oView);

        $oEncoder = new oxSeoEncoderArticle();
        $this->assertEquals("testTag", $oEncoder->UNITgetTag(new oxarticle, 0));
    }

    /**
     * article has no vendor defined
     *
     * @return null
     */
    public function testGetVendorArticleHasNoManufacturerDefined()
    {
        $oEncoder = new oxSeoEncoderArticle();
        $this->assertNull($oEncoder->UNITgetVendor(new oxArticle(), 0));
    }

    /**
     * article has no manufacturer defined
     *
     * @return null
     */
    public function testGetVendorUnknownViewClass()
    {
        $sVendorId = oxDb::getDb()->getOne("select oxid from oxvendor");

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxvendorid = new oxField($sVendorId);

        oxRegistry::getConfig()->setActiveView(new oxUbase);

        $oEncoder = new oxSeoEncoderArticle();
        $oVendor = $oEncoder->UNITgetVendor($oArticle, 0);
        $this->assertNotNull($oVendor);
        $this->assertEquals($sVendorId, $oVendor->getId());
    }

    /**
     * unknown Vendor id
     *
     * @return null
     */
    public function testGetVendorUnknownManufacturerId()
    {
        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxvendorid = new oxField("xxx");

        $oEncoder = new oxSeoEncoderArticle();
        $this->assertNull($oEncoder->UNITgetVendor($oArticle, 0));
    }

    /**
     * current view Vendor matches product
     *
     * @return null
     */
    public function testGetVendorCurrentViewVendorMatchesProduct()
    {
        $sVendorId = oxDb::getDb()->getOne("select oxid from oxvendor");

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxvendorid = new oxField($sVendorId);

        $oVendor = new oxVendor();
        $oVendor->load($sVendorId);

        $oView = $this->getMock("oxUBase", array("getActVendor"));
        $oView->expects($this->once())->method('getActVendor')->will($this->returnValue($oVendor));

        oxRegistry::getConfig()->setActiveView($oView);

        $oEncoder = new oxSeoEncoderArticle();
        $oManufacturer = $oEncoder->UNITgetVendor($oArticle, 0);
        $this->assertNotNull($oVendor);
        $this->assertEquals($sVendorId, $oVendor->getId());
    }

    /**
     * language ids does not match
     *
     * @return null
     */
    public function testGetVendorLanguageIdsDoesNotMatch()
    {
        $sVendorId = oxDb::getDb()->getOne("select oxid from oxvendor");

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxvendorid = new oxField($sVendorId);

        $oVendor = new oxVendor();
        $oVendor->load($sVendorId);

        $oView = $this->getMock("oxUBase", array("getActVendor"));
        $oView->expects($this->once())->method('getActVendor')->will($this->returnValue($oVendor));

        oxRegistry::getConfig()->setActiveView($oView);

        $oEncoder = new oxSeoEncoderArticle();
        $oVendor = $oEncoder->UNITgetVendor($oArticle, 1);
        $this->assertNotNull($oVendor);
        $this->assertEquals($sVendorId, $oVendor->getId());

    }

    /**
     * view manufacturer does not match article
     *
     * @return null
     */
    public function testGetVendorViewVendorDoesNotMatchArticle()
    {
        $sVendorId = oxDb::getDb()->getOne("select oxid from oxvendor");

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxvendorid = new oxField($sVendorId);

        $oVendor = new oxVendor();
        $oVendor->setId("xxx");

        $oView = $this->getMock("oxUBase", array("getActVendor"));
        $oView->expects($this->once())->method('getActVendor')->will($this->returnValue($oVendor));

        oxRegistry::getConfig()->setActiveView($oView);

        $oEncoder = new oxSeoEncoderArticle();
        $oVendor = $oEncoder->UNITgetVendor($oArticle, 0);
        $this->assertNotNull($oVendor);
        $this->assertEquals($sVendorId, $oVendor->getId());
    }

    /**
     * article has no manufacturer defined
     *
     * @return null
     */
    public function testGetManufacturerArticleHasNoManufacturerDefined()
    {
        $oEncoder = new oxSeoEncoderArticle();
        $this->assertNull($oEncoder->UNITgetManufacturer(new oxArticle(), 0));
    }

    /**
     * article has no manufacturer defined
     *
     * @return null
     */
    public function testGetManufacturerUnknownViewClass()
    {
        $sManufacturerId = oxDb::getDb()->getOne("select oxid from oxmanufacturers");

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManufacturerId);

        oxRegistry::getConfig()->setActiveView(new oxUbase);

        $oEncoder = new oxSeoEncoderArticle();
        $oManufacturer = $oEncoder->UNITgetManufacturer($oArticle, 0);
        $this->assertNotNull($oManufacturer);
        $this->assertEquals($sManufacturerId, $oManufacturer->getId());
    }

    /**
     * unknown Manufacturer id
     *
     * @return null
     */
    public function testGetManufacturerUnknownManufacturerId()
    {
        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxmanufacturerid = new oxField("xxx");

        $oEncoder = new oxSeoEncoderArticle();
        $this->assertNull($oEncoder->UNITgetManufacturer($oArticle, 0));
    }

    /**
     * current view manufacturer matches product
     *
     * @return null
     */
    public function testGetManufacturerCurrentViewManufacturerMatchesProduct()
    {
        $sManufacturerId = oxDb::getDb()->getOne("select oxid from oxmanufacturers");

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManufacturerId);

        $oManufacturer = new oxManufacturer();
        $oManufacturer->load($sManufacturerId);

        $oView = $this->getMock("oxUBase", array("getActManufacturer"));
        $oView->expects($this->once())->method('getActManufacturer')->will($this->returnValue($oManufacturer));

        oxRegistry::getConfig()->setActiveView($oView);

        $oEncoder = new oxSeoEncoderArticle();
        $oManufacturer = $oEncoder->UNITgetManufacturer($oArticle, 0);
        $this->assertNotNull($oManufacturer);
        $this->assertEquals($sManufacturerId, $oManufacturer->getId());
    }

    /**
     * language ids does not match
     *
     * @return null
     */
    public function testGetManufacturerLanguageIdsDoesNotMatch()
    {
        $sManufacturerId = oxDb::getDb()->getOne("select oxid from oxmanufacturers");

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManufacturerId);

        $oManufacturer = new oxManufacturer();
        $oManufacturer->load($sManufacturerId);

        $oView = $this->getMock("oxUBase", array("getActManufacturer"));
        $oView->expects($this->once())->method('getActManufacturer')->will($this->returnValue($oManufacturer));

        oxRegistry::getConfig()->setActiveView($oView);

        $oEncoder = new oxSeoEncoderArticle();
        $oManufacturer = $oEncoder->UNITgetManufacturer($oArticle, 1);
        $this->assertNotNull($oManufacturer);
        $this->assertEquals($sManufacturerId, $oManufacturer->getId());

    }

    /**
     * view manufacturer does not match article
     *
     * @return null
     */
    public function testGetManufacturerViewManufacturerDoesNotMatchArticle()
    {
        $sManufacturerId = oxDb::getDb()->getOne("select oxid from oxmanufacturers");

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManufacturerId);

        $oManufacturer = new oxManufacturer();
        $oManufacturer->setId("xxx");

        $oView = $this->getMock("oxUBase", array("getActManufacturer"));
        $oView->expects($this->once())->method('getActManufacturer')->will($this->returnValue($oManufacturer));

        oxRegistry::getConfig()->setActiveView($oView);

        $oEncoder = new oxSeoEncoderArticle();
        $oManufacturer = $oEncoder->UNITgetManufacturer($oArticle, 0);
        $this->assertNotNull($oManufacturer);
        $this->assertEquals($sManufacturerId, $oManufacturer->getId());
    }

    public function testGetArticleRecommUri()
    {
        $iLang = 0;

        $oArticle = new oxArticle();
        $oArticle->setId("_testArticleId");

        // creating test recomm list
        $oRecomm = new oxRecommList();
        $oRecomm->setId("_testRecomm");
        $oRecomm->oxrecommlists__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId());
        $oRecomm->oxrecommlists__oxtitle = new oxField("testrecommtitle");
        $oRecomm->save();

        $sRecommSeoUrl = oxRegistry::get("oxSeoEncoderRecomm")->getRecommUri($oRecomm, $iLang);

        $oEncoder = $this->getMock(
            "oxSeoEncoderArticle", array("_getRecomm",
                                         "_loadFromDb",
                                         "_getProductForLang",
                                         "_prepareArticleTitle",
                                         "_processSeoUrl",
                                         "_getListType")
        );
        $oEncoder->expects($this->once())->method('_getRecomm')->will($this->returnValue($oRecomm));
        $oEncoder->expects($this->once())->method('_loadFromDb')->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('_getProductForLang')->will($this->returnValue($oArticle));
        $oEncoder->expects($this->once())->method('_prepareArticleTitle')->will($this->returnValue("testArticleTitle"));
        $oEncoder->expects($this->once())->method('_processSeoUrl')->with($this->equalTo($sRecommSeoUrl . "testArticleTitle"), $this->equalTo($oArticle->getId()), $this->equalTo($iLang))->will($this->returnValue($sRecommSeoUrl . "testArticleTitle/"));
        $oEncoder->expects($this->once())->method('_getListType')->will($this->returnValue("recommlist"));
        $this->assertEquals($sRecommSeoUrl . "testArticleTitle/", $oEncoder->getArticleRecommUri($oArticle, $iLang));
    }

    public function testGetArticleMainUriDataInDbFound()
    {
        $oArticle = $this->getMock("oxarticle", array("getId", "getStdLink"));
        $oArticle->expects($this->atLeastOnce())->method('getId')->will($this->returnValue('testId'));
        $oArticle->expects($this->never())->method('getStdLink');

        $oEncoder = $this->getMock(
            "oxseoencoderarticle", array("_loadFromDb", "_getProductForLang", "_createArticleCategoryUri",
                                         "_processSeoUrl", "_prepareArticleTitle", "_saveToDb")
        );

        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalto('oxarticle'), $this->equalto('testId'), $this->equalto(0), $this->equalto(null), $this->equalto(''), $this->equalto(true))->will($this->returnValue('testSeoUri'));
        $oEncoder->expects($this->never())->method('_getProductForLang');
        $oEncoder->expects($this->never())->method('_createArticleCategoryUri');
        $oEncoder->expects($this->never())->method('_processSeoUrl');
        $oEncoder->expects($this->never())->method('_prepareArticleTitle');
        $oEncoder->expects($this->never())->method('_saveToDb');

        $this->assertEquals('testSeoUri', $oEncoder->getArticleMainUri($oArticle, 0));
    }

    public function testGetArticleMainUriHasCategory()
    {
        $sMainCatId = oxDb::getDb()->getOne("select oxcatnid from " . getViewName("oxobject2category") . " where oxobjectid = '1126' order by oxtime");

        $oCategory = oxNew("oxcategory");
        $oCategory->load($sMainCatId);

        $oArticle = $this->getMock("oxarticle", array("getId", "getStdLink"));
        $oArticle->expects($this->atLeastOnce())->method('getId')->will($this->returnValue('1126'));
        $oArticle->expects($this->never())->method('getStdLink');

        $oEncoder = $this->getMock(
            "oxseoencoderarticle", array("_loadFromDb", "_getProductForLang", "_createArticleCategoryUri",
                                         "_processSeoUrl", "_prepareArticleTitle", "_saveToDb")
        );

        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxarticle'), $this->equalTo('1126'), $this->equalTo(0), $this->equalTo(null), $this->equalTo($sMainCatId), $this->equalTo(true))->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('_createArticleCategoryUri')->with($this->equalTo($oArticle), $this->equalTo($oCategory), $this->equalTo(0))->will($this->returnValue('testSeoUri'));
        $oEncoder->expects($this->never())->method('_getProductForLang');
        $oEncoder->expects($this->never())->method('_processSeoUrl');
        $oEncoder->expects($this->never())->method('_prepareArticleTitle');
        $oEncoder->expects($this->never())->method('_saveToDb');

        $this->assertEquals('testSeoUri', $oEncoder->getArticleMainUri($oArticle, 0));
    }

    public function testGetArticleMainUriVariantHasCategory()
    {
        $sMainCatId = oxDb::getDb()->getOne("select oxcatnid from " . getViewName("oxobject2category") . " where oxobjectid = '1126' order by oxtime");

        $oCategory = oxNew("oxcategory");
        $oCategory->load($sMainCatId);

        $oArticle = $this->getMock("oxarticle", array("getId", "getStdLink"));
        $oArticle->oxarticles__oxparentid = new oxField('1126');
        $oArticle->expects($this->atLeastOnce())->method('getId')->will($this->returnValue('testVarId'));
        $oArticle->expects($this->never())->method('getStdLink');

        $oEncoder = $this->getMock(
            "oxseoencoderarticle", array("_loadFromDb", "_getProductForLang", "_createArticleCategoryUri",
                                         "_processSeoUrl", "_prepareArticleTitle", "_saveToDb")
        );

        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxarticle'), $this->equalTo('testVarId'), $this->equalTo(0), $this->equalTo(null), $this->equalTo($sMainCatId), $this->equalTo(true))->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('_createArticleCategoryUri')->with($this->equalTo($oArticle), $this->equalTo($oCategory), $this->equalTo(0))->will($this->returnValue('testSeoUri'));
        $oEncoder->expects($this->never())->method('_getProductForLang');
        $oEncoder->expects($this->never())->method('_processSeoUrl');
        $oEncoder->expects($this->never())->method('_prepareArticleTitle');
        $oEncoder->expects($this->never())->method('_saveToDb');

        $this->assertEquals('testSeoUri', $oEncoder->getArticleMainUri($oArticle, 0));
    }

    public function testGetArticleMainUriHasNoCategory()
    {
        $oArticle = $this->getMock("oxarticle", array("getId", "getBaseStdLink"));
        $oArticle->expects($this->atLeastOnce())->method('getId')->will($this->returnValue('testId'));
        $oArticle->expects($this->once())->method('getBaseStdLink')->with($this->equalTo(0))->will($this->returnValue('testBaseStdLink'));

        $oEncoder = $this->getMock(
            "oxseoencoderarticle", array("_loadFromDb", "_getProductForLang", "_createArticleCategoryUri",
                                         "_processSeoUrl", "_prepareArticleTitle", "_saveToDb")
        );

        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxarticle'), $this->equalTo('testId'), $this->equalTo(0), $this->equalTo(null), $this->equalTo(''), $this->equalTo(true))->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('_getProductForLang')->with($this->equalTo($oArticle), $this->equalTo(0))->will($this->returnValue($oArticle));
        $oEncoder->expects($this->once())->method('_prepareArticleTitle')->with($this->equalTo($oArticle))->will($this->returnValue('testArticleTitle'));
        $oEncoder->expects($this->once())->method('_processSeoUrl')->with($this->equalTo('testArticleTitle'), $this->equalTo('testId'), $this->equalTo(0))->will($this->returnValue('testSeoUri'));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo('oxarticle'), $this->equalTo('testId'), $this->equalTo('testBaseStdLink'), $this->equalTo('testSeoUri'), $this->equalTo(0), $this->equalTo(null), $this->equalTo(0), $this->equalTo(''));

        $oEncoder->expects($this->never())->method('_createArticleCategoryUri');

        $this->assertEquals('testSeoUri', $oEncoder->getArticleMainUri($oArticle, 0));
    }

    public function testGetArticleTagUri()
    {
        $oArticle = new oxArticle();
        $oArticle->load('1126');

        $oSeoEncoderArticle = $this->getMock("oxSeoEncoderArticle", array("_getTag"));

        $sTag = 'absinth';
        $sTagUrl = "tag/{$sTag}/Bar-Set-ABSINTH.html";

        $oSeoEncoderArticle->expects($this->any())->method('_getTag')->will($this->returnValue($sTag));

        $this->assertEquals($sTagUrl, $oSeoEncoderArticle->getArticleTagUri($oArticle, 0));

        // chache works ?
        $this->assertEquals($sTagUrl, $oSeoEncoderArticle->getArticleTagUri($oArticle, 0));
    }

    public function testGetProductForLang()
    {
        $oArticle = $this->getMock('oxarticle', array('getLanguage', 'getId'));
        $oArticle->expects($this->once())->method('getLanguage')->will($this->returnValue(2));
        $oArticle->expects($this->once())->method('getId')->will($this->returnValue('1126'));

        $oEncoder = new oxSeoEncoderArticle();
        $oArticle = $oEncoder->UNITgetProductForLang($oArticle, 0);
        $this->assertEquals('1126', $oArticle->getId());

        $oArticle = $this->getMock('oxarticle', array('getLanguage', 'getId'));
        $oArticle->expects($this->once())->method('getLanguage')->will($this->returnValue(0));
        $oArticle->expects($this->once())->method('getId')->will($this->returnValue('1126'));

        $oEncoder = new oxSeoEncoderArticle();
        $oArticle = $oEncoder->UNITgetProductForLang($oArticle, 0);
        $this->assertEquals('1126', $oArticle->getId());

    }

    public function testCreateArticleSeoUrlWhenTitleContainsOnlyBadChars()
    {
        oxTestModules::addFunction("oxlang", "getSeoReplaceChars", "{return array();}");

        $oArticle = $this->getMock('oxArticle', array('getCategory'));
        $oArticle = new oxArticle();
        $oArticle->setId('_testArtId');
        $oArticle->oxarticles__oxtitle = new oxField('äöüÄÖÜß');

        $this->assertEquals(oxRegistry::getConfig()->getConfigParam("sShopURL") . "oxid.html", $oArticle->getLink());
    }

    public function testGetArticleVendorUriArticleHasNoVendorAssigned()
    {
        $sVendorId = oxDb::getDb()->getOne("select oxid from oxvendor");
        $oVendor = new oxVendor();
        $oVendor->load($sVendorId);

        $sSeoUri = "Nach-Lieferant/" . str_replace(array(' ', '.', '+'), '-', $oVendor->oxvendor__oxtitle->value) . "/oxid-test-article-title-oxid-test-article-var-select.html";

        $oArticle = new oxarticle();
        $oArticle->oxarticles__oxtitle = new oxField('oxid test article title');
        $oArticle->oxarticles__oxvarselect = new oxField('oxid test article var select');
        $oArticle->oxarticles__oxvendorid = new oxField($sVendorId);

        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getVendor"));
        $oEncoder->expects($this->once())->method('_getVendor')->will($this->returnValue($oVendor));
        $this->assertEquals($sSeoUri, $oEncoder->getArticleVendorUri($oArticle, 0));
    }

    public function testGetArticleManufacturerUriArticleHasNoManufacturerAssigned()
    {
        $sManufacturerId = oxDb::getDb()->getOne("select oxid from oxmanufacturers");
        $oManufacturer = new oxManufacturer();
        $oManufacturer->load($sManufacturerId);
        $sSeoUri = "Nach-Hersteller/" . str_replace(array(' ', '.', '+'), '-', $oManufacturer->oxmanufacturers__oxtitle->value) . "/oxid-test-article-title-oxid-test-article-var-select.html";

        $oArticle = new oxarticle();
        $oArticle->oxarticles__oxtitle = new oxField('oxid test article title');
        $oArticle->oxarticles__oxvarselect = new oxField('oxid test article var select');
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManufacturerId);

        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getManufacturer"));
        $oEncoder->expects($this->once())->method('_getManufacturer')->will($this->returnValue($oManufacturer));
        $this->assertEquals($sSeoUri, $oEncoder->getArticleManufacturerUri($oArticle, 0));
    }

    public function testGetArticleVendorUriArticleArticleIsAssignedToVendor()
    {
        $sVendorId = oxDb::getDb()->getOne("select oxid from oxvendor");
        $oVendor = new oxVendor();
        $oVendor->load($sVendorId);

        $sSeoUri = 'Nach-Lieferant/' . str_replace(array(' ', '.', '+'), '-', $oVendor->oxvendor__oxtitle->value) . '/oxid-test-article-title-oxid-test-article-var-select.html';

        $oArticle = new oxarticle();
        $oArticle->oxarticles__oxtitle = new oxField('oxid test article title');
        $oArticle->oxarticles__oxvarselect = new oxField('oxid test article var select');
        $oArticle->oxarticles__oxvendorid = new oxField($sVendorId);

        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getVendor"));
        $oEncoder->expects($this->once())->method('_getVendor')->will($this->returnValue($oVendor));
        $this->assertEquals($sSeoUri, $oEncoder->getArticleVendorUri($oArticle, 0));
    }

    public function testGetArticleManufacturerUriArticleArticleIsAssignedToManufacturer()
    {
        $sManufacturerId = oxDb::getDb()->getOne('select oxid from oxmanufacturers');
        $oManufacturer = new oxManufacturer();
        $oManufacturer->load($sManufacturerId);

        $sSeoUri = 'Nach-Hersteller/' . str_replace(array(' ', '.', '+'), '-', $oManufacturer->oxmanufacturers__oxtitle->value) . '/oxid-test-article-title-oxid-test-article-var-select.html';

        $oArticle = new oxarticle();
        $oArticle->oxarticles__oxtitle = new oxField('oxid test article title');
        $oArticle->oxarticles__oxvarselect = new oxField('oxid test article var select');
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManufacturerId);

        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getManufacturer"));
        $oEncoder->expects($this->once())->method('_getManufacturer')->will($this->returnValue($oManufacturer));
        $this->assertEquals($sSeoUri, $oEncoder->getArticleManufacturerUri($oArticle, 0));
    }

    public function testGetArticleVendorUriArticleArticleIsAssignedToVendorWithLangParam()
    {
        $sVendorId = oxDb::getDb()->getOne('select oxid from oxvendor');
        $oVendor = new oxVendor();
        $oVendor->load($sVendorId);

        $oArticle = new oxarticle();
        $oArticle->setLanguage(1);
        $sArtId = '1354';
        $oxtitle = 'Wanduhr-SPIDER';

        $sSeoUri = 'Nach-Lieferant/' . str_replace(array(' ', '.', '+'), '-', $oVendor->oxvendor__oxtitle->value) . '/' . $oxtitle . '-oxid-test-article-var-select.html';

        $oArticle->setId($sArtId);
        $oArticle->oxarticles__oxtitle = new oxField($oxtitle);
        oxTestModules::addFunction('oxarticle', 'loadInLang', '{parent::loadInLang($aA[0], $aA[1]);$this->oxarticles__oxvarselect = new oxField( "oxid test article var select" );}');
        $oArticle->oxarticles__oxvarselect = new oxField('if this is here, object is not reloaded: bad :(');
        $oArticle->oxarticles__oxvendorid = new oxField($sVendorId);

        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getVendor"));
        $oEncoder->expects($this->once())->method('_getVendor')->will($this->returnValue($oVendor));
        $this->assertEquals($sSeoUri, $oEncoder->getArticleVendorUri($oArticle, 0));
    }

    public function testGetArticleManufacturerUriArticleArticleIsAssignedToManufacturerWithLangParam()
    {
        $sManufacturerId = oxDb::getDb()->getOne('select oxid from oxmanufacturers');
        $oManufacturer = new oxManufacturer();
        $oManufacturer->load($sManufacturerId);

        $oArticle = new oxarticle();
        $oArticle->setLanguage(1);
        $sArtId = '1354';
        $oxtitle = 'Wanduhr-SPIDER';

        $sSeoUri = 'Nach-Hersteller/' . str_replace(array(' ', '.', '+'), '-', $oManufacturer->oxmanufacturers__oxtitle->value) . '/' . $oxtitle . '-oxid-test-article-var-select.html';

        $oArticle->setId($sArtId);
        $oArticle->oxarticles__oxtitle = new oxField($oxtitle);
        oxTestModules::addFunction('oxarticle', 'loadInLang', '{parent::loadInLang($aA[0], $aA[1]);$this->oxarticles__oxvarselect = new oxField( "oxid test article var select" );}');
        $oArticle->oxarticles__oxvarselect = new oxField('if this is here, object is not reloaded: bad :(');
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManufacturerId);

        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getManufacturer"));
        $oEncoder->expects($this->once())->method('_getManufacturer')->will($this->returnValue($oManufacturer));
        $this->assertEquals($sSeoUri, $oEncoder->getArticleManufacturerUri($oArticle, 0));
    }

    public function testGetArticleVendorUriArticleArticleIsAssignedToVendorEngWithLangParam()
    {
        $sVendorId = oxDb::getDb()->getOne('select oxid from oxvendor');
        $oVendor = new oxVendor();
        $oVendor->loadInLang(1, $sVendorId);

        $oArticle = new oxarticle();
        $oArticle->setLanguage(0);
        $sArtId = '1354';
        $oxtitle = 'Wall-Clock-SPIDER';

        $sSeoUri = 'en/By-Distributor/' . str_replace(array(' ', '.', '+'), '-', $oVendor->oxvendor__oxtitle->value) . '/' . $oxtitle . '-oxid-test-article-var-select.html';

        $oArticle->setId($sArtId);
        $oArticle->oxarticles__oxtitle = new oxField($oxtitle);
        oxTestModules::addFunction('oxarticle', 'loadInLang', '{parent::loadInLang($aA[0], $aA[1]);$this->oxarticles__oxvarselect = new oxField( "oxid test article var select" );}');
        $oArticle->oxarticles__oxvarselect = new oxField('if this is here, object is not reloaded: bad :(');
        $oArticle->oxarticles__oxvendorid = new oxField($sVendorId);

        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getVendor"));
        $oEncoder->expects($this->once())->method('_getVendor')->will($this->returnValue($oVendor));
        $this->assertEquals($sSeoUri, $oEncoder->getArticleVendorUri($oArticle, 1));
    }

    public function testGetArticleManufacturerUriArticleArticleIsAssignedToManufacturerEngWithLangParam()
    {
        $sManufacturerId = oxDb::getDb()->getOne('select oxid from oxmanufacturers');
        $oManufacturer = new oxManufacturer();
        $oManufacturer->loadInLang(1, $sManufacturerId);

        $oArticle = new oxarticle();
        $oArticle->setLanguage(0);
        $sArtId = '1354';
        $oxtitle = 'Wall-Clock-SPIDER';

        $sSeoUri = 'en/By-Manufacturer/' . str_replace(array(' ', '.', '+'), '-', $oManufacturer->oxmanufacturers__oxtitle->value) . '/' . $oxtitle . '-oxid-test-article-var-select.html';

        $oArticle->setId($sArtId);
        $oArticle->oxarticles__oxtitle = new oxField($oxtitle);
        oxTestModules::addFunction('oxarticle', 'loadInLang', '{parent::loadInLang($aA[0], $aA[1]);$this->oxarticles__oxvarselect = new oxField( "oxid test article var select" );}');
        $oArticle->oxarticles__oxvarselect = new oxField('if this is here, object is not reloaded: bad :(');
        $oArticle->oxarticles__oxmanufacturerid = new oxField($sManufacturerId);

        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getManufacturer"));
        $oEncoder->expects($this->once())->method('_getManufacturer')->will($this->returnValue($oManufacturer));
        $this->assertEquals($sSeoUri, $oEncoder->getArticleManufacturerUri($oArticle, 1));
    }

    public function testPrepareArticleTitle()
    {
        $oArticle = new oxarticle();
        $oArticle->oxarticles__oxtitle = new oxfield('test main title');

        $oEncoder = new oxSeoEncoderArticle();
        $this->assertEquals('test-main-title.html', $oEncoder->UNITprepareArticleTitle($oArticle));

        // no title just number
        $oArticle = new oxarticle();
        $oArticle->oxarticles__oxartnum = new oxfield('123-321');
        $this->assertEquals('123-321.html', $oEncoder->UNITprepareArticleTitle($oArticle));

        // varselect is set
        $oArticle = new oxarticle();
        $oArticle->oxarticles__oxvarselect = new oxfield('test var select');
        $this->assertEquals('test-var-select.html', $oEncoder->UNITprepareArticleTitle($oArticle));

        // no data is set
        $oArticle = new oxarticle();
        $this->assertEquals('oxid.html', $oEncoder->UNITprepareArticleTitle($oArticle));

        // variant
        $sVarId = oxDb::getDb()->getOne("select oxid from oxarticles where oxparentid !=''");
        $oVariant = new oxArticle();
        $oVariant->load($sVarId);

        $oParent = new oxArticle();
        $oParent->load($oVariant->oxarticles__oxparentid->value);

        $oVariant->oxarticles__oxtitle = new oxField("");
        $oVariant->oxarticles__oxvarselect = new oxField("varselect1");
        $sTitle = str_replace(".", "-varselect1.", $oEncoder->UNITprepareArticleTitle($oParent));

        $this->assertEquals($sTitle, $oEncoder->UNITprepareArticleTitle($oVariant));

        $oVariant->oxarticles__oxvarselect = new oxField("varselect2");
        $sTitle = str_replace(".", "-varselect2.", $oEncoder->UNITprepareArticleTitle($oParent));
        $this->assertEquals($sTitle, $oEncoder->UNITprepareArticleTitle($oVariant));
    }

    public function testGetArticleUrl()
    {
        $oArticle = $this->getMock('oxarticle', array('getLanguage'));
        $oArticle->expects($this->once())->method('getLanguage')->will($this->returnValue(0));

        $oEncoder = $this->getMock('oxSeoEncoderArticle', array('_getFullUrl', 'getArticleUri', 'getArticleVendorUri', 'getArticleManufacturerUri', 'getArticleMainUri'));
        $oEncoder->expects($this->once())->method('getArticleUri')->will($this->returnValue("seoArticleUri"));
        $oEncoder->expects($this->never())->method('getArticleVendorUri');
        $oEncoder->expects($this->never())->method('getArticleManufacturerUri');
        $oEncoder->expects($this->never())->method('getArticleMainUri');
        $oEncoder->expects($this->once())->method('_getFullUrl')->will($this->returnValue('seoarturl'));

        $this->assertEquals('seoarturl', $oEncoder->getArticleUrl($oArticle));
    }

    public function testGetArticleUrlForVendor()
    {
        $oArticle = $this->getMock('oxarticle', array('getLanguage'));
        $oArticle->expects($this->once())->method('getLanguage')->will($this->returnValue(0));

        $oEncoder = $this->getMock('oxSeoEncoderArticle', array('_getFullUrl', 'getArticleUri', 'getArticleVendorUri', 'getArticleManufacturerUri', 'getArticleMainUri'));
        $oEncoder->expects($this->once())->method('_getFullUrl')->will($this->returnValue('seoarturl'));
        $oEncoder->expects($this->never())->method('getArticleUri');
        $oEncoder->expects($this->once())->method('getArticleVendorUri')->will($this->returnValue('seoarturl'));;
        $oEncoder->expects($this->never())->method('getArticleManufacturerUri');
        $oEncoder->expects($this->never())->method('getArticleMainUri');

        $this->assertEquals('seoarturl', $oEncoder->getArticleUrl($oArticle, null, 1));
    }

    public function testGetArticleUrlForManufacturer()
    {
        $oArticle = $this->getMock('oxarticle', array('getLanguage'));
        $oArticle->expects($this->once())->method('getLanguage')->will($this->returnValue(0));

        $oEncoder = $this->getMock('oxSeoEncoderArticle', array('_getFullUrl', 'getArticleUri', 'getArticleVendorUri', 'getArticleManufacturerUri', 'getArticleMainUri'));
        $oEncoder->expects($this->once())->method('_getFullUrl')->will($this->returnValue('seoarturl'));
        $oEncoder->expects($this->never())->method('getArticleUri');
        $oEncoder->expects($this->never())->method('getArticleVendorUri');
        $oEncoder->expects($this->once())->method('getArticleManufacturerUri')->will($this->returnValue('seoarturl'));
        $oEncoder->expects($this->never())->method('getArticleMainUri');

        $this->assertEquals('seoarturl', $oEncoder->getArticleUrl($oArticle, null, 2));
    }

    public function testGetArticleUrlForPriceCategory()
    {
        $oArticle = $this->getMock('oxarticle', array('getLanguage'));
        $oArticle->expects($this->once())->method('getLanguage')->will($this->returnValue(0));

        $oEncoder = $this->getMock('oxSeoEncoderArticle', array('_getFullUrl', 'getArticleUri', 'getArticleVendorUri', 'getArticleManufacturerUri', 'getArticleMainUri'));
        $oEncoder->expects($this->once())->method('_getFullUrl')->will($this->returnValue('seoarturl'));
        $oEncoder->expects($this->once())->method('getArticleUri')->will($this->returnValue('seoarturl'));
        $oEncoder->expects($this->never())->method('getArticleVendorUri');
        $oEncoder->expects($this->never())->method('getArticleManufacturerUri');
        $oEncoder->expects($this->never())->method('getArticleMainUri');

        $this->assertEquals('seoarturl', $oEncoder->getArticleUrl($oArticle, null, 3));
    }

    public function testGetArticleUrlForTag()
    {
        $oArticle = $this->getMock('oxarticle', array('getLanguage'));
        $oArticle->expects($this->once())->method('getLanguage')->will($this->returnValue(0));

        $oEncoder = $this->getMock('oxSeoEncoderArticle', array('_getFullUrl', 'getArticleTagUri', '_getArticlePriceCategoryUri', 'getArticleUri', 'getArticleVendorUri', 'getArticleManufacturerUri', 'getArticleMainUri'));
        $oEncoder->expects($this->once())->method('_getFullUrl')->will($this->returnValue('seoarturl'));
        $oEncoder->expects($this->once())->method('getArticleTagUri')->will($this->returnValue('seoarturl'));
        $oEncoder->expects($this->never())->method('_getArticlePriceCategoryUri');
        $oEncoder->expects($this->never())->method('getArticleUri');
        $oEncoder->expects($this->never())->method('getArticleVendorUri');
        $oEncoder->expects($this->never())->method('getArticleManufacturerUri');
        $oEncoder->expects($this->never())->method('getArticleMainUri');

        $this->assertEquals('seoarturl', $oEncoder->getArticleUrl($oArticle, null, 4));
    }

    /**
     * Testing article uri getter
     */
    public function testGetArticleUri()
    {
        $oCategory = $this->getMock("oxCategory", array("isPriceCategory"));
        $oCategory->expects($this->any())->method('isPriceCategory')->will($this->returnValue(false));
        $oCategory->load(oxDb::getDb()->getOne("select oxid from oxcategories where oxparentid = 'oxrootid'"));

        $oArticle = $this->getMock("oxarticle", array("inCategory"));
        $oArticle->expects($this->once())->method('inCategory')->will($this->returnValue(true));
        $oArticle->oxarticles__oxtitle = new oxField('Messerblock VOODOO');
        $oArticle->oxarticles__oxvarselect = new oxField('test var select');
        $oArticle->oxarticles__oxartnum = new oxField('123');
        $oArticle->oxarticles__oxprice = new oxField(100);

        $sUrl = oxRegistry::get("oxSeoEncoder")->UNITprepareTitle($oCategory->oxcategories__oxtitle->value) . '/Messerblock-VOODOO-test-var-select.html';
        $oEncoder = $this->getMock("oxSeoEncoderArticle", array("_getCategory"));
        $oEncoder->expects($this->once())->method('_getCategory')->will($this->returnValue($oCategory));
        $this->assertEquals($sUrl, $oEncoder->getArticleUri($oArticle, 0));
    }

    public function testGetArticleUriWithoutTitle()
    {
        $oCategory = $this->getMock("oxCategory", array("isPriceCategory"));
        $oCategory->expects($this->any())->method('isPriceCategory')->will($this->returnValue(true));
        $oCategory->load(oxDb::getDb()->getOne("select oxid from oxcategories where oxparentid = 'oxrootid'"));

        $oArticle = $this->getMock("oxarticle", array("inCategory", "inPriceCategory"));
        $oArticle->expects($this->never())->method('inCategory');
        $oArticle->expects($this->once())->method('inPriceCategory')->will($this->returnValue(true));
        $oArticle->oxarticles__oxtitle = new oxField('');
        $oArticle->oxarticles__oxid = new oxField('testtestnocat');
        $oArticle->oxarticles__oxartnum = new oxField('123');

        $oEncoder = $this->getMock("oxSeoEncoderArticle", array('_loadFromDb', "_getCategory"));
        $oEncoder->expects($this->once())->method('_getCategory')->will($this->returnValue($oCategory));
        $oEncoder->expects($this->once())->method('_loadFromDb')->will($this->returnValue(false));

        $this->assertEquals(oxRegistry::get("oxSeoEncoder")->UNITprepareTitle($oCategory->oxcategories__oxtitle->value) . "/123.html", $oEncoder->getArticleUri($oArticle, 0));
    }

    public function testGetArticleUriWithoutTitleInEnglish()
    {
        $oCategory = $this->getMock("oxCategory", array("isPriceCategory"));
        $oCategory->expects($this->any())->method('isPriceCategory')->will($this->returnValue(false));
        $oCategory->loadInLang(1, oxDb::getDb()->getOne("select oxid from oxcategories where oxparentid = 'oxrootid'"));

        $oArticle = $this->getMock("oxarticle", array("inCategory"));
        $oArticle->expects($this->once())->method('inCategory')->will($this->returnValue(true));
        $oArticle->setLanguage(1);
        $oArticle->oxarticles__oxtitle = new oxField('');
        $oArticle->oxarticles__oxid = new oxField('testtest');
        $oArticle->oxarticles__oxartnum = new oxField('123');

        $oEncoder = $this->getMock('oxSeoEncoderArticle', array('_loadFromDb', "_getCategory"));
        $oEncoder->expects($this->once())->method('_getCategory')->will($this->returnValue($oCategory));
        $oEncoder->expects($this->once())->method('_loadFromDb')->will($this->returnValue(false));

        $this->assertEquals("en/" . oxRegistry::get("oxSeoEncoder")->UNITprepareTitle($oCategory->oxcategories__oxtitle->value) . "/123.html", $oEncoder->getArticleUri($oArticle, 1));
    }

    public function testGetArticleUriVariantWithCategory()
    {
        $oCategory = $this->getMock("oxCategory", array("isPriceCategory"));
        $oCategory->expects($this->any())->method('isPriceCategory')->will($this->returnValue(false));
        $oCategory->load(oxDb::getDb()->getOne("select oxid from oxcategories where oxparentid = 'oxrootid'"));

        $oArticle = $this->getMock("oxarticle", array("inCategory"));
        $oArticle->expects($this->once())->method('inCategory')->will($this->returnValue(true));
        $oArticle->load('8a142c410f55ed579.98106125');
        $sUrl = oxRegistry::get("oxSeoEncoder")->UNITprepareTitle($oCategory->oxcategories__oxtitle->value) . '/Tischlampe-SPHERE-rot.html';

        $oEncoder = $this->getMock('oxSeoEncoderArticle', array('_loadFromDb', "_getCategory"));
        $oEncoder->expects($this->once())->method('_getCategory')->will($this->returnValue($oCategory));
        $oEncoder->expects($this->once())->method('_loadFromDb')->will($this->returnvalue(false));

        $sSeoUrl = $oEncoder->getArticleUri($oArticle, 0);
        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testEncodeArtUrlvariantWithCategoryInEnglish()
    {
        oxRegistry::get("oxSeoEncoder")->setSeparator('+');

        $oCategory = $this->getMock("oxCategory", array("isPriceCategory"));
        $oCategory->expects($this->any())->method('isPriceCategory')->will($this->returnValue(false));
        $oCategory->loadInLang(1, oxDb::getDb()->getOne("select oxid from oxcategories where oxparentid = 'oxrootid'"));

        $oArticle = $this->getMock("oxarticle", array("inCategory"));
        $oArticle->expects($this->once())->method('inCategory')->will($this->returnValue(true));

        $oArticle->loadInLang(1, '8a142c410f55ed579.98106125');
        $sUrl = "en/" . oxRegistry::get("oxSeoEncoder")->UNITprepareTitle($oCategory->oxcategories__oxtitle->value) . "/Table+Lamp+SPHERE+red.html";

        $oEncoder = $this->getMock('modSeoEncoderArticle', array('_loadFromDb', "_getCategory"));
        $oEncoder->expects($this->once())->method('_getCategory')->will($this->returnValue($oCategory));
        $oEncoder->expects($this->any())->method('_loadFromDb')->will($this->returnvalue(false));
        $oEncoder->setSeparator('+');

        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');
        oxDb::getDb()->execute('delete from oxseohistory');

        $this->assertEquals($sUrl, $oEncoder->getArticleUri($oArticle, 1));
    }

    public function testGetArticleUriVariantWithCategoryWithLangParam()
    {
        $oCategory = $this->getMock("oxCategory", array("isPriceCategory"));
        $oCategory->expects($this->any())->method('isPriceCategory')->will($this->returnValue(false));
        $oCategory->load(oxDb::getDb()->getOne("select oxid from oxcategories where oxparentid = 'oxrootid'"));

        $oArticle = $this->getMock("oxarticle", array("inCategory"));
        $oArticle->expects($this->once())->method('inCategory')->will($this->returnValue(true));
        $oArticle->loadInLang(1, '8a142c410f55ed579.98106125');
        $sUrl = oxRegistry::get("oxSeoEncoder")->UNITprepareTitle($oCategory->oxcategories__oxtitle->value) . '/Tischlampe-SPHERE-rot.html';

        $oEncoder = $this->getMock('oxSeoEncoderArticle', array('_loadFromDb', "_getCategory"));
        $oEncoder->expects($this->once())->method('_getCategory')->will($this->returnValue($oCategory));
        $oEncoder->expects($this->any())->method('_loadFromDb')->will($this->returnvalue(false));

        $this->assertEquals($sUrl, $oEncoder->getArticleUri($oArticle, 0));
    }

    public function testEncodeArtUrlvariantWithCategoryInEnglishWithLangParam()
    {
        oxRegistry::get("oxSeoEncoder")->setSeparator('+');
        $oCategory = $this->getMock("oxCategory", array("isPriceCategory"));
        $oCategory->expects($this->any())->method('isPriceCategory')->will($this->returnValue(false));
        $oCategory->loadInLang(1, oxDb::getDb()->getOne("select oxid from oxcategories where oxparentid = 'oxrootid'"));

        $oArticle = $this->getMock("oxarticle", array("inCategory"));
        $oArticle->expects($this->once())->method('inCategory')->will($this->returnValue(true));
        $oArticle->loadInLang(0, '8a142c410f55ed579.98106125');
        $sUrl = "en/" . oxRegistry::get("oxSeoEncoder")->UNITprepareTitle($oCategory->oxcategories__oxtitle->value) . "/Table+Lamp+SPHERE+red.html";

        $oEncoder = $this->getMock('modSeoEncoderArticle', array('_loadFromDb', "_getCategory"));
        $oEncoder->expects($this->once())->method('_getCategory')->will($this->returnValue($oCategory));
        $oEncoder->expects($this->any())->method('_loadFromDb')->will($this->returnvalue(false));
        $oEncoder->setSeparator('+');

        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');
        oxDb::getDb()->execute('delete from oxseohistory');

        $this->assertEquals($sUrl, $oEncoder->getArticleUri($oArticle, 1));
    }

    /**
     * Test case:
     * wrong article seo url preparation, must be
     *  Bierspiel-OANS-ZWOA-GSUFFA
     * but returns
     *  de/Spiele/Brettspiele/Bierspiel-OANS-ZWOA-GSUFFA-...
     */
    public function testGetArticleSeoLinkDe()
    {
        $oArticle = new oxarticle();

        $oArticle->loadInLang(0, '1127');
        $sExp = "Blinkende-Eiswuerfel-FLASH";

        $oEncoder = oxRegistry::get("oxSeoEncoderArticle");
        $oEncoder->setSeparator();
        $this->assertEquals($sExp, $oEncoder->UNITprepareTitle($oArticle->oxarticles__oxtitle->value));
    }


    /**
     * Test case:
     * article was saved, but title was left the same, testing if seo url is kept the same
     */
    public function testActicleIsSavedSeoUrlShouldStayTheSame()
    {
        $sArtId = '1131';

        $oArticle = new oxarticle();
        $oArticle->load($sArtId);
        $oArticle->save();
        $sSeoUrl = $oArticle->getLink();

        $oArticle = new oxarticle();
        $oArticle->load($sArtId);

        $this->assertEquals($sSeoUrl, $oArticle->getLink(), "This test fails probably because of _getUniqueSeoUrl problems");
    }

    /**
     * Test case:
     * new article with same title, seo url must contain "-oxid" prefix
     */
    public function testAddinNewArticleWithSameTitle()
    {
        $oArticle = new oxbase();
        $oArticle->init('oxarticles');
        $oArticle->load('2363');
        $oArticle->oxarticles__oxtitle = new oxField(" testa");
        $oArticle->setId('testa');
        $oArticle->save();

        $oArticle = new oxarticle();
        $oArticle->load('testa');
        $sSeoUrl = $oArticle->getLink();

        $oArticle = new oxbase();
        $oArticle->init('oxarticles');
        $oArticle->load('testa');
        $oArticle->setId('testb');
        $oArticle->save();

        $oArticle = new oxarticle();
        $oArticle->load('testb');
        $sNewSeoUrl = $oArticle->getLink();

        $this->assertNotEquals($sSeoUrl, $sNewSeoUrl);
    }


    public function testonDeleteArticle()
    {
        $sShopId = oxRegistry::getConfig()->getBaseShopId();
        $oDb = oxDb::getDb();
        $sQ = "insert into oxseo
                   ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams )
               values
                   ( 'article_id', '132', '{$sShopId}', '0', '', '', 'oxarticle', '0', '0', '' )";
        $oDb->execute($sQ);

        $sQ = "insert into oxobject2seodata ( oxobjectid, oxshopid, oxlang ) values ( 'article_id', '{$sShopId}', '0' )";
        $oDb->execute($sQ);

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'article_id'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'article_id'"));

        $oArticle = new oxbase();
        $oArticle->setId('article_id');

        $oEncoder = new oxSeoEncoderArticle();
        $oEncoder->onDeleteArticle($oArticle);

        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'article_id'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'article_id'"));
    }

    public function testCreateArticleCategoryUri()
    {
        oxTestModules::addFunction('oxSeoEncoderCategory', 'getCategoryUri($c, $l = NULL, $blRegenerate = false)', '{return "caturl".$c->getId().$l;}');
        $oA = $this->getMock('oxarticle', array('getLanguage', 'getId', 'getBaseStdLink'));
        $oA->expects($this->never())->method('getLanguage');
        $oA->expects($this->any())->method('getId')->will($this->returnValue('articleId'));
        $oA->expects($this->any())->method('getBaseStdLink')->with(
            $this->equalTo(1)
        )->will($this->returnValue('articleBaseStdLink'));

        $oUtilsUrl = $this->getMock('oxutilsurl', array('appendUrl'));
        $oUtilsUrl->expects($this->any())->method('appendUrl')->with(
            $this->equalTo('articleBaseStdLink'),
            $this->equalTo(array('cnid' => 'catId'))
        )->will($this->returnValue('articleStdLink'));
        oxTestModules::addModuleObject('oxUtilsUrl', $oUtilsUrl);

        $oC = $this->getMock('oxcategory', array('getId'));
        $oC->expects($this->any())->method('getId')->will($this->returnValue('catId'));

        $oSEA = $this->getMock('oxSeoEncoderArticle', array('_getProductForLang', '_prepareArticleTitle', '_processSeoUrl', '_saveToDb'));
        $oSEA->expects($this->once())->method('_getProductForLang')->with($this->equalTo($oA), $this->equalTo(1))->will($this->returnValue($oA));
        $oSEA->expects($this->once())->method('_prepareArticleTitle')->with($this->equalTo($oA))->will($this->returnValue('articleTitle'));
        $oSEA->expects($this->once())->method('_processSeoUrl')->with(
            $this->equalTo("caturlcatId1articleTitle"),
            $this->equalTo('articleId'),
            $this->equalTo(1)
        )->will($this->returnValue('articleUrlReturned'));
        $oSEA->expects($this->once())->method('_saveToDb')->with(
            $this->equalTo("oxarticle"),
            $this->equalTo('articleId'),
            $this->equalTo("articleStdLink"),
            $this->equalTo('articleUrlReturned'),
            $this->equalTo(1),
            $this->equalTo(null),
            $this->equalTo(0),
            $this->equalTo('catId')
        )->will($this->returnValue(null));

        $this->assertEquals('articleUrlReturned', $oSEA->UNITcreateArticleCategoryUri($oA, $oC, 1));
    }

    public function testGetArticleMainUrl()
    {
        $oA = $this->getMock('oxarticle', array('getLanguage'));
        $oA->expects($this->any())->method('getLanguage')->will($this->returnValue(1));

        $oSEA = $this->getMock('oxSeoEncoderArticle', array('_getFullUrl', 'getArticleMainUri'));
        $oSEA->expects($this->once())->method('getArticleMainUri')
            ->with(
                $this->equalTo($oA),
                $this->equalTo(1)
            )->will($this->returnValue('articleUri'));
        $oSEA->expects($this->once())->method('_getFullUrl')
            ->with(
                $this->equalTo('articleUri'),
                $this->equalTo(1)
            )->will($this->returnValue('articleUrlReturned'));

        $this->assertEquals('articleUrlReturned', $oSEA->getArticleMainUrl($oA));
    }

    /**
     * oxSeoEncoderArticle::getArticleUri() test case
     *
     * @return null
     */
    public function testGetArticleUriForMainCategory()
    {
        $sProdId = "1126";
        $sCatId = oxDb::getDb()->getOne("select oxcatnid from oxobject2category where oxobjectid = '{$sProdId}'");

        $oProduct = new oxArticle();
        $oProduct->load($sProdId);

        $oCategory = new oxCategory();
        $oCategory->load($sCatId);
        $sBaseUri = str_replace(oxRegistry::getConfig()->getConfigParam("sShopURL"), "", $oCategory->getLink());

        $oEncoder = $this->getMock('oxSeoEncoderArticle', array('_getCategory', '_getMainCategory', '_loadFromDb'));
        $oEncoder->expects($this->once())->method('_getCategory')->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('_loadFromDb')->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('_getMainCategory')->will($this->returnValue($oCategory));
        $this->assertEquals($sBaseUri . "Bar-Set-ABSINTH.html", $oEncoder->getArticleUri($oProduct, 0));
    }

    /**
     * Test case for #0002564: seo urls of variant-members without title and
     * varselect get generic postfix instead of artnum
     *
     * @return null
     */
    public function testFor0002564()
    {
        $sParentId = "1126";

        $oProduct1 = new oxArticle();
        $oProduct1->setId("_testVar1");
        $oProduct1->oxarticles__oxartnum = new oxField("artnum1");
        $oProduct1->oxarticles__oxparentid = new oxField($sParentId);
        $oProduct1->save();

        $oProduct2 = new oxArticle();
        $oProduct2->setId("_testVar2");
        $oProduct2->oxarticles__oxartnum = new oxField("artnum2");
        $oProduct2->oxarticles__oxparentid = new oxField($sParentId);
        $oProduct2->save();

        $oProduct3 = new oxArticle();
        $oProduct3->setId("_testVar3");
        $oProduct3->oxarticles__oxartnum = new oxField("artnum3");
        $oProduct3->oxarticles__oxparentid = new oxField($sParentId);
        $oProduct3->save();

        $oProduct4 = new oxArticle();
        $oProduct4->setId("_testVar4");
        $oProduct4->oxarticles__oxartnum = new oxField("artnum4");
        $oProduct4->oxarticles__oxparentid = new oxField($sParentId);
        $oProduct4->save();

        $oEncoder = new oxSeoEncoderArticle();
        $sLink = $oEncoder->getArticleUri($oProduct1, 0);
        $this->assertTrue((bool) $sLink);
        $this->assertEquals("Bar-Set-ABSINTH-artnum1.html", basename($sLink));

        $oEncoder = new oxSeoEncoderArticle();
        $sLink = $oEncoder->getArticleUri($oProduct2, 0);
        $this->assertTrue((bool) $sLink);
        $this->assertEquals("Bar-Set-ABSINTH-artnum2.html", basename($sLink));

        $oEncoder = new oxSeoEncoderArticle();
        $sLink = $oEncoder->getArticleUri($oProduct3, 0);
        $this->assertTrue((bool) $sLink);
        $this->assertEquals("Bar-Set-ABSINTH-artnum3.html", basename($sLink));

        $oEncoder = new oxSeoEncoderArticle();
        $sLink = $oEncoder->getArticleUri($oProduct4, 0);
        $this->assertTrue((bool) $sLink);
        $this->assertEquals("Bar-Set-ABSINTH-artnum4.html", basename($sLink));
    }
}
