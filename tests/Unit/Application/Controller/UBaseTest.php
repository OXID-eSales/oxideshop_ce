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
namespace Unit\Application\Controller;

use OxidEsales\EshopCommunity\Application\Model\ActionList;
use oxUBaseHelper;
use OxidEsales\EshopCommunity\Application\Model\User;
use \stdClass;
use \oxField;
use \exception;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

if (!function_exists('getSeoProcType')) {
    function getSeoProcType()
    {
        return Unit_oxubaseTest::$iProcType;
    }
}

require_once TEST_LIBRARY_HELPERS_PATH . 'oxUBaseHelper.php';

/**
 * Testing oxUBase class
 */
class UBaseTest extends \OxidTestCase
{

    protected $_sRequestMethod = null;
    protected $_sRequestUri = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        oxUBaseHelper::resetComponentNames();

        // adding article to recommendList
        $sQ = 'replace into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $this->getConfig()->getShopId() . '" ) ';
        oxDb::getDB()->Execute($sQ);

        parent::setUp();

        // backup
        $this->_sRequestMethod = $_SERVER["REQUEST_METHOD"];
        $this->_sRequestUri = $_SERVER['REQUEST_URI'];
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        // restoring
        $_SERVER["REQUEST_METHOD"] = $this->_sRequestMethod;
        $_SERVER['REQUEST_URI'] = $this->_sRequestUri;

        oxDb::getDb()->execute('delete from oxrecommlists where oxid like "testlist%" ');
        oxDb::getDb()->execute('delete from oxseologs ');
        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');

        oxDb::getDb()->execute('delete from oxcontents where oxloadid = "_testKeywordsIdentId" ');

        $oUBase = oxNew('oxUbase');
        $oUBase->getSession()->setBasket(null);

        oxUBaseHelper::resetComponentNames();

        parent::tearDown();
    }

    /**
     * Test case for oxUBase::_getComponentNames()
     *
     * @return null
     */
    public function testGetComponentNames()
    {
        $sCmpName = "testCmp" . time();
        eval("class {$sCmpName} extends oxUbase {}");

        $this->setConfigParam('aUserComponentNames', array($sCmpName => 1));

        $aComponentNames = array(
            'oxcmp_user'       => 1, // 0 means don't init if cached
            'oxcmp_lang'       => 0,
            'oxcmp_cur'        => 1,
            'oxcmp_shop'       => 1,
            'oxcmp_categories' => 0,
            'oxcmp_utils'      => 1,
            'oxcmp_news'       => 0,
            'oxcmp_basket'     => 1,
            $sCmpName          => 1
        );
        $oView = oxNew('oxUBase');
        $this->assertEquals($aComponentNames, $oView->UNITgetComponentNames());
    }

    /**
     * oxUBase::isActive() test case
     */
    public function testIsActive()
    {
        $this->setConfigParam("blSomethingEnabled", true);
        $oView = oxNew('oxUbase');
        $this->assertTrue($oView->isActive("Something"));
        $this->assertNull($oView->isActive("Nothing"));
    }

    /**
     * Getting view values
     */
    public function testGetActiveRecommList()
    {
        $this->setRequestParameter('recommid', 'testlist');
        $oView = oxNew('oxUbase');
        $this->assertEquals('testlist', $oView->getActiveRecommList()->getId());
    }

    public function testGetCanonicalUrl()
    { // just check if function exists and returns null
        $o = oxNew('oxUBase');
        $this->assertSame(null, $o->getCanonicalUrl());
    }

    public function testSetGetManufacturerTree()
    {
        $oUBase = oxNew('oxUBase');
        $oUBase->setManufacturerTree('oManufacturerTree');
        $this->assertEquals('oManufacturerTree', $oUBase->getManufacturerTree());
    }

    public function testGetActSearch()
    {
        $oSearch = new stdClass();
        $oSearch->link = $this->getConfig()->getShopHomeURL() . "cl=search";

        $oUBase = oxNew('oxUBase');
        $this->assertEquals($oSearch, $oUBase->getActSearch());
    }

    public function testSetGetActManufacturer()
    {
        $oUBase = oxNew('oxUBase');
        $oUBase->setActManufacturer('oActManufacturer');
        $this->assertEquals('oActManufacturer', $oUBase->getActManufacturer());
    }

    public function testSetGetViewProduct()
    {
        $oUBase = oxNew('oxUBase');
        $oUBase->setViewProduct('oProduct');
        $this->assertNull($oUBase->getViewProduct());
    }

    public function testGetViewProductList()
    {
        $oUBase = $this->getProxyClass('oxUBase');
        $oUBase->setNonPublicVar('_aArticleList', 'aArticleList');
        $this->assertEquals('aArticleList', $oUBase->getViewProductList());
    }

    public function testGetActManufacturerRoot()
    {
        $this->setRequestParameter('mnid', 'root');

        $oUBase = oxNew('oxUBase');
        $oM = oxNew('oxManufacturer');
        $oM->load('root');
        $this->assertEquals($oM, $oUBase->getActManufacturer());
    }

    public function testGetActManufacturer()
    {
        $sId = $this->getTestConfig()->getShopEdition() == 'EE' ? '88a996f859f94176da943f38ee067984' : 'fe07958b49de225bd1dbc7594fb9a6b0';
        $this->setRequestParameter('mnid', $sId);

        $oUBase = oxNew('oxUBase');
        $oMan = $oUBase->getActManufacturer();
        $this->assertTrue($oMan !== false);
        $this->assertEquals($sId, $oMan->getId());
    }

    public function testGetActVendorRoot()
    {
        $this->setRequestParameter('cnid', 'v_root');

        $oUBase = oxNew('oxUBase');
        $oV = oxNew('oxVendor');
        $oV->load('root');
        $this->assertEquals($oV, $oUBase->getActVendor());
    }

    public function testGetActVendor__()
    {
        $sId = $this->getTestConfig()->getShopEdition() == 'EE' ? 'v_d2e44d9b31fcce448.08890330' : 'v_68342e2955d7401e6.18967838';
        $this->setRequestParameter('cnid', $sId);

        $oUBase = oxNew('oxUBase');
        $oVnd = $oUBase->getActVendor();
        $this->assertTrue($oVnd !== false);
        $this->assertEquals(str_replace('v_', '', $sId), $oVnd->getId());
    }

    public function testSetGetActVendor()
    {
        $oUBase = oxNew('oxUBase');
        $oUBase->setActVendor('oActVendor');
        $this->assertEquals('oActVendor', $oUBase->getActVendor());
    }

    public function testSetGetCategoryTree()
    {
        $oUBase = oxNew('oxUBase');
        $oUBase->setCategoryTree('oCategoryTree');
        $this->assertEquals('oCategoryTree', $oUBase->getCategoryTree());
    }

    public function testGetCatTreePath()
    {
        $oUBase = $this->getProxyClass('oxubase');
        $oUBase->setNonPublicVar('_sCatTreePath', 'scattreepath');

        $this->assertEquals('scattreepath', $oUBase->getCatTreePath());
    }

    public function testGetManufacturerId()
    {
        // active manufacturer is not set
        $oUBase = $this->getMock('oxubase', array('getActManufacturer'));
        $oUBase->expects($this->once())->method('getActManufacturer')->will($this->returnValue(null));
        $this->assertFalse($oUBase->getManufacturerId());

        // active manufacturer was set
        $oManufacturer = $this->getMock('oxmanufacturer', array('getId'));
        $oManufacturer->expects($this->once())->method('getId')->will($this->returnValue('someid'));

        $oUBase = $this->getMock('oxubase', array('getActManufacturer'));
        $oUBase->expects($this->once())->method('getActManufacturer')->will($this->returnValue($oManufacturer));
        $this->assertEquals('someid', $oUBase->getManufacturerId());
    }

    public function testGetSetRootManufacturer()
    {
        $oUbase = oxNew('oxubase');
        $oUbase->setRootManufacturer('sRootManufacturer');
        $this->assertEquals('sRootManufacturer', $oUbase->getRootManufacturer());
    }

    public function testGetSetManufacturerlist()
    {
        $oUbase = oxNew('oxubase');
        $oUbase->setManufacturerlist('aManufacturerlist');
        $this->assertEquals('aManufacturerlist', $oUbase->getManufacturerlist());
    }

    /*
     * Test getting view ID without some params
     */
    public function testGetViewId()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

        $oView = oxNew('oxUBase');
        $this->assertEquals("ox|0|0|0|0", $oView->getViewId());

        // and caching
        oxRegistry::getLang()->setBaseLanguage(1);
        $this->assertEquals("ox|0|0|0|0", $oView->getViewId());
    }

    /*
     * Test getting view ID with some additional params
     */
    public function testGetViewIdWithOtherParams()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

        oxRegistry::getLang()->setBaseLanguage(1);
        $this->setRequestParameter('currency', '1');
        $this->setRequestParameter('cl', 'details');
        $this->setRequestParameter('fnc', 'dsd');
        $this->setSessionParam("usr", 'oxdefaultadmin');

        $oView = oxNew('oxUBase');
        $sId = $oView->getViewId();

        $this->assertEquals("ox|1|1|0|0", $sId);
    }

    /*
     * Test getting view ID with SSL enabled
     */
    public function testGetViewIdWithSSL()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

        $myConfig = $this->getConfig();
        $myConfig->setIsSsl(true);

        $oView = oxNew('oxUBase');
        $sId = $oView->getViewId();

        $this->assertEquals("ox|0|0|0|0|ssl", $sId);
    }

    public function testGetMetaDescriptionForStartView()
    {
        $sVal = 'Alles zum Thema Wassersport, Sportbekleidung und Mode. Umfangreiches Produktsortiment mit den neusten Trendprodukten. Blitzschneller Versand.';
        $oView = oxNew('start');

        $this->assertEquals($sVal, $oView->getMetaDescription());
    }

    public function testGetMetaKeywordsForStartView()
    {
        $oContent = oxNew('oxcontent');
        $oContent->loadByIdent('oxstartmetakeywords');

        $oView = oxNew('start');
        $this->assertEquals(strip_tags($oContent->oxcontents__oxcontent->value), strip_tags($oView->getMetaKeywords()));
    }

    /*
     * Checking if method does not removes duplicated words if meta keywords
     * are loaded from oxContent table by ident (M:844)
     */
    public function testGetMetaKeywordsDoesNotRemovesDuplicatedWords()
    {
        $oContent = oxNew('oxcontent');
        $oContent->oxcontents__oxloadid = new oxField('_testKeywordsIdentId');
        $oContent->oxcontents__oxcontent = new oxField('online shop, cool stuff, stuff, buy');
        $oContent->oxcontents__oxactive = new oxField(1);
        $oContent->save();

        $sKeywords = $oContent->oxcontents__oxcontent->value;

        $oView = $this->getProxyClass('oxubase');
        $oView->setNonPublicVar('_sMetaKeywordsIdent', '_testKeywordsIdentId');

        $this->assertEquals($sKeywords, $oView->getMetaKeywords());
    }

    /*
     * Testing initiating components
     */
    public function testInitComponents()
    {
        $oView = $this->getProxyClass('oxubase');
        $oView->setNonPublicVar('_aComponentNames', array("oxcmp_lang" => false));
        $oView->init();

        $aComponents = $oView->getComponents();
        $this->assertEquals(1, count($aComponents));
        $this->assertEquals('oxidesales\eshopcommunity\application\component\languagecomponent', $aComponents["oxcmp_lang"]->getThisAction());
        $this->assertEquals("oxubaseproxy", $aComponents["oxcmp_lang"]->getParent()->getThisAction());
    }

    /*
     * Testing initiating components
     */
    public function testInitUserDefinedComponents()
    {
        $oView = $this->getMock('oxubase', array("_getComponentNames"));
        $oView->expects($this->once())->method('_getComponentNames')->will($this->returnValue(array("oxcmp_cur" => false, "oxcmp_lang" => false)));
        $oView->init();

        $aComponents = $oView->getComponents();
        $this->assertEquals(2, count($aComponents));
        $this->assertEquals('oxidesales\eshopcommunity\application\component\languagecomponent', $aComponents["oxcmp_lang"]->getThisAction());
        $this->assertEquals('oxidesales\eshopcommunity\application\component\currencycomponent', $aComponents["oxcmp_cur"]->getThisAction());
        $this->assertEquals(strtolower(get_class($oView)), $aComponents["oxcmp_lang"]->getParent()->getThisAction());
    }

    /*
     * Testing initiating components when view is component
     */
    public function testIniOfComponent()
    {
        $oView = $this->getMock("oxview", array('addGlobalParams'));
        $oView->expects($this->never())->method('addGlobalParams');
        $oView->setIsComponent(true);
        $oView->init();
    }

    /*
     * Test rendering components
     */
    public function testRender()
    {
        $this->getConfig()->setConfigParam('blDisableNavBars', true);
        $oView = $this->getMock('oxubase', array('getIsOrderStep'));

        $oView->expects($this->once())->method('getIsOrderStep')->will($this->returnValue(true));

        $oView->render();
    }

    /*
     * Test _setNrOfArtPerPage()
     */
    public function testSetNrOfArtPerPage()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam('iNrofCatArticles', 10);
        $myConfig->setConfigParam('aNrofCatArticles', 'xxx');

        $myConfig->setConfigParam('iNrofSearchArticles', 15);
        $myConfig->setConfigParam('aNrofSearchArticles', 'xxx');

        $oView = oxNew('oxubase');
        $oView->UNITsetNrOfArtPerPage();

        $oViewConf = $oView->getViewConfig();
        $this->assertEquals(10, $oViewConf->getViewConfigParam('iartPerPage'));
        $this->assertEquals(10, $myConfig->getConfigParam('iNrofCatArticles'));
        $this->assertEquals(array(10), $myConfig->getConfigParam('aNrofCatArticles'));
    }

    /*
     * Test _setNrOfArtPerPage()
     */
    public function testSetNrOfArtPerPageSetToSessionWithWrongNumber()
    {
        $myConfig = $this->getConfig();
        $this->setRequestParameter('_artperpage', 200);

        $oView = oxNew('oxubase');
        $oView->UNITsetNrOfArtPerPage();

        $iCnt = $this->getSessionParam("_artperpage");

        $oViewConf = $oView->getViewConfig();
        $this->assertEquals(10, $oViewConf->getViewConfigParam('iartPerPage'));
        $this->assertEquals(10, $myConfig->getConfigParam('iNrofCatArticles'));
        $this->assertEquals(10, $iCnt);
    }

    /*
      * Test _setNrOfArtPerPage() with missing parameter aNrofCatArticles in database
      */
    public function testSetNrOfArtPerPageWhenConfigParamIsMissing()
    {
        $config = $this->getConfig();
        $config->setConfigParam('iNrofCatArticles', 10);
        $config->setConfigParam('aNrofCatArticles', null);
        $this->setSessionParam("_artperpage", 20);

        $oView = oxNew('oxubase');
        $oView->UNITsetNrOfArtPerPage();

        $this->assertEquals(10, $config->getConfigParam('iNrofCatArticles'));
        $this->assertEquals(array(10), $config->getConfigParam('aNrofCatArticles'));
    }

    /*
     * Test _setNrOfArtPerPage() sets articles per page in session
     */
    public function testSetNrOfArtPerPageToSession()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam('aNrofCatArticles', array(0 => 30));
        $this->setRequestParameter('_artperpage', 30);

        $oView = oxNew('oxubase');
        $oView->UNITsetNrOfArtPerPage();

        $iCnt = $this->getSessionParam("_artperpage");

        $oViewConf = $oView->getViewConfig();
        $this->assertEquals(30, $oViewConf->getViewConfigParam('iartPerPage'));
        $this->assertEquals(30, $myConfig->getConfigParam('iNrofCatArticles'));
        $this->assertEquals(30, $iCnt);
    }

    /*
     * Test _setNrOfArtPerPage() for calculating uses value set in session
     */
    public function testSetNrOfArtPerPageFromSession()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam('aNrofCatArticles', array(0 => 26));
        $this->setSessionParam("_artperpage", 26);

        $oView = oxNew('oxubase');
        $oView->UNITsetNrOfArtPerPage();

        $iCnt = $this->getSessionParam("_artperpage");

        $oViewConf = $oView->getViewConfig();
        $this->assertEquals(26, $oViewConf->getViewConfigParam('iartPerPage'));
        $this->assertEquals(26, $myConfig->getConfigParam('iNrofCatArticles'));
        $this->assertEquals(26, $iCnt);
    }

    /*
     * Test _setNrOfArtPerPage() without params
     */
    public function testSetNrOfArtPerPageWithoutParams()
    {
        $myConfig = $this->getConfig();

        $myConfig->setConfigParam('iNrofCatArticles', null);
        $myConfig->setConfigParam('aNrofCatArticles', null);

        $oView = oxNew('oxubase');
        $oView->UNITsetNrOfArtPerPage();

        $iCnt = $this->getSessionParam("_artperpage");

        $oViewConf = $oView->getViewConfig();
        $this->assertEquals(10, $oViewConf->getViewConfigParam('iartPerPage'));
        $this->assertEquals(10, $myConfig->getConfigParam('iNrofCatArticles'));
        $this->assertEquals(null, $iCnt);
    }

    /*
     * Test _setNrOfArtPerPage() without params
     */
    public function testSetNrOfArtPerPageWithFirstParam()
    {
        $myConfig = $this->getConfig();

        $myConfig->setConfigParam('iNrofCatArticles', null);
        $myConfig->setConfigParam('aNrofCatArticles', array(0 => 2));
        $this->setSessionParam("_artperpage", null);
        $this->setRequestParameter('_artperpage', null);

        $oView = oxNew('oxubase');
        $oView->UNITsetNrOfArtPerPage();

        $oViewConf = $oView->getViewConfig();
        $this->assertEquals(2, $oViewConf->getViewConfigParam('iartPerPage'));
        $this->assertEquals(2, $myConfig->getConfigParam('iNrofCatArticles'));
    }

    /*
     * Test _setNrOfArtPerPage() without params
     */
    public function testSetNrOfArtPerPageWithArtPerPage()
    {
        $myConfig = $this->getConfig();

        $myConfig->setConfigParam('iNrofCatArticles', null);
        $myConfig->setConfigParam('aNrofCatArticles', array(0 => 2));
        $this->setSessionParam("_artperpage", null);
        $this->setRequestParameter('_artperpage', 2);

        $oView = oxNew('oxubase');
        $oView->UNITsetNrOfArtPerPage();

        $oViewConf = $oView->getViewConfig();
        $this->assertEquals(2, $oViewConf->getViewConfigParam('iartPerPage'));
        $this->assertEquals(2, $myConfig->getConfigParam('iNrofCatArticles'));
    }

    /*
     * M45: Possibility to push any "Show articles per page" number parameter
     */
    public function testSetNrOfArtPerPageWithWrongArtPerPage()
    {
        $myConfig = $this->getConfig();

        $myConfig->setConfigParam('iNrofCatArticles', null);
        $myConfig->setConfigParam('aNrofCatArticles', array(0 => 10));
        $this->setSessionParam("_artperpage", null);
        $this->setRequestParameter('_artperpage', 2);

        $oView = oxNew('oxubase');
        $oView->UNITsetNrOfArtPerPage();

        $oViewConf = $oView->getViewConfig();
        $this->assertEquals(10, $oViewConf->getViewConfigParam('iartPerPage'));
        $this->assertEquals(10, $myConfig->getConfigParam('iNrofCatArticles'));
    }

    /*
     * Test setting meta description
     */
    public function testSetMetaDescription()
    {
        $sMeta = 'testValue';
        $oView = oxNew('oxubase');
        $oView->setMetaDescription($sMeta);

        $this->assertEquals($sMeta, $oView->getMetaDescription());
    }

    public function testSetMetaDescriptionWhenSeoIsOn()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
        oxTestModules::addFunction("oxseoencoder", "getMetaData", '{return "xxx";}');

        $oView = $this->getMock('oxubase', array('_prepareMetaDescription', '_getSeoObjectId'));
        $oView->expects($this->never())->method('_prepareMetaDescription');
        $oView->expects($this->once())->method('_getSeoObjectId')->will($this->returnValue(1));
        $oView->setMetaDescription(null);

        $this->assertEquals('xxx', $oView->getMetaDescription());
    }

    public function testSetMetaKeywordsWhenSeoIsOn()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
        oxTestModules::addFunction("oxseoencoder", "getMetaData", '{return "xxx";}');

        $oView = $this->getMock('oxubase', array('_prepareMetaDescription', '_getSeoObjectId'));
        $oView->expects($this->never())->method('_prepareMetaDescription');
        $oView->expects($this->once())->method('_getSeoObjectId')->will($this->returnValue(1));
        $oView->setMetaDescription(null);

        $this->assertEquals('xxx', $oView->getMetaKeywords());
    }

    /*
     * Test preparing meta description - stripping tags
     */
    public function testPrepareMetaDescriptionStripTags()
    {
        $sDesc = '<div>Test  <b>5er</b>  Edelstahl-Messerset.&nbsp;</div>';

        $oView = oxNew('oxubase');
        $sResult = $oView->UNITprepareMetaDescription($sDesc);

        $this->assertEquals("Test 5er Edelstahl-Messerset.", $sResult);
    }

    /*
     * Test preparing meta description - truncating text
     */
    public function testPrepareMetaDescriptionWithLength()
    {
        $sDesc = '<div>Test  5er  Edelstahl-Messerset.&nbsp;';

        $oView = oxNew('oxubase');
        $sResult = $oView->UNITprepareMetaDescription($sDesc, 12, false);

        $this->assertEquals("Test 5er Ede", $sResult);
    }

    /*
     * Test preparing meta description - removing spec. chars
     */
    public function testPrepareMetaDescriptionRemovesSpecChars()
    {
        $sDesc = "&nbsp; \" ' : ! ? \n \r \t 	    ;";

        $oView = oxNew('oxubase');
        $sResult = $oView->UNITprepareMetaDescription($sDesc);

        $this->assertEquals("&quot; &#039; : ! ?", $sResult);
    }

    /*
     * Test preparing meta description - removing spec. chars skips dots and commas
     * (M:844)
     */
    public function testPrepareMetaDescriptionDoesNotRemovesDotsAndCommas()
    {
        $sDesc = "Lady Gaga, Pokerface.";

        $oView = oxNew('oxubase');
        $sResult = $oView->UNITprepareMetaDescription($sDesc);

        $this->assertEquals("Lady Gaga, Pokerface.", $sResult);
    }

    /*
     * Test preparing meta description - removing duplicates
     */
    public function testPrepareMetaDescriptionRemovesDuplicates()
    {
        $sDesc = "aa bb aa cc aa";

        $oView = oxNew('oxubase');
        $sResult = $oView->UNITprepareMetaDescription($sDesc, -1, true);

        $this->assertEquals("aa, bb, cc", $sResult);
    }

    /*
     * Test preparing meta description - not removing duplicates
     */
    public function testPrepareMetaDescriptionNotRemovesDuplicates()
    {
        $sDesc = "aa bb aa cc aa";

        $oView = oxNew('oxubase');
        $sResult = $oView->UNITprepareMetaDescription($sDesc, -1, false);

        $this->assertEquals("aa bb aa cc aa", $sResult);
    }

    /*
     * Test setting meta keywords
     */
    public function testSetMetaKeywords()
    {
        $sKeywords = 'xxx';
        $oView = oxNew('oxubase');
        $oView->setMetaKeywords($sKeywords);

        $this->assertEquals($sKeywords, $oView->getMetaKeywords());
    }

    /*
     * Test prepare meta keywords
     */
    public function testPrepareMetaKeywords()
    {
        $sDesc = '<div>aaa  <b>bbb</b> ,ccc.&nbsp;</div>';

        $oView = oxNew('oxubase');
        $sResult = $oView->UNITprepareMetaKeyword($sDesc);

        $this->assertEquals("aaa, bbb, ccc", $sResult);
    }

    /*
     * Test prepare meta keywords - removing duplicated words
     */
    public function testPrepareMetaKeywordsRemovesDefinedStrings()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam('aSkipTags', array('ccc'));

        $sDesc = 'aaa bbb ccc ddd';

        $oView = oxNew('oxubase');
        $sResult = $oView->UNITprepareMetaKeyword($sDesc);

        $this->assertEquals("aaa, bbb, ddd", $sResult);
    }

    /*
     * Test preparing meta keywords - removing duplicated words and lowercase words
     * (M:844)
     */
    public function testPrepareMetaKeywordsRemovesDotsAndCommas()
    {
        $sDesc = "Lady Gaga, Gaga, Lady, Pokerface.";

        $oView = oxNew('oxubase');
        $sResult = $oView->UNITprepareMetaKeyword($sDesc);

        $this->assertEquals("lady, gaga, pokerface", $sResult);
    }

    /*
 * Test preparing meta keywords - removing spec. chars skips dots and commas
 * and duplicated words
 * (M:844)
 */
    public function testPrepareMetaKeywordsDoesNotRemovesDotsAndCommas()
    {
        $sDesc = "Lady Gaga, Pokerface realy realy...";

        $oView = oxNew('oxubase');
        $sResult = $oView->UNITprepareMetaKeyword($sDesc, false);

        $this->assertEquals("Lady Gaga, Pokerface realy realy...", $sResult);
    }

    /*
     * Test removing duplicated words from string
     */
    public function testsRemoveDuplicatedWords()
    {
        $aIn = array("aaa ccc bbb ccc ddd ccc"                                                                       => "aaa, ccc, bbb, ddd",
            "kuyichi, t-shirt, tiger, bekleidung, fashion, ihn, shirts, &, co., shirt, tiger, organic, men" => "kuyichi, t-shirt, tiger, bekleidung, fashion, ihn, shirts, co, shirt, organic, men",
        );

        $oView = oxNew('oxubase');
        foreach ($aIn as $sIn => $sOut) {
            $this->assertEquals($sOut, $oView->UNITremoveDuplicatedWords($sIn));
        }
    }

    /*
     * Test removing duplicated words from array
     */
    public function testsRemoveDuplicatedWordsFromArray()
    {
        $sDesc = array('aaa', 'ccc', 'bbb', 'ccc', 'ddd', 'ccc');

        $oView = oxNew('oxubase');
        $sResult = $oView->UNITremoveDuplicatedWords($sDesc);

        $this->assertEquals("aaa, ccc, bbb, ddd", $sResult);
    }

    /*
     * Test set/get components array
     */
    public function testSetGetComponents()
    {
        $oView = oxNew('oxUBase');
        $oView->setComponents(array('1a', '2b'));
        $this->assertEquals(array('1a', '2b'), $oView->getComponents());
    }

    /*
     * Test set/get component
     */
    public function testSetGetComponent()
    {
        $oView = oxNew('oxUBase');
        $oView->setComponents(array('a1' => '1a', 'b2' => '2b'));
        $this->assertEquals('1a', $oView->getComponent('a1'));
        $this->assertNull($oView->getComponent('test'));
    }

    /*
     * Test set/get is an order step
     */
    public function testIsOrderStep()
    {
        $oView = oxNew('oxubase');
        $oView->setIsOrderStep('123456789');
        $this->assertEquals('123456789', $oView->getIsOrderStep());
    }

    /*
     * Test adding additional data to _viewData
     */
    public function testSetAdditionalParams()
    {
        $this->setRequestParameter('cnid', 'testCnId');
        $this->setRequestParameter('lang', '1');
        $this->setRequestParameter('searchparam', 'aa');
        $this->setRequestParameter('searchtag', 'testtag');
        $this->setRequestParameter('searchcnid', 'testcat');
        $this->setRequestParameter('searchvendor', 'testvendor');
        $this->setRequestParameter('searchmanufacturer', 'testmanufact');
        $this->setRequestParameter('mnid', 'testid');
        $oView = oxNew('oxubase');
        $oView->setClassName('testClass');
        $myConfig = $this->getMock('oxconfig', array('getActiveView'));
        $myConfig->expects($this->once())
            ->method('getActiveView')
            ->will($this->returnValue($oView));
        $oView->setConfig($myConfig);
        $oView->getAdditionalParams();

        $sAdditionalParams = '';
        if (($sLang = oxRegistry::getLang()->getUrlLang())) {
            $sAdditionalParams = $sLang . "&amp;";
        }
        $sAdditionalParams .= "cl=testClass&amp;searchparam=aa&amp;searchcnid=testcat&amp;searchvendor=testvendor&amp;searchmanufacturer=testmanufact&amp;cnid=testCnId&amp;mnid=testid";
        $this->assertEquals($sAdditionalParams, $oView->getAdditionalParams());
    }

    /*
     * Test AddGlobalParams() calls _setNrOfArtPerPage()
     */
    public function testAddGlobalParamsCallsSetNrOfArtPerPage()
    {
        $oView = $this->getMock('oxubase', array('_setNrOfArtPerPage'));
        $oView->expects($this->once())->method('_setNrOfArtPerPage');

        $oView->addGlobalParams(new stdclass);
    }

    public function testShowSearch()
    {
        $oView = oxNew('oxUbase');
        $this->assertEquals(1, $oView->showSearch());

        $this->setConfigParam('blDisableNavBars', true);

        $oView = oxNew('basket');
        $this->assertEquals(0, $oView->showSearch());
    }

    public function testGetTitleSuffix()
    {
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxtitlesuffix = $this->getMock('oxField', array('__get'));
        $oShop->oxshops__oxtitlesuffix->expects($this->once())->method('__get')->will($this->returnValue('testsuffix'));

        $oConfig = $this->getMock('oxconfig', array('getActiveShop'));
        $oConfig->expects($this->once())->method('getActiveShop')->will($this->returnValue($oShop));

        $oView = $this->getMock('oxubase', array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertEquals('testsuffix', $oView->getTitleSuffix());
    }


    public function testGetTitlePrefix()
    {
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxtitleprefix = $this->getMock('oxField', array('__get'));
        $oShop->oxshops__oxtitleprefix->expects($this->once())->method('__get')->will($this->returnValue('testsuffix'));

        $oConfig = $this->getMock('oxconfig', array('getActiveShop'));
        $oConfig->expects($this->once())->method('getActiveShop')->will($this->returnValue($oShop));

        $oView = $this->getMock('oxubase', array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertEquals('testsuffix', $oView->getTitlePrefix());
    }

    public function testGetSeoRequestParams()
    {
        $oView = $this->getMock('oxubase', array('getClassName', 'getFncName'));
        $oView->expects($this->once())->method('getClassName')->will($this->returnValue('testclass'));
        $oView->expects($this->once())->method('getFncName')->will($this->returnValue('testfnc'));

        $this->setRequestParameter('page', 'testpage');
        $this->setRequestParameter('tpl', 'somedir/testtpl.tpl');
        $this->setRequestParameter('oxloadid', 'testcontent');
        $this->setRequestParameter('pgNr', 100);

        $this->assertEquals('cl=testclass&amp;fnc=testfnc&amp;page=testpage&amp;tpl=testtpl.tpl&amp;oxloadid=testcontent&amp;pgNr=100', $oView->UNITgetSeoRequestParams());
    }

    public function testGetSimilarRecommListIds()
    {
        $oView = oxNew('oxubase');
        $this->assertFalse($oView->getSimilarRecommListIds());
    }

    /*
     * Test set/get sort by
     */
    public function testSetItemSortingGetSortingGetSortingSql()
    {
        $aSorting = array('sortby' => '`oxid`', 'sortdir' => 'asc');

        $oView = oxNew('oxubase');
        $oView->setItemSorting('xxx', '`oxid`', 'asc');

        $this->assertEquals($oView->getDefaultSorting(), $oView->getSorting('yyy'));

        $this->assertEquals($aSorting, $oView->getSorting('xxx'));
        $this->assertEquals(implode(' ', $aSorting), $oView->getSortingSql('xxx'));
    }

    public function testGetListTypeAndSetListType()
    {
        $oView = oxNew('oxubase');
        $this->assertNull($oView->getListType());

        $this->setRequestParameter('listtype', 'xxx');
        $this->assertEquals('xxx', $oView->getListType());

        $this->setRequestParameter('listtype', null);
        $this->assertEquals('xxx', $oView->getListType());

        $oView->setListType('yyy');
        $this->assertEquals('yyy', $oView->getListType());
    }


    public function testAddRssFeed()
    {
        $oView = oxNew('oxubase');
        $oView->addRssFeed('test', 'http://example.com/?force_sid=abc123');
        $a = $oView->getRssLinks();

        $this->assertEquals(
            array(
                0 => array('title' => 'test', 'link' => 'http://example.com/')), $a
        );

        $oView->addRssFeed('testd', 'http://example.com/?test=1', 'iknowthiskey');

        $a = $oView->getRssLinks();
        $this->assertEquals(
            array(
                0              => array('title' => 'test', 'link' => 'http://example.com/'),
                'iknowthiskey' => array('title' => 'testd', 'link' => 'http://example.com/?test=1')), $a
        );
    }

    public function testGetDynUrlParams()
    {
        $oV = oxNew('oxubase');
        $this->setRequestParameter('searchparam', 'sa"');
        $this->setRequestParameter('searchcnid', 'sa"%22');
        $this->setRequestParameter('searchvendor', 'sa%22"');
        $this->setRequestParameter('searchmanufacturer', 'ma%22"');

        $oV->setListType('lalala');
        $this->assertEquals('', $oV->getDynUrlParams());
        $oV->setListType('search');
        $sGot = $oV->getDynUrlParams();
        $this->assertEquals('&amp;listtype=search&amp;searchparam=sa%22&amp;searchcnid=sa%22%22&amp;searchvendor=sa%22%22&amp;searchmanufacturer=ma%22%22', $sGot);
    }

    /**
     * Bug 6233 test case.
     */
    public function testGetLinkTransfersThirdParameter()
    {
        $languageId = 2;
        $activePage = 10;

        $baseView = $this->getMock('oxubase', array('getBaseLink', 'getActPage', '_addPageNrParam'));
        $baseView->expects($this->once())->method('getActPage')->will($this->returnValue($activePage));
        $baseView->expects($this->once())->method('getBaseLink')->with($this->equalTo($languageId))->will($this->returnValue('link'));
        $baseView->expects($this->once())->method('_addPageNrParam')->with($this->equalTo('link'), $this->equalTo($activePage), $this->equalTo($languageId));
        $baseView->getLink($languageId);
    }

    public function testGetLinkWithDefinedPageNumber()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blSeoMode', false);

        $oV = $this->getMock('oxubase', array('_getRequestParams', 'getActPage'));
        $oV->expects($this->any())->method('_getRequestParams')->will($this->returnValue('req'));
        $oV->expects($this->once())->method('getActPage')->will($this->returnValue(false));

        $this->assertEquals($oConfig->getShopCurrentURL(0) . 'req', $oV->getLink());

        $oV = $this->getMock('oxubase', array('_getRequestParams', 'getActPage', '_addPageNrParam'));
        $oV->expects($this->any())->method('_getRequestParams')->will($this->returnValue('req'));
        $oV->expects($this->once())->method('getActPage')->will($this->returnValue(16));
        $oV->expects($this->once())->method('_addPageNrParam')->with($this->equalTo($oConfig->getShopCurrentURL(0) . 'req&amp;lang=2', 16, 2))->will($this->returnValue('linkas'));

        $this->assertEquals('linkas', $oV->getLink(2));
    }

    public function testGetLink_SeoIsOnProductPageFromCategoryList()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blSeoMode', true);

        $oV = $this->getMock('oxubase', array('_getRequestParams', '_getSubject'));

        $articleId = '1126';
        $sExp = "Geschenke/Bar-Equipment/Bar-Set-ABSINTH.html";
        $sExpEng = "en/Gifts/Bar-Equipment/Bar-Set-ABSINTH.html";
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $articleId = '1889';
            $sExp = "Spiele/Brettspiele/Bierspiel-OANS-ZWOA-GSUFFA.html";
            $sExpEng = "en/Games/Boardgames/Beergame-OANS-ZWOA-GSUFFA.html";
        }
        $oArt = oxNew('oxArticle');
        $oArt->loadInLang(1, $articleId);

        $oV->expects($this->any())->method('_getSubject')->will($this->returnValue($oArt));

        $this->assertEquals($oConfig->getShopURL() . $sExp, $oV->getLink());
        $this->assertEquals($oConfig->getShopURL() . $sExp, $oV->getLink(0));
        $this->assertEquals($oConfig->getShopURL() . $sExpEng, $oV->getLink(1));
    }

    public function testGetLink_SeoIsOnProductPageFromManufacturerList()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blSeoMode', true);

        $oV = $this->getMock('oxUBase', array('_getRequestParams', '_getSubject'));

        $articleId = '1964';
        $sVndExp = "Nach-Hersteller/Bush/Original-BUSH-Beach-Radio.html";
        $sVndExpEng = "en/By-manufacturer/Bush/Original-BUSH-Beach-Radio.html";
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $articleId = '1889';
            $sVndExp = "Nach-Hersteller/Hersteller-2/Bierspiel-OANS-ZWOA-GSUFFA.html";
            $sVndExpEng = "en/By-manufacturer/Manufacturer-2/Beergame-OANS-ZWOA-GSUFFA.html";
        }

        $oArt = oxNew('oxArticle');
        $oArt->setLinkType(OXARTICLE_LINKTYPE_MANUFACTURER);
        $oArt->loadInLang(1, $articleId);

        $oV->expects($this->any())->method('_getSubject')->will($this->returnValue($oArt));

        $this->assertEquals($oConfig->getShopURL() . $sVndExp, $oV->getLink());
        $this->assertEquals($oConfig->getShopURL() . $sVndExp, $oV->getLink(0));
        $this->assertEquals($oConfig->getShopURL() . $sVndExpEng, $oV->getLink(1));
    }

    public function testGetLink_SeiIsOnPageWithoutSeoURL()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blSeoMode', true);

        $oV = $this->getMock('oxubase', array('_getRequestParams'));
        $oV->expects($this->any())->method('_getRequestParams')->will($this->returnValue('req'));

        $this->assertEquals($oConfig->getShopCurrentURL(0) . 'req', $oV->getLink());
        $this->assertEquals($oConfig->getShopCurrentURL(0) . 'req', $oV->getLink(0));
        $this->assertEquals($oConfig->getShopCurrentURL(1) . 'req&amp;lang=1', $oV->getLink(1));
    }

    public function testGetLink_SeoIsOnContactPage()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blSeoMode', true);

        $oV = $this->getMock('oxubase', array('_getRequestParams', '_getSeoRequestParams'));
        $oV->expects($this->any())->method('_getRequestParams')->will($this->returnValue('cl=contact'));
        $oV->expects($this->any())->method('_getSeoRequestParams')->will($this->returnValue('cl=contact'));

        $this->assertEquals($oConfig->getShopURL() . 'kontakt/', $oV->getLink());
        $this->assertEquals($oConfig->getShopURL() . 'kontakt/', $oV->getLink(0));
        $this->assertEquals($oConfig->getShopURL() . 'en/contact/', $oV->getLink(1));
    }


    public function testGetLink_SeoIsOff()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blSeoMode', false);

        $oV = $this->getMock('oxubase', array('_getRequestParams'));
        $oV->expects($this->any())->method('_getRequestParams')->will($this->returnValue('req'));

        $this->assertEquals($oConfig->getShopCurrentURL(0) . 'req', $oV->getLink());
        $this->assertEquals($oConfig->getShopCurrentURL(0) . 'req', $oV->getLink(0));
        $this->assertEquals($oConfig->getShopCurrentURL(1) . 'req&amp;lang=1', $oV->getLink(1));
    }

    public function testLoadCurrency()
    {
        $oView = oxNew('oxUbase');
        $this->setConfigParam('bl_perfLoadCurrency', true);

        $this->assertTrue($oView->loadCurrency());
    }

    public function testDontShowEmptyCategories()
    {
        $oView = oxNew('oxUbase');
        $this->setConfigParam('blDontShowEmptyCategories', true);

        $this->assertTrue($oView->dontShowEmptyCategories());
    }

    public function testIsLanguageLoaded()
    {
        $oView = oxNew('oxUbase');
        $this->setConfigParam('bl_perfLoadLanguages', true);

        $this->assertTrue($oView->isLanguageLoaded());
    }

    public function testGetRssLinks()
    {
        $oView = oxNew('oxUBase');
        $oView->addRssFeed('testTitle', 'testUrl', 'test');
        $aRssLinks['test'] = array('title' => 'testTitle', 'link' => 'testUrl');
        $this->assertEquals($aRssLinks, $oView->getRssLinks());
    }

    public function testGetSetMenueList()
    {
        $oView = oxNew('oxUbase');
        $oView->setMenueList('testmenue');
        $this->assertEquals('testmenue', $oView->getMenueList());
    }

    public function testGetSetActiveCategory()
    {
        $oView = oxNew('oxUbase');
        $oView->setActiveCategory('testcat');
        $this->assertEquals('testcat', $oView->getActiveCategory());
    }

    /**
     * Base view class title getter class
     */
    public function testGetTitle()
    {
        $oActiveView = $this->getMock('oxubase', array('getClassName'));
        $oActiveView->expects($this->once())->method('getClassName')->will($this->returnValue('links'));
        $oConfig = $this->getMock('oxconfig', array('getActiveView'));
        $oConfig->expects($this->once())->method('getActiveView')->will($this->returnValue($oActiveView));
        $oView = $this->getMock('oxubase', array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertEquals('Links', $oView->getTitle());
    }

    /*
     * Testing actvile lang abbervation getter
     */
    public function testGetActiveLangAbbr()
    {
        oxRegistry::getLang()->setBaseLanguage(0);

        $oView = oxNew('oxubase');
        $this->assertEquals("de", $oView->getActiveLangAbbr());

        oxRegistry::getLang()->setBaseLanguage(1);

        $oView = oxNew('oxubase');
        $this->assertEquals("en", $oView->getActiveLangAbbr());

    }

    /*
     * Testing active lang abbreviation getter when lang loading disabled in config
     */
    public function testGetActiveLangAbbrWhenDisabledInConfig()
    {
        $this->setConfigParam('bl_perfLoadLanguages', false);
        //expect the same result like in testGetActiveLangAbbr
        //the only difference is that for performance reasons with this setting the shop internal
        //abbr is used and not the user defined settings from the database.
        $this->testGetActiveLangAbbr();
    }

    public function testGetRequestParams()
    {
        $oView = $this->getMock('oxubase', array('getClassName', 'getFncName'));
        $oView->expects($this->any())->method('getClassName')->will($this->returnValue('testclass'));
        $oView->expects($this->any())->method('getFncName')->will($this->returnValue('testfunc'));
        $this->setRequestParameter('cnid', 'catid');
        $this->setRequestParameter('mnid', 'manId');
        $this->setRequestParameter('anid', 'artid');
        $this->setRequestParameter('page', '2');
        $this->setRequestParameter('tpl', 'test');
        $this->setRequestParameter('oxloadid', 'test');
        $this->setRequestParameter('pgNr', '2');
        $this->setRequestParameter('searchparam', 'test');
        $this->setRequestParameter('searchcnid', 'searchcat');
        $this->setRequestParameter('searchvendor', 'searchven');
        $this->setRequestParameter('searchmanufacturer', 'searchman');
        $this->setRequestParameter('searchrecomm', 'searchrec');
        $this->setRequestParameter('recommid', 'recid');

        $sExpUrl = 'cl=testclass&amp;fnc=testfunc&amp;cnid=catid&amp;mnid=manId&amp;anid=artid&amp;page=2&amp;tpl=test&amp;oxloadid=test&amp;pgNr=2' .
            '&amp;searchparam=test&amp;searchcnid=searchcat&amp;searchvendor=searchven' .
            '&amp;searchmanufacturer=searchman&amp;searchrecomm=searchrec&amp;recommid=recid';
        $this->assertEquals($sExpUrl, $oView->UNITgetRequestParams());
    }

    public function testGetRequestParamsSkipFnc()
    {
        $oView = $this->getMock('oxubase', array('getClassName', 'getFncName'));
        $oView->expects($this->any())->method('getClassName')->will($this->returnValue('testclass'));
        $oView->expects($this->any())->method('getFncName')->will($this->returnValue('tobasket'));
        $this->setRequestParameter('cnid', 'catid');
        $this->setRequestParameter('mnid', 'manId');

        $sExpUrl = 'cl=testclass&amp;cnid=catid&amp;mnid=manId';
        $this->assertEquals($sExpUrl, $oView->UNITgetRequestParams());
    }

    public function testGetRequestParamsSkipFnc2()
    {
        $oView = $this->getMock('oxubase', array('getClassName', 'getFncName'));
        $oView->expects($this->any())->method('getClassName')->will($this->returnValue('testclass'));
        $oView->expects($this->any())->method('getFncName')->will($this->returnValue('moveleft'));
        $this->setRequestParameter('cnid', 'catid');
        $this->setRequestParameter('mnid', 'manId');

        $sExpUrl = 'cl=testclass&amp;cnid=catid&amp;mnid=manId';
        $this->assertEquals($sExpUrl, $oView->UNITgetRequestParams());
    }

    public function testGetRequestParamsWithoutPageNr()
    {
        $oView = $this->getMock('oxubase', array('getClassName'));
        $oView->expects($this->any())->method('getClassName')->will($this->returnValue('testclass'));
        $this->setRequestParameter('cnid', 'catid');
        $this->setRequestParameter('pgNr', '2');

        $sExpUrl = 'cl=testclass&amp;cnid=catid';
        $this->assertEquals($sExpUrl, $oView->UNITgetRequestParams(false));
    }

    public function testNoIndex()
    {
        $oView = oxNew('oxubase');
        $this->assertEquals(0, $oView->noIndex());

        //
        $this->setRequestParameter('fnc', 'blankfunction');
        $oView = oxNew('oxubase');
        $this->assertEquals(0, $oView->noIndex());

        $this->setRequestParameter('fnc', 'tocomparelist');
        $oView = oxNew('oxubase');
        $this->assertEquals(1, $oView->noIndex());

        $this->setRequestParameter('fnc', 'tobasket');
        $oView = oxNew('oxubase');
        $this->assertEquals(1, $oView->noIndex());

        //
        $this->setRequestParameter('fnc', 'blankfunction');
        $oView = oxNew('oxubase');
        $this->assertEquals(0, $oView->noIndex());

        //
        $this->setRequestParameter('cur', 'xxx');
        $oView = oxNew('oxubase');
        $this->assertEquals(1, $oView->noIndex());
    }

    public function testSetActCurrencyGetActCurrency()
    {
        $oCurr = 'testcurr';

        $oView = oxNew('oxUbase');
        $oView->setActCurrency($oCurr);
        $this->assertEquals($oCurr, $oView->getActCurrency());
    }

    public function testGetContentByIdent()
    {
        $oUBase = $this->getProxyClass("oxubase");

        $oContent = $oUBase->getContentByIdent('oxagb');
        $this->assertNotNull($oContent);
        $this->assertEquals('oxagb', $oContent->oxcontents__oxloadid->value);

        $aContents = $oUBase->getNonPublicVar("_aContents");
        $this->assertTrue(isset($aContents['oxagb']));
        $this->assertEquals('oxagb', $aContents['oxagb']->oxcontents__oxloadid->value);
    }

    public function testGetContentCategory()
    {
        $oUBase = oxNew('oxubase');
        $this->assertFalse($oUBase->getContentCategory());
    }

    public function testCanRedirectFalse()
    {
        $this->setRequestParameter('fnc', 'something');
        $oUBase = oxNew('oxubase');
        $this->assertFalse($oUBase->UNITcanRedirect());
    }

    public function testCanRedirectTrue()
    {
        $this->setRequestParameter('cl', 'details');
        $oUBase = oxNew('oxubase');
        $this->assertTrue($oUBase->UNITcanRedirect());
    }

    public function testProcessRequestCanRedirect()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = $sUri = 'index.php?cl=account_wishlist';

        oxTestModules::addFunction("oxUtils", "redirect", "{ \$aArgs = func_get_args(); throw new exception( \$aArgs[0] ); }");

        $oUBase = $this->getMock('oxubase', array('_canRedirect', 'isAdmin'));
        $oUBase->expects($this->any())->method('_canRedirect')->will($this->returnValue(true));
        $oUBase->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        try {
            $oUBase->UNITprocessRequest();
        } catch (Exception $oEx) {
            $this->assertEquals($this->getConfig()->getShopURL() . 'mein-wunschzettel/', $oEx->getMessage(), 'error executing "testProcessRequest" test');

            return;
        }

        $this->fail('error executing "testProcessRequest" test');
    }

    public function testForceNoIndex()
    {
        $oView = oxNew('oxubase');
        $oView->UNITforceNoIndex();
        $this->assertEquals(2, $oView->noIndex());
    }

    public function testProcessRequestCantRedirect()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = $sUri = 'index.php?param1=value1&param2=value2';

        oxTestModules::addFunction("oxUtils", "redirect", "{ \$aArgs = func_get_args(); throw new exception( \$aArgs[0] ); }");

        $oUBase = $this->getMock('oxubase', array('_canRedirect', 'getLink', 'isAdmin', '_forceNoIndex'));
        $oUBase->expects($this->any())->method('_canRedirect')->will($this->returnValue(false));
        $oUBase->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oUBase->expects($this->once())->method('_forceNoIndex');

        $this->setConfigParam('blSeoLogging', 1);

        try {
            $oUBase->UNITprocessRequest();
        } catch (Exception $oEx) {
            // redirect must not be executed
            $this->fail('error executing "testProcessRequestCantRedirect" test');
        }

        $sShopId = $this->getConfig()->getShopId();
        $sLangId = oxRegistry::getLang()->getBaseLanguage();
        $sIdent = md5(strtolower(str_replace('&', '&amp;', $sUri)) . $sShopId . $sLangId);

        // testing if request was written in seo log table
        $this->assertTrue((bool) oxDb::getDb()->getOne("select 1 from oxseologs where oxident='$sIdent'"));
    }

    public function testProcessRequestCantRedirectNoIndex()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = $sUri = 'index.php?param1=value1&param2=value2';

        oxTestModules::addFunction("oxUtils", "redirect", "{ \$aArgs = func_get_args(); throw new exception( \$aArgs[0] ); }");

        $oUBase = $this->getMock('oxubase', array('_canRedirect', 'getLink', 'isAdmin', '_forceNoIndex', 'noIndex'));
        $oUBase->expects($this->any())->method('_canRedirect')->will($this->returnValue(false));
        $oUBase->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oUBase->expects($this->never())->method('_forceNoIndex');
        $oUBase->expects($this->once())->method('noIndex')->will($this->returnValue(VIEW_INDEXSTATE_NOINDEXFOLLOW));

        try {
            $oUBase->UNITprocessRequest();
        } catch (Exception $oEx) {
            // redirect must not be executed
            $this->fail('error executing "testProcessRequestCantRedirect" test');
        }

        $sShopId = $this->getConfig()->getShopId();
        $sLangId = oxRegistry::getLang()->getBaseLanguage();
        $sIdent = md5(strtolower(str_replace('&', '&amp;', $sUri)) . $sShopId . $sLangId);

        // testing if request was written in seo log table
        $this->assertfalse((bool) oxDb::getDb()->getOne("select 1 from oxseologs where oxident='$sIdent'"));
    }

    public function testProcessRequestCantRedirectNoLogging()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = $sUri = 'index.php?param1=value1&param2=value2';

        $utils = $this->getMock('oxUtils', array('redirect'));
        $utils->expects($this->never())->method('redirect');
        oxRegistry::set('oxUtils', $utils);

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this->getMock('oxConfig', array('isProductiveMode'));
        $config->expects($this->any())->method('isProductiveMode')->will($this->returnValue(1));

        $userBase = $this->getMock('oxubase', array('_canRedirect', 'getLink', 'isAdmin', '_forceNoIndex', 'getConfig'));
        $userBase->expects($this->any())->method('_canRedirect')->will($this->returnValue(false));
        $userBase->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $userBase->expects($this->once())->method('_forceNoIndex');
        $userBase->expects($this->any())->method('getConfig')->will($this->returnValue($config));

        $userBase->UNITprocessRequest();

        $sLangId = oxRegistry::getLang()->getBaseLanguage();
        $sIdent = md5(strtolower(str_replace('&', '&amp;', $sUri)) . '1' . $sLangId);

        // testing if request was written in seo log table
        $this->assertfalse((bool) oxDb::getDb()->getOne("select 1 from oxseologs where oxident='$sIdent'"));
    }

    public function testProcessRequestCantRedirectLoggingByParam()
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = $sUri = 'index.php?param1=value1&param2=value2';

        $utils = $this->getMock('oxUtils', array('redirect'));
        $utils->expects($this->never())->method('redirect');
        oxRegistry::set('oxUtils', $utils);

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this->getMock('oxconfig', array('isProductiveMode'));
        $config->expects($this->any())->method('isProductiveMode')->will($this->returnValue(1));
        $config->setConfigParam('blSeoLogging', 1);

        $oUBase = $this->getMock('oxubase', array('_canRedirect', 'getLink', 'isAdmin', '_forceNoIndex', 'getConfig'));
        $oUBase->expects($this->any())->method('_canRedirect')->will($this->returnValue(false));
        $oUBase->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oUBase->expects($this->once())->method('_forceNoIndex');
        $oUBase->expects($this->any())->method('getConfig')->will($this->returnValue($config));

        $oUBase->UNITprocessRequest();

        $sLangId = oxRegistry::getLang()->getBaseLanguage();
        $shopId = $this->getShopId();
        $sIdent = md5(strtolower(str_replace('&', '&amp;', $sUri)) . $shopId . $sLangId);

        // testing if request was written in seo log table
        $this->assertTrue((bool) oxDb::getDb()->getOne("select 1 from oxseologs where oxident='$sIdent'"));
    }

    // M71: Coupons should be considered in "Min order price" check
    public function testIsLowOrderPrice()
    {
        $oBasket = $this->getMock('oxBasket', array('isBelowMinOrderPrice'));
        $oBasket->expects($this->once())->method('isBelowMinOrderPrice')->will($this->returnValue(true));

        $oUBase = oxNew('oxUBase');
        $oUBase->getSession()->setBasket($oBasket);

        $oUBase->isLowOrderPrice();
        $this->assertTrue($oUBase->isLowOrderPrice());
    }

    public function testGetMinOrderPrice()
    {
        $this->setConfigParam("iMinOrderPrice", 40);
        $oCur = $this->getConfig()->getActShopCurrencyObject();

        $sMinOrderPrice = oxRegistry::getLang()->formatCurrency(40 * $oCur->rate);

        $oUBase = $this->getMock("oxUBase", array("isLowOrderPrice"));
        $oUBase->expects($this->once())->method('isLowOrderPrice')->will($this->returnValue(true));

        $this->assertEquals($sMinOrderPrice, $oUBase->getMinOrderPrice());
    }

    public function testGetTop5ArticleList()
    {
        $oUBase = $this->getProxyClass('oxubase');

        $oUBase->setNonPublicVar("_blTop5Action", true);
        $aList = $oUBase->getTop5ArticleList();

        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 6 : 4;
        $this->assertEquals($expectedCount, $aList->count());
    }

    public function testGetTop5ArticleList_notDefaultCount()
    {
        $oUBase = $this->getProxyClass('oxubase');
        $oUBase->setNonPublicVar("_blTop5Action", true);
        $aList = $oUBase->getTop5ArticleList(2);
        $this->assertEquals(2, $aList->count());
    }

    public function testGetBargainArticleList()
    {
        $oUBase = $this->getProxyClass('oxubase');

        $oUBase->setNonPublicVar("_blBargainAction", true);
        $aList = $oUBase->getBargainArticleList();

        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 6 : 4;
        $this->assertEquals($expectedCount, $aList->count());
    }

    public function testGetNewsRealStatus()
    {
        $oUBase = $this->getProxyClass('oxubase');
        $this->setConfigParam('blDisableNavBars', true);
        $oUBase->setNonPublicVar("_blIsOrderStep", true);
        $oUBase->render();
        $this->assertEquals(1, $oUBase->getNewsRealStatus());
    }

    // do not add pgNr. It will be added later
    public function testGeneratePageNavigationUrl()
    {
        $this->setRequestParameter('pgNr', '2');
        $this->setRequestParameter('lang', '1');
        $oUBase = $this->getMock('oxubase', array("getClassName", "getFncName"));
        $oUBase->expects($this->any())->method('getClassName')->will($this->returnValue("testclass"));
        $oUBase->expects($this->any())->method('getFncName')->will($this->returnValue("testfnc"));

        $this->assertEquals($this->getConfig()->getShopHomeURL() . "cl=testclass&amp;fnc=testfnc", $oUBase->generatePageNavigationUrl());
    }

    // If page number is zero
    public function testAddPageNrParamFirstPage()
    {
        $oUBase = $this->getProxyClass('oxubase');
        $this->assertEquals("aaa", $oUBase->UNITaddPageNrParam("aaa", 0));

        $this->assertEquals("aaa?bb", $oUBase->UNITaddPageNrParam("aaa?bb&amp;pgNr=2", 0));
        $this->assertEquals("aaa?param=value", $oUBase->UNITaddPageNrParam("aaa?pgNr=11&amp;param=value", 0));
        $this->assertEquals("aaa?", $oUBase->UNITaddPageNrParam("aaa?pgNr=11", 0));
        $this->assertEquals("aaa?bb&amp;param=value", $oUBase->UNITaddPageNrParam("aaa?bb&amp;pgNr=99&amp;param=value", 0));
    }

    public function testAddPageNrParam()
    {
        $oUBase = $this->getProxyClass('oxubase');

        $this->assertEquals("aaa?bb&amp;pgNr=2", $oUBase->UNITaddPageNrParam("aaa?bb", 2));

        $this->assertEquals("aaa?bb&amp;pgNr=1", $oUBase->UNITaddPageNrParam("aaa?bb&amp;pgNr=2", 1));
        $this->assertEquals("aaa?pgNr=11&amp;param=value", $oUBase->UNITaddPageNrParam("aaa?pgNr=13&amp;param=value", 11));
        $this->assertEquals("aaa?bb&amp;pgNr=919&amp;param=value", $oUBase->UNITaddPageNrParam("aaa?bb&amp;pgNr=155&amp;param=value", 919));

    }

    public function testSetGetRootVendor()
    {
        $oView = oxNew('oxUBase');
        $oView->setRootVendor('rootvendor');

        $this->assertEquals('rootvendor', $oView->getRootVendor());
    }

    public function testGetVendorId()
    {
        $oView = oxNew('oxUBase');
        $this->setRequestParameter('cnid', 'v_root');
        $this->assertEquals('root', $oView->getVendorId());
    }

    /**
     * oxUBase::getPromoFinishedList() test case
     *
     * @return null
     */
    public function testGetPromoFinishedList()
    {
        $oList = $this->getMock("oxActionList", array("loadFinishedByCount"));
        $oList->expects($this->once())->method('loadFinishedByCount')->with($this->equalTo(2));
        oxTestModules::addModuleObject('oxActionList', $oList);

        $oView = oxNew('oxUBase');

        $this->assertTrue($oView->getPromoFinishedList() instanceof ActionList);
    }

    /**
     * oxUBase::getPromoCurrentList() test case
     *
     * @return null
     */
    public function testGetPromoCurrentList()
    {
        $oList = $this->getMock("oxActionList", array("loadCurrent"));
        $oList->expects($this->once())->method('loadCurrent');
        oxTestModules::addModuleObject('oxActionList', $oList);

        $oView = oxNew('oxUBase');

        $this->assertTrue($oView->getPromoCurrentList() instanceof ActionList);
    }

    /**
     * oxUBase::getPromoFutureList() test case
     *
     * @return null
     */
    public function testGetPromoFutureList()
    {
        $oList = $this->getMock("oxActionList", array("loadFutureByCount"));
        $oList->expects($this->once())->method('loadFutureByCount')->with($this->equalTo(2));
        oxTestModules::addModuleObject('oxActionList', $oList);

        $oView = oxNew('oxUBase');

        $this->assertTrue($oView->getPromoFutureList() instanceof ActionList);
    }

    /**
     * oxUBase::getShowPromotionList() test case
     *
     * @return null
     */
    public function testGetShowPromotionList()
    {
        $oList = $this->getMock("oxActionList", array("areAnyActivePromotions"));
        $oList->expects($this->once())->method('areAnyActivePromotions')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxActionList', $oList);

        $oView = $this->getMock("oxUBase", array("getPromoFinishedList", "getPromoCurrentList", "getPromoFutureList"));
        $oView->expects($this->once())->method('getPromoFinishedList')->will($this->returnValue(1));
        $oView->expects($this->once())->method('getPromoCurrentList')->will($this->returnValue(1));
        $oView->expects($this->once())->method('getPromoFutureList')->will($this->returnValue(1));

        $this->assertTrue($oView->getShowPromotionList());
    }

    /**
     * oxUBase::getShowPromotionList() performance test case
     *
     * @return null
     */
    public function testGetShowPromotionListPerformanceIfNoPromotionsActive()
    {
        $oList = $this->getMock("oxActionList", array("areAnyActivePromotions"));
        $oList->expects($this->once())->method('areAnyActivePromotions')->will($this->returnValue(false));
        oxTestModules::addModuleObject('oxActionList', $oList);

        $oView = $this->getMock("oxUBase", array("getPromoFinishedList", "getPromoCurrentList", "getPromoFutureList"));
        $oView->expects($this->never())->method('getPromoFinishedList');
        $oView->expects($this->never())->method('getPromoCurrentList');
        $oView->expects($this->never())->method('getPromoFutureList');

        $this->assertFalse($oView->getShowPromotionList());
    }

    public function testGetFieldValidationErrors()
    {
        oxTestModules::addFunction("oxInputValidator", "getFieldValidationErrors", "{return array('test');}");

        $oView = oxNew('oxubase');
        $this->assertEquals(array('test'), $oView->getFieldValidationErrors());
    }

    /**
     * List display type getter test
     *
     * @return null
     */
    public function testGetListDisplayType_getVarFromRequest()
    {
        $oSession = $this->getSession();

        $this->setRequestParameter('ldtype', null);
        $this->setConfigParam('sDefaultListDisplayType', null);
        $oSubj = oxNew('oxubase');
        $this->assertEquals('infogrid', $oSubj->getListDisplayType());
        $this->assertEquals(null, oxRegistry::getSession()->getVariable("ldtype"));

        $oSession->setVariable('ldtype', null);
        $this->setRequestParameter('ldtype', "line");
        $this->assertEquals('infogrid', $oSubj->getListDisplayType());

        $oSession->setVariable('ldtype', null);
        $this->setRequestParameter('ldtype', 'grid');
        $this->setConfigParam('sDefaultListDisplayType', null);
        $oSubj = oxNew('oxubase');
        $this->assertEquals('grid', $oSubj->getListDisplayType());
        $this->assertEquals('grid', oxRegistry::getSession()->getVariable("ldtype"));

        $oSession->setVariable('ldtype', null);
        $this->setRequestParameter('ldtype', null);
        $this->setConfigParam('sDefaultListDisplayType', 'line');
        $oSubj = oxNew('oxubase');
        $this->assertEquals('line', $oSubj->getListDisplayType());
        $this->assertEquals(null, oxRegistry::getSession()->getVariable("ldtype"));

        // non existing list display type
        $oSession->setVariable('ldtype', null);
        $this->setRequestParameter('ldtype', "test");
        $this->setConfigParam('sDefaultListDisplayType', null);
        $oSubj = oxNew('oxubase');
        $this->assertEquals('infogrid', $oSubj->getListDisplayType());
        $this->assertEquals('infogrid', oxRegistry::getSession()->getVariable("ldtype"));
    }

    /**
     * List display type getter test
     *
     * @return null
     */
    public function testGetListDisplayType_getVarFromSession()
    {
        $oSession = $this->getSession();

        $this->setRequestParameter('ldtype', null);
        $oSession->setVariable('ldtype', null);
        $this->setConfigParam('sDefaultListDisplayType', null);
        $oSubj = oxNew('oxubase');
        $this->assertEquals('infogrid', $oSubj->getListDisplayType());
        $this->assertEquals(null, oxRegistry::getSession()->getVariable("ldtype"));

        $this->setRequestParameter('ldtype', null);
        $oSession->setVariable('ldtype', "line");
        $this->assertEquals('infogrid', $oSubj->getListDisplayType());

        $this->setRequestParameter('ldtype', null);
        $oSession->setVariable('ldtype', 'grid');
        $this->setConfigParam('sDefaultListDisplayType', null);
        $oSubj = oxNew('oxubase');
        $this->assertEquals('grid', $oSubj->getListDisplayType());

        //getting previously setted in session value
        $this->setRequestParameter('ldtype', null);
        $this->setConfigParam('sDefaultListDisplayType', 'line');
        $oSubj = oxNew('oxubase');
        $this->assertEquals('grid', $oSubj->getListDisplayType());

        // non existing list display type
        $this->setRequestParameter('ldtype', null);
        $oSession->setVariable('ldtype', "test");
        $this->setConfigParam('sDefaultListDisplayType', null);
        $oSubj = oxNew('oxubase');
        $this->assertEquals('infogrid', $oSubj->getListDisplayType());
        $this->assertEquals('test', oxRegistry::getSession()->getVariable("ldtype"));
    }

    /**
     * oxUBase::isEnabledPrivateSales() test case
     *
     * @return null
     */
    public function testIsEnabledPrivateSales()
    {
        // disabled
        $this->setConfigParam("blPsLoginEnabled", false);

        $oView = oxNew('oxUbase');
        $this->assertFalse($oView->isEnabledPrivateSales());

        // enabled, but preview is ON
        $this->setConfigParam("blPsLoginEnabled", true);
        oxTestModules::addFunction("oxutils", "canPreview", "{return true;}");

        $oView = oxNew('oxUbase');
        $this->assertFalse($oView->isEnabledPrivateSales());

        // enabled
        $this->setConfigParam("blPsLoginEnabled", true);
        oxTestModules::addFunction("oxutils", "canPreview", "{return null;}");

        $oView = oxNew('oxUbase');
        $this->assertTrue($oView->isEnabledPrivateSales());
    }

    public function testGetActPage()
    {
        $oUBase = oxNew('oxUbase');
        $this->setRequestParameter("pgNr", 2);

        $this->assertEquals(2, $oUBase->getActPage());
    }

    public function testGetActPageIfBelowZero()
    {
        $oUBase = oxNew('oxUbase');
        $this->setRequestParameter("pgNr", -1);

        $this->assertEquals(0, $oUBase->getActPage());
    }

    public function testSetGetRootCatChanged()
    {
        $oUBase = oxNew('oxUbase');
        $oUBase->setRootCatChanged(true);

        $this->assertTrue($oUBase->isRootCatChanged());
    }

    public function testGetInvoiceAddress()
    {
        $oUBase = oxNew('oxUbase');
        $this->setRequestParameter('invadr', 'testAddress');

        $this->assertEquals('testAddress', $oUBase->getInvoiceAddress());
    }

    public function testGetDeliveryAddress()
    {
        $oUBase = oxNew('oxUbase');
        $this->setRequestParameter('deladr', 'testAddress');

        $this->assertEquals('testAddress', $oUBase->getDeliveryAddress());
    }

    /**
     * tests setting of delivery method
     */
    public function testSetDeliveryAddress()
    {
        $oUBase = oxNew('oxubase');
        $aDelAddress = array('address' => 'TestAddress');
        $oUBase->setDeliveryAddress($aDelAddress);

        $this->assertEquals($aDelAddress, $oUBase->getDeliveryAddress());
    }

    public function testSetGetInvoiceAddress()
    {
        $oUBase = oxNew('oxUbase');
        $this->setRequestParameter('invadr', 'testAddress');
        $oUBase->setInvoiceAddress('testAddress1');

        $this->assertEquals('testAddress1', $oUBase->getInvoiceAddress());
    }

    public function testGetActiveUsername()
    {
        $oUBase = oxNew('oxUbase');
        $this->setRequestParameter('lgn_usr', 'testEmail');

        $this->assertEquals('testEmail', $oUBase->getActiveUsername());
    }

    public function testGetActiveUsernameFromSession()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxusername = new oxField('testEmail');
        $oUBase = $this->getMock("oxubase", array("getUser"));
        $oUBase->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $this->setRequestParameter('lgn_usr', false);

        $this->assertEquals('testEmail', $oUBase->getActiveUsername());
    }

    public function testGetWishlistUserId()
    {
        $oUBase = oxNew('oxUbase');
        $this->setRequestParameter('wishid', 'testId');

        $this->assertEquals('testId', $oUBase->getWishlistUserId());
    }

    /**
     * oxUBase::showCategoryArticlesCount() test case
     *
     * @return null
     */
    public function testShowCategoryArticlesCount()
    {
        $oView = oxNew('oxUbase');

        // disabled
        $this->setConfigParam("bl_perfShowActionCatArticleCnt", false);
        $this->assertFalse($oView->showCategoryArticlesCount());

        // enable
        $this->setConfigParam("bl_perfShowActionCatArticleCnt", true);
        $this->assertTrue($oView->showCategoryArticlesCount());
    }

    /**
     * oxUBase::getNewBasketItemMsgType() test case
     *
     * @return null
     */
    public function testGetNewBasketItemMsgType()
    {
        $oView = oxNew('oxUbase');
        $this->assertEquals((int) $this->getConfig()->getConfigParam("iNewBasketItemMessage"), $oView->getNewBasketItemMsgType());
    }

    /**
     * oxUBase::isEnabledDownloadabaleFiles() test case
     *
     * @return null
     */
    public function testIsEnabledDownloadableFiles()
    {
        $oView = oxNew('oxUbase');
        $this->assertEquals((bool) $this->getConfig()->getConfigParam("blEnableDownloads"), $oView->isEnabledDownloadableFiles());
    }

    /**
     * oxUBase::showRememberMe() test case
     *
     * @return null
     */
    public function testShowRememberMe()
    {
        $oView = oxNew('oxUbase');
        $this->assertEquals((bool) $this->getConfig()->getConfigParam("blShowRememberMe"), $oView->showRememberMe());
    }

    /**
     * oxUBase::isPriceCalculated() test case
     *
     * @return null
     */
    public function testIsPriceCalculated()
    {
        $oView = oxNew('oxUbase');
        $this->assertEquals((bool) $this->getConfig()->getConfigParam("bl_perfLoadPrice"), $oView->isPriceCalculated());
    }

    /* oxUBase::getCatMoreUrl() test case
     *
     * @return null
     */
    public function testGetCatMoreUrl()
    {
        $oUBase = oxNew('oxUBase');
        $this->assertEquals($oUBase->getConfig()->getShopHomeURL() . 'cnid=oxmore', $oUBase->getCatMoreUrl());
    }

    /*
     * oxUBase::isFieldRequired() test case
     *
     * @return null
     */
    public function testIsFieldRequired()
    {
        $aArray = array('test' => 'isset');

        $oUBase = $this->getProxyClass('oxUBase');
        $oUBase->setNonPublicVar('_aMustFillFields', $aArray);

        $this->assertTrue($oUBase->isFieldRequired('test'));
        $this->assertFalse($oUBase->isFieldRequired('testFalse'));
        $this->assertFalse($oUBase->isFieldRequired(null));
    }

    /*
     * oxUBase::getLastProducts() test case
     *
     * @return null
     */
    public function testGetLastProducts()
    {
        $oUBase = oxNew('oxubase');
        $this->assertNull($oUBase->getLastProducts());
    }

    /*
     * oxUBase::getPageNavigationLimitedBottom() test case
     *
     * @return null
     */
    public function testGetPageNavigationLimitedBottom()
    {
        $oUBase = oxNew('oxubase');
        $oRes = new stdClass();
        $oRes->NrOfPages = null;
        $oRes->actPage = 1;
        $this->assertEquals($oRes, $oUBase->getPageNavigationLimitedBottom());
    }

    /*
     * oxUBase::getPageNavigationLimitedTop() test case
     *
     * @return null
     */
    public function testGetPageNavigationLimitedTop()
    {
        $oUBase = oxNew('oxubase');
        $oRes = new stdClass();
        $oRes->NrOfPages = null;
        $oRes->actPage = 1;
        $this->assertEquals($oRes, $oUBase->getPageNavigationLimitedTop());
    }

    /*
     * oxUBase::getPageNavigation() test case
     *
     * @return null
     */
    public function testGetPageNavigation()
    {
        $oUBase = oxNew('oxubase');
        $this->assertNull($oUBase->getPageNavigation());
    }

    /*
     * oxUBase::getAlsoBoughtTheseProducts() test case
     *
     * @return null
     */
    public function testGetAlsoBoughtTheseProducts()
    {
        $oUBase = oxNew('oxubase');
        $this->assertNull($oUBase->getAlsoBoughtTheseProducts());
    }

    /*
     * oxUBase::getArticleId() test case
     *
     * @return null
     */
    public function testGetArticleId()
    {
        $oUBase = oxNew('oxubase');
        $this->assertNull($oUBase->getArticleId());
    }

    /*
     * oxUBase::getCrossSelling() test case
     *
     * @return null
     */
    public function testGetCrossSelling()
    {
        $oUBase = oxNew('oxubase');
        $this->assertNull($oUBase->getCrossSelling());
    }

    /*
     * oxUBase::getSimilarProducts() test case
     *
     * @return null
     */
    public function testGetSimilarProducts()
    {
        $oUBase = oxNew('oxubase');
        $this->assertNull($oUBase->getSimilarProducts());
    }

    /*
     * oxUBase::getAccessoires() test case
     *
     * @return null
     */
    public function testGetAccessoires()
    {
        $oUBase = oxNew('oxubase');
        $this->assertNull($oUBase->getAccessoires());
    }

    /*
     * oxUBase::getPaymentList() test case
     *
     * @return null
     */
    public function testGetPaymentList()
    {
        $oUBase = oxNew('oxubase');
        $this->assertNull($oUBase->getPaymentList());
    }

    /**
     * Tests if navigation parameters getter collects all needed values
     *
     * @return null
     */
    public function testGetNavigationParams()
    {
        $aParams = array("cnid"               => "testCategory",
            "mnid"               => "testManufacturer",
            "listtype"           => "testType",
            "ldtype"             => "testDisplay",
            "recommid"           => "paramValue",
            "searchrecomm"       => "testRecommendation",
            "searchparam"        => "testSearchParam",
            "searchtag"          => "testTag",
            "searchvendor"       => "testVendor",
            "searchcnid"         => "testCategory",
            "searchmanufacturer" => "testManufacturer",
        );
        foreach ($aParams as $sKey => $sValue) {
            $this->setRequestParameter($sKey, $sValue);
        }
        $aParams['actcontrol'] = "content";

        $oView = oxNew('oxUBase');
        $oView->setClassName('content');
        $aParameters = $oView->getNavigationParams();
        $this->assertEquals(ksort($aParams), ksort($aParameters));
    }

    public function testGetWishlistName()
    {
        $this->setRequestParameter('wishid', "testwishlist");
        oxTestModules::addFunction('oxuser', 'load', '{ return true; }');

        $oView = $this->getMock("oxubase", array("getUser"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue(true));

        $this->assertTrue($oView->getWishlistName() instanceof User);
    }

    public function testGetWishlistNameIfNotLoggedIn()
    {
        $this->setRequestParameter('wishid', "testwishlist");
        oxTestModules::addFunction('oxuser', 'load', '{ return true; }');

        $oView = $this->getMock("oxubase", array("getUser"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue(false));

        $this->assertFalse($oView->getWishlistName());
    }

    public function testGetWishlistNameIfWishIdIsNotSetted()
    {
        $this->setRequestParameter('wishid', null);
        oxTestModules::addFunction('oxuser', 'load', '{ return true; }');

        $oView = $this->getMock("oxubase", array("getUser"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue(true));

        $this->assertFalse($oView->getWishlistName());
    }

    public function testGetWishlistNameIfWishUserDoNotExists()
    {
        $this->setRequestParameter('wishid', "testwishlist");

        $oView = $this->getMock("oxubase", array("getUser"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue(true));

        $this->assertFalse($oView->getWishlistName());
    }

    /**
     * oxUBase::getTopNavigationCatCnt() test case
     *
     * @return null
     */
    public function testGetTopNavigationCatCntDefault()
    {
        $this->setConfigParam("iTopNaviCatCount", false);

        $oView = oxNew('oxUbase');
        $this->assertEquals(5, $oView->getTopNavigationCatCnt());
    }

    /**
     * oxUBase::getTopNavigationCatCnt() test case
     *
     * @return null
     */
    public function testGetTopNavigationCatCnt()
    {
        $this->setConfigParam("iTopNaviCatCount", 6);

        $oView = oxNew('oxUbase');
        $this->assertEquals(6, $oView->getTopNavigationCatCnt());
    }

    /**
     * Testing oxUBase::getCompareItemCount()
     *
     * @return null
     */
    public function testGetCompareItemCount()
    {
        $this->setSessionParam('aFiltcompproducts', array('1', '2'));

        $oView = oxNew('oxUbase');
        $this->assertEquals(2, $oView->getCompareItemCount());
    }

    /**
     * Testing oxUBase::isVatIncluded()
     * b2b mode is activated
     *
     * @return null
     */
    public function testIsVatIncludedNettoUser()
    {
        $this->getConfig()->setConfigParam('blShowNetPrice', true);

        $oUser = $this->getMock("oxuser", array('getActiveCountry'));
        $oUser->expects($this->once())->method('getActiveCountry')->will($this->returnValue(''));

        $oView = $this->getMock('oxubase', array('getUser'));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $this->assertFalse($oView->isVatIncluded());
    }

    /**
     * Testing oxUBase::isVatIncluded()
     * if shop is in netto mode
     *
     * @return null
     */
    public function testIsVatIncludedNettoShop()
    {
        $this->getConfig()->setConfigParam("blShowNetPrice", true);

        $oView = oxNew('oxUbase');
        $this->assertFalse($oView->isVatIncluded());
    }

    /**
     * Testing oxUBase::isVatIncluded()
     * if vat will be calculated only in basket
     *
     * @return null
     */
    public function testIsVatIncludedVatOnlyInBasket()
    {
        $this->getConfig()->setConfigParam("blShowNetPrice", false);
        $this->getConfig()->setConfigParam("bl_perfCalcVatOnlyForBasketOrder", true);

        $oView = oxNew('oxUbase');
        $this->assertFalse($oView->isVatIncluded());
    }

    /**
     * Testing oxUBase::isVatIncluded()
     * If country does bill VAT or not
     *
     * no session (no country)
     */
    public function testIsVatIncludedVatBilledInCountryNoSession()
    {
        $this->getConfig()->setConfigParam("blShowNetPrice", false);
        $this->getConfig()->setConfigParam("bl_perfCalcVatOnlyForBasketOrder", false);

        $oUser = $this->getMock("oxuser", array('getActiveCountry'));
        $oUser->expects($this->once())->method('getActiveCountry')->will($this->returnValue(''));

        $oView = $this->getMock('oxubase', array('getUser'));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $this->assertTrue($oView->isVatIncluded());
    }

    /**
     * Testing oxUBase::isVatIncluded()
     * If country does bill VAT or not
     *
     * country does bill vat
     */
    public function testIsVatIncludedVatInCountryIsBilled()
    {
        oxDb::getDB()->Execute("insert into oxcountry (oxid, oxvatstatus ) values ( 'oxcountry_0', 1)");

        $this->getConfig()->setConfigParam("blShowNetPrice", false);
        $this->getConfig()->setConfigParam("bl_perfCalcVatOnlyForBasketOrder", false);

        $oUser = $this->getMock("oxuser", array('getActiveCountry'));
        $oUser->expects($this->once())->method('getActiveCountry')->will($this->returnValue('oxcountry_0'));

        $oView = $this->getMock('oxubase', array('getUser'));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $this->assertTrue($oView->isVatIncluded());
    }

    /**
     * Testing oxUBase::isVatIncluded()
     * If country does bill VAT or not
     *
     * country does not bill vat
     */
    public function testIsVatIncludedVatInCountryIsNotBilled()
    {
        oxDb::getDB()->Execute("insert into oxcountry (oxid, oxvatstatus ) values ( 'oxcountry_1', 0)");

        $this->getConfig()->setConfigParam("blShowNetPrice", false);
        $this->getConfig()->setConfigParam("bl_perfCalcVatOnlyForBasketOrder", false);

        $oUser = $this->getMock("oxuser", array('getActiveCountry'));
        $oUser->expects($this->once())->method('getActiveCountry')->will($this->returnValue('oxcountry_1'));

        $oView = $this->getMock('oxubase', array('getUser'));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $this->assertFalse($oView->isVatIncluded());
    }

    /**
     * Check that widget link is retrieved properly
     */
    public function testGetWidgetLink()
    {
        $oView = oxNew('oxUbase');
        $this->getConfig()->setConfigParam("sShopURL", "testshop/");
        $this->setLanguage(1);

        $this->assertEquals("testshop/widget.php?lang=1", $oView->getWidgetLink());
    }

    /**
     * @return array
     */
    public function _dpProductiveModeNotInfluencingSeoLogging()
    {
        return array(
            array(0, 0, false, "Url should not be processed"),
            array(1, 0, false, "Url should not be processed"),
            array(0, 1, true, "Url should be processed"),
            array(1, 1, true, "Url should be processed"),
        );
    }

    /**
     * Test case for bug #5409
     *
     * @dataProvider _dpProductiveModeNotInfluencingSeoLogging
     */
    public function testProductiveModeNotInfluencingSeoLogging($blProductive, $blSeoLogging, $blExpected, $sMsg)
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = $sUri = 'index.php?param1=value1&param2=value2';

        oxTestModules::addFunction("oxUtils", "redirect", "{ \$aArgs = func_get_args(); throw new exception( \$aArgs[0] ); }");

        $this->setConfigParam('blSeoLogging', $blSeoLogging);
        $this->setConfigParam('blProductive', $blProductive);

        $oUBase = $this->getMock('oxubase', array('_canRedirect', 'getLink', 'isAdmin', '_forceNoIndex'));
        $oUBase->expects($this->any())->method('_canRedirect')->will($this->returnValue(false));
        $oUBase->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oUBase->expects($this->once())->method('_forceNoIndex');

        try {
            $oUBase->UNITprocessRequest();
        } catch (Exception $oEx) {
            // redirect must not be executed
            $this->fail('error executing "testProcessRequestCantRedirect" test: ' . $oEx->getMessage());
        }

        $sShopId = $this->getConfig()->getShopId();
        $sLangId = oxRegistry::getLang()->getBaseLanguage();
        $sIdent = md5(strtolower(str_replace('&', '&amp;', $sUri)) . $sShopId . $sLangId);

        // testing if request was written in seo log table
        $this->assertEquals($blExpected, (bool) oxDb::getDb()->getOne("select 1 from oxseologs where oxident='$sIdent'"), $sMsg);
    }

    /**
     * Data provider for testGetPageTitle
     *
     * @return array
     */
    public function getPageTitleParts()
    {
        return array(
            array(array('prefix' => 'Prefix', 'title' => 'Title', 'suffix' => 'Suffix', 'pageSuffix' => 'PageSuffix'), 'Prefix | Title | Suffix | PageSuffix'),
            array(array('prefix' => 'Prefix', 'title' => 'Title', 'suffix' => 'Suffix', 'pageSuffix' => ''), 'Prefix | Title | Suffix'),
            array(array('prefix' => 'Prefix', 'title' => '', 'suffix' => 'Suffix', 'pageSuffix' => ''), 'Prefix | Suffix'),
            array(array('prefix' => '', 'title' => 'Title', 'suffix' => 'Suffix', 'pageSuffix' => ''), 'Title | Suffix'),
            array(array('prefix' => 'Prefix', 'title' => 'Title', 'suffix' => '', 'pageSuffix' => ''), 'Prefix | Title'),
            array(array('prefix' => '', 'title' => 'Title', 'suffix' => '', 'pageSuffix' => ''), 'Title'),
            array(array('prefix' => '', 'title' => '', 'suffix' => '', 'pageSuffix' => ''), ''),
            array(array('prefix' => 'Prefix', 'title' => null, 'suffix' => '', 'pageSuffix' => ''), 'Prefix'),
            array(array('prefix' => 'Prefix', 'title' => false, 'suffix' => '', 'pageSuffix' => ''), 'Prefix'),
        );
    }

    /**
     * @dataProvider getPageTitleParts
     */
    public function testGetPageTitle($aParts, $sTitle)
    {
        $oUBase = $this->getMock('oxUBase', array('getTitlePrefix', 'getTitle', 'getTitleSuffix', 'getTitlePageSuffix'));
        $oUBase->expects($this->any())->method('getTitlePrefix')->will($this->returnValue($aParts['prefix']));
        $oUBase->expects($this->any())->method('getTitle')->will($this->returnValue($aParts['title']));
        $oUBase->expects($this->any())->method('getTitleSuffix')->will($this->returnValue($aParts['suffix']));
        $oUBase->expects($this->any())->method('getTitlePageSuffix')->will($this->returnValue($aParts['pageSuffix']));

        $this->assertEquals($sTitle, $oUBase->getPageTitle());
    }

    /**
     * test for getUserSelectedSorting
     * @see https://bugs.oxid-esales.com/view.php?id=6083
     */
    public function testGetUserSelectedSortingValidSorting()
    {
        /** @var BaseController $baseController */
        $baseController = oxNew('oxUBase');

        $_GET[$baseController->getSortOrderByParameterName()] = 'oxid';
        $_GET[$baseController->getSortOrderParameterName()] = 'asc';
        $this->assertEquals(
            array('sortby' => 'oxid', 'sortdir' => 'asc'),
            $baseController->getUserSelectedSorting()
        );

        $_GET[$baseController->getSortOrderParameterName()] = 'desc';
        $this->assertEquals(
            array('sortby' => 'oxid', 'sortdir' => 'desc'),
            $baseController->getUserSelectedSorting()
        );
    }

    /**
     * test for getUserSelectedSorting
     * @see https://bugs.oxid-esales.com/view.php?id=6083
     */
    public function testGetUserSelectedSortingInvalidSorting()
    {
        /** @var BaseController $baseController */
        $baseController = oxNew('oxUBase');

        //not existing field name
        $_GET[$baseController->getSortOrderByParameterName()] = 'foobar';
        $_GET[$baseController->getSortOrderParameterName()] = 'asc';
        $this->assertNull($baseController->getUserSelectedSorting());

        //empty field name
        $_GET[$baseController->getSortOrderByParameterName()] = '';
        $this->assertNull($baseController->getUserSelectedSorting());

        //not existing order direction
        $_GET[$baseController->getSortOrderByParameterName()] = 'oxid';
        $_GET[$baseController->getSortOrderParameterName()] = 'foobar';
        $this->assertNull($baseController->getUserSelectedSorting());

        //empty order direction
        $_GET[$baseController->getSortOrderParameterName()] = '';
        $this->assertNull($baseController->getUserSelectedSorting());
    }
}
