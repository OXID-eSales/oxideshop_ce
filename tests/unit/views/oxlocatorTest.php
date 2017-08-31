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

class testOxLocator extends oxLocator
{

    public $oBackProduct = null;
    public $oNextProduct = null;

    public function setClickCat($oClickCat)
    {
        $this->_oClickCat = $oClickCat;
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }

    public function __set($name, $val)
    {
        $this->$name = $val;
    }

    public function getLinkType()
    {
        return OXARTICLE_LINKTYPE_CATEGORY;
    }
}

class modUtils extends oxUtils
{

    public function seoIsActive($blReset = false, $sShopId = null, $iActLang = null)
    {
        return true;
    }

    public function isSearchEngine($blReset = false, $sShopId = null, $iActLang = null)
    {
        return true;
    }
}

class Unit_Views_oxlocatorTest extends OxidTestCase
{
    /**
     * Make a copy of The Barrel for testing
     */
    const SOURCE_ARTICLE_ID = 'f4f73033cf5045525644042325355732';

    protected $_iSeoMode = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        // backuping
        $this->_iSeoMode = oxRegistry::getConfig()->getActiveShop()->oxshops__oxseoactive->value;
        oxRegistry::getConfig()->getActiveShop()->oxshops__oxseoactive = new oxField(0, oxField::T_RAW);

        oxRegistry::getUtils()->seoIsActive(true);

        modConfig::setRequestParameter("listtype", null);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRegistry::getConfig()->setGlobalParameter('listtype', null);

        $sDelete = "Delete from oxcategories where oxtitle = 'test'";
        oxDb::getDb()->Execute($sDelete);
        oxDb::getDb()->execute('delete from oxrecommlists where oxid like "testlist%" ');
        oxDb::getDb()->execute('delete from oxobject2list where oxlistid like "testlist%" ');
        oxDb::getDb()->execute('delete from oxarticles where oxid like "%1234567%" ');

        // restoring
        oxRegistry::getConfig()->getActiveShop()->oxshops__oxseoactive = new oxField($this->_iSeoMode, oxField::T_RAW);

        oxRegistry::getUtils()->seoIsActive(true);
        parent::tearDown();
    }

    /**
     * Link maker testing
     */
    public function testMakeLink()
    {
        $aInput = array(
            'www.1.com/index.php'        => array('a=1&amp;b=2', 'www.1.com/index.php?a=1&amp;b=2'),
            'www.1.com/index.php?cl=xxx' => array('a=1&amp;b=2', 'www.1.com/index.php?cl=xxx&amp;a=1&amp;b=2'),
        );
        $oLocator = new oxLocator();
        foreach ($aInput as $sLink => $aParams) {
            if ($oLocator->UNITmakeLink($sLink, $aParams[0]) != $aParams[1]) {
                $this->fail('testMakeLink failed');
            }
        }
    }

    /**
     * Testing constructor
     */
    public function testConstruct()
    {
        $oLocator = $this->getProxyClass('oxlocator', array('test'));
        $this->assertEquals('test', $oLocator->getNonPublicVar('_sType'));

        $oLocator = $this->getProxyClass('oxlocator');
        $this->assertEquals('list', $oLocator->getNonPublicVar('_sType'));
    }

    /**
     * Testing locator data setter
     */
    public function testSetLocatorData()
    {
        $oCurrArticle = new oxarticle();

        $oLocatorTarget = $this->getMock('oxview', array('setListType'));
        $oLocatorTarget->expects($this->once())->method('setListType');

        $oLocator = $this->getMock('oxlocator', array('_setListLocatorData'));
        $oLocator->expects($this->once())->method('_setListLocatorData')->with($this->equalTo($oLocatorTarget), $this->equalTo($oCurrArticle));
        $oLocator->setLocatorData($oCurrArticle, $oLocatorTarget, 'xxx');
    }

    public function testSetListLocatorData()
    {
        // seo off
        modConfig::getInstance()->setConfigParam('blSeoMode', false);

        modConfig::getInstance()->setConfigParam('iNrofCatArticles', 10);

        oxRegistry::getUtils()->seoIsActive(true);

        $myConfig = oxRegistry::getConfig();

        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue('1651'));

        $sActCat = '30e44ab83fdee7564.23264141';
        $sPrevId = '1351';
        $sNextId = '1661';
        $sActCat = '8a142c3e4143562a5.46426637';
        $sPrevId = '1477';
        $sNextId = '1672';

        $oCategory = new oxcategory();
        $oCategory->load($sActCat);

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActiveCategory', 'getCategoryTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $oLocatorTarget->expects($this->once())->method('getCategoryTree')->will($this->returnValue(new oxcategorylist));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $oLocator = new testOxLocator();

        // testing
        $oLocator->UNITsetListLocatorData($oLocatorTarget, $oCurrArticle);

        $this->assertEquals(9, $oCategory->iProductPos);
        $this->assertEquals(32, $oCategory->iCntOfProd);
        $iPgNr = 0;


        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=alist&amp;cnid={$sActCat}" . (($iPgNr) ? "&amp;pgNr={$iPgNr}" : ""), $oCategory->toListLink);
        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=details&amp;anid=" . $sNextId, $oCategory->nextProductLink);
        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=details&amp;anid=" . $sPrevId, $oCategory->prevProductLink);
    }

    public function testSetListLocatorDataSeo()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $myConfig = oxRegistry::getConfig();

        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue('1651'));

        $sActCat = '30e44ab83fdee7564.23264141';
        $sPrevId = '1351';
        $sNextId = '1661';
        $sActCat = '8a142c3e4143562a5.46426637';
        $sPrevId = '1477';
        $sNextId = '1672';

        $oCategory = new oxcategory();
        $oCategory->load($sActCat);

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActiveCategory', 'getCategoryTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $oLocatorTarget->expects($this->once())->method('getCategoryTree')->will($this->returnValue(new oxcategorylist));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(true));

        $oLocator = $this->getMock('testOxLocator', array('getConfig'));
        $oLocator->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        // testing
        $oLocator->UNITsetListLocatorData($oLocatorTarget, $oCurrArticle);

        $sShopUrl = oxRegistry::getConfig()->getShopUrl();

        $this->assertEquals(9, $oCategory->iProductPos);
        $this->assertEquals(32, $oCategory->iCntOfProd);

        $sToListLink = $sShopUrl . 'Geschenke/9/';
        $sNextProdLink = $sShopUrl . 'Geschenke/Wohnen/Uhren/Wanduhr-PHOTOFRAME.html';
        $sPrevProdLink = $sShopUrl . 'Geschenke/Bar-Equipment/Champagnerverschluss-GOLF.html';


        $this->assertEquals($sToListLink, $oCategory->toListLink);
        $this->assertEquals($sNextProdLink, $oCategory->nextProductLink);
        $this->assertEquals($sPrevProdLink, $oCategory->prevProductLink);
    }

    public function testSetVendorLocatorData()
    {
        // seo off
        $this->getConfig()->setConfigParam('blSeoMode', false);
        oxRegistry::getUtils()->seoIsActive(true);

        $myConfig = oxRegistry::getConfig();

        $sArt = '1142';
        $sNextLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1477&amp;listtype=vendor&amp;cnid=v_d2e44d9b31fcce448.08890330";
        $sPrevLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1131&amp;listtype=vendor&amp;cnid=v_d2e44d9b31fcce448.08890330";
        $sArt = '1964';
        $sPrevLink = '';
        $sNextLink = '';
        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArt));

        $sActCat = 'v_d2e44d9b31fcce448.08890330';
        $sActCat = 'v_77442e37fdf34ccd3.94620745';

        $oVendor = new oxvendor();
        $oVendor->load(str_replace('v_', '', $sActCat));

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActVendor', 'getVendorTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActVendor')->will($this->returnValue($oVendor));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_VENDOR));

        $oLocator = new testOxLocator();
        $oLocator->UNITsetVendorLocatorData($oLocatorTarget, $oCurrArticle);


        $this->assertEquals(1, $oVendor->iProductPos);
        $this->assertEquals(1, $oVendor->iCntOfProd);

        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=vendorlist&amp;cnid={$sActCat}{$sPgNr}", $oVendor->toListLink);
        $this->assertEquals($sNextLink, $oVendor->nextProductLink);
        $this->assertEquals($sPrevLink, $oVendor->prevProductLink);
    }

    public function testSetVendorLocatorDataSeo()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $myConfig = oxRegistry::getConfig();

        $sArt = '1142';
        $sNextLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1477&amp;listtype=vendor&amp;cnid=v_d2e44d9b31fcce448.08890330";
        $sPrevLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1131&amp;listtype=vendor&amp;cnid=v_d2e44d9b31fcce448.08890330";
        $sArt = '1964';
        $sPrevLink = '';
        $sNextLink = '';
        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArt));
        $oCurrArticle->setLinkType(1);

        $sActCat = 'v_d2e44d9b31fcce448.08890330';
        $sActCat = 'v_77442e37fdf34ccd3.94620745';

        $oVendor = new oxvendor();
        $oVendor->load(str_replace('v_', '', $sActCat));

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActVendor', 'getVendorTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActVendor')->will($this->returnValue($oVendor));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_VENDOR));

        $oLocator = new testOxLocator();
        $oLocator->UNITsetVendorLocatorData($oLocatorTarget, $oCurrArticle);

        $sShopUrl = oxRegistry::getConfig()->getShopUrl();


        $this->assertEquals(1, $oVendor->iProductPos);
        $this->assertEquals(1, $oVendor->iCntOfProd);
        $sToListLink = $sShopUrl . 'Nach-Lieferant/Bush/';
        $sPrevProdLink = null;
        $sNextProdLink = null;

        $this->assertEquals($sToListLink, $oVendor->toListLink);
        $this->assertEquals($sNextProdLink, $oVendor->nextProductLink);
        $this->assertEquals($sPrevProdLink, $oVendor->prevProductLink);
    }

    public function testSetManufacturerLocatorData()
    {
        // seo off
        modConfig::getInstance()->setConfigParam('blSeoMode', false);
        oxRegistry::getUtils()->seoIsActive(true);

        $myConfig = oxRegistry::getConfig();

        $sArt = '1142';
        $sNextLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1477&amp;listtype=manufacturer&amp;mnid=" . md5("d2e44d9b31fcce448.08890330");
        $sPrevLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1131&amp;listtype=manufacturer&amp;mnid=" . md5("d2e44d9b31fcce448.08890330");
        $sArt = '1964';
        $sPrevLink = '';
        $sNextLink = '';
        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArt));

        $sActCat = md5('d2e44d9b31fcce448.08890330');
        $sActCat = md5('77442e37fdf34ccd3.94620745');

        $oManufacturer = new oxmanufacturer();
        $oManufacturer->load($sActCat);

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActManufacturer', 'getManufacturerTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActManufacturer')->will($this->returnValue($oManufacturer));
        $oLocatorTarget->expects($this->once())->method('getManufacturerTree')->will($this->returnValue(new oxmanufacturerlist));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_MANUFACTURER));

        $oLocator = new testOxLocator();
        $oLocator->UNITsetManufacturerLocatorData($oLocatorTarget, $oCurrArticle);


        $this->assertEquals(1, $oManufacturer->iProductPos);
        $this->assertEquals(1, $oManufacturer->iCntOfProd);

        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=manufacturerlist&amp;mnid={$sActCat}{$sPgNr}", $oManufacturer->toListLink);
        $this->assertEquals($sNextLink, $oManufacturer->nextProductLink);
        $this->assertEquals($sPrevLink, $oManufacturer->prevProductLink);
    }

    public function testSetManufacturerLocatorDataSeo()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $myConfig = oxRegistry::getConfig();

        $sArt = '1142';
        $sNextLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1477&amp;listtype=vendor&amp;cnid=v_d2e44d9b31fcce448.08890330";
        $sPrevLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1131&amp;listtype=vendor&amp;cnid=v_d2e44d9b31fcce448.08890330";
        $sArt = '1964';
        $sPrevLink = '';
        $sNextLink = '';
        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArt));
        $oCurrArticle->setLinkType(1);

        $sActCat = md5('d2e44d9b31fcce448.08890330');
        $sActCat = md5('77442e37fdf34ccd3.94620745');

        $oManufacturer = new oxmanufacturer();
        $oManufacturer->load($sActCat);

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActManufacturer', 'getManufacturerTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActManufacturer')->will($this->returnValue($oManufacturer));
        $oLocatorTarget->expects($this->once())->method('getManufacturerTree')->will($this->returnValue(new oxmanufacturerlist));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_MANUFACTURER));

        $oLocator = new testOxLocator();
        $oLocator->UNITsetManufacturerLocatorData($oLocatorTarget, $oCurrArticle);

        $sShopUrl = oxRegistry::getConfig()->getShopUrl();


        $this->assertEquals(1, $oManufacturer->iProductPos);
        $this->assertEquals(1, $oManufacturer->iCntOfProd);
        $sToListLink = $sShopUrl . 'Nach-Hersteller/Bush/';
        $sPrevProdLink = null;
        $sNextProdLink = null;

        $this->assertEquals($sToListLink, $oManufacturer->toListLink);
        $this->assertEquals($sNextProdLink, $oManufacturer->nextProductLink);
        $this->assertEquals($sPrevProdLink, $oManufacturer->prevProductLink);
    }

    public function testSetSearchLocatorData()
    {
        modConfig::getInstance()->setConfigParam('blSeoMode', false);
        oxRegistry::getUtils()->seoIsActive(true);

        $myConfig = oxRegistry::getConfig();
        $sPrevLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1651&amp;searchparam=Bier&amp;listtype=search";
        $sNextLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=2357&amp;searchparam=Bier&amp;listtype=search";
        $sArtId = '1889';
        $sArtId = '1651';
        $sPrevLink = '';
        $sNextLink = '';

        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArtId));

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'getSortingSql', 'addTplParam', 'setSearchTitle', 'getSearchTitle', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('addTplParam');
        $oLocatorTarget->expects($this->any())->method('setSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        modConfig::setRequestParameter("searchparam", 'Bier');
        $oLocator = new testOxLocator();
        $oLocator->UNITsetSearchLocatorData($oLocatorTarget, $oCurrArticle);
        modConfig::setRequestParameter("searchparam", null);

        $oSearch = $oLocatorTarget->getActSearch();


        $this->assertEquals(1, $oSearch->iProductPos);
        $this->assertEquals(1, $oSearch->iCntOfProd);
        $sPgNr = '';

        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=search{$sPgNr}&amp;searchparam=Bier&amp;listtype=search", $oSearch->toListLink);
        $this->assertEquals($sNextLink, $oSearch->nextProductLink);
        $this->assertEquals($sPrevLink, $oSearch->prevProductLink);
    }

    public function testSetSearchLocatorDataFromVendor()
    {
        // seo off
        modConfig::getInstance()->setConfigParam('blSeoMode', false);
        oxRegistry::getUtils()->seoIsActive(true);

        $myConfig = oxRegistry::getConfig();

        $sPrevId = '1131';
        $sNextId = '1477';

        $sArt = '1142';
        $sArt = '5065';

        $sPrevId = '5064';
        $sNextId = '5067';

        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArt));

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'prepareSortColumns', 'getSortingSql', 'addTplParam', 'setSearchTitle', 'getSearchTitle', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('addTplParam');
        $oLocatorTarget->expects($this->any())->method('setSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $sSearchVendor = 'd2e44d9b31fcce448.08890330';
        $sSearchVendor = '68342e2955d7401e6.18967838';
        modConfig::setRequestParameter("searchparam", 'a');
        modConfig::setRequestParameter("searchvendor", $sSearchVendor);
        $oLocator = new testOxLocator();
        $oLocator->UNITsetSearchLocatorData($oLocatorTarget, $oCurrArticle);
        modConfig::setRequestParameter("searchparam", null);
        modConfig::setRequestParameter("searchvendor", null);

        $oSearch = $oLocatorTarget->getActSearch();


        $this->assertEquals(2, $oSearch->iProductPos);
        $this->assertEquals(5, $oSearch->iCntOfProd);
        $iPgNr = 1;

        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=search&amp;pgNr={$iPgNr}&amp;searchparam=a&amp;listtype=search&amp;searchvendor={$sSearchVendor}", $oSearch->toListLink);
        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=details&amp;anid={$sNextId}&amp;searchparam=a&amp;listtype=search&amp;searchvendor={$sSearchVendor}", $oSearch->nextProductLink);
        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=details&amp;anid={$sPrevId}&amp;searchparam=a&amp;listtype=search&amp;searchvendor={$sSearchVendor}", $oSearch->prevProductLink);
    }

    public function testSetSearchLocatorDataFromCat()
    {
        modConfig::getInstance()->setConfigParam('blSeoMode', false);
        oxRegistry::getUtils()->seoIsActive(true);

        $myConfig = oxRegistry::getConfig();

        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue('1651'));

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'prepareSortColumns', 'getSortingSql', 'addTplParam', 'setSearchTitle', 'getSearchTitle', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('addTplParam');
        $oLocatorTarget->expects($this->any())->method('setSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));


        $sSearchCat = '30e44ab841af13e46.42570689';
        $sNextLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=2357&amp;searchparam=Bier&amp;listtype=search&amp;searchcnid=$sSearchCat";
        $sSearchCat = '8a142c3e4143562a5.46426637';
        $sNextLink = '';

        modConfig::setRequestParameter("searchparam", 'Bier');
        modConfig::setRequestParameter("searchcnid", $sSearchCat);
        $oLocator = new testOxLocator();
        $oLocator->UNITsetSearchLocatorData($oLocatorTarget, $oCurrArticle);
        modConfig::setRequestParameter("searchparam", null);
        modConfig::setRequestParameter("searchcnid", null);

        $oSearch = $oLocatorTarget->getActSearch();


        $this->assertEquals(1, $oSearch->iProductPos);
        $this->assertEquals(1, $oSearch->iCntOfProd);

        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=search&amp;searchparam=Bier&amp;listtype=search&amp;searchcnid=$sSearchCat", $oSearch->toListLink);
        $this->assertEquals($sNextLink, $oSearch->nextProductLink);
        $this->assertNull($oSearch->prevProductLink);
    }

    public function testSetTagLocatorData()
    {
        // seo off
        modConfig::getInstance()->setConfigParam('blSeoMode', false);
        oxRegistry::getUtils()->seoIsActive(true);

        $myConfig = oxRegistry::getConfig();

        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue('2000'));

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'getSortingSql', 'addTplParam', 'setSearchTitle', 'getSearchTitle', 'showSorting'));
        $oLocatorTarget->expects($this->any())->method('addTplParam');
        $oLocatorTarget->expects($this->any())->method('setSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_TAG));

        modConfig::setRequestParameter("searchtag", 'wanduhr');
        $oLocator = new testOxLocator();
        $oLocator->UNITsetTagLocatorData($oLocatorTarget, $oCurrArticle);

        $oTag = $oLocatorTarget->getActTag();


        $this->assertEquals(2, $oTag->iProductPos);
        $this->assertEquals(3, $oTag->iCntOfProd);
        $sPrevLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1771&amp;searchtag=wanduhr&amp;listtype=tag";
        $expectedUrl = $myConfig->getShopHomeUrl() . "cl=tag&amp;searchtag=wanduhr&amp;pgNr=1";

        $this->assertEquals($expectedUrl, $oTag->toListLink);
        $this->assertEquals($sPrevLink, $oTag->prevProductLink);
    }

    // set locator data after recommlist search
    public function testSetRecommListLocatorData()
    {
        oxTestModules::addFunction('oxarticlelist', 'loadRecommArticleIds', '{parent::loadRecommArticleIds($aA[0], " order by oxobject2list.oxobjectid asc" );}');
        $myConfig = $this->getConfig();

        // seo off
        $this->setConfigParam('blSeoMode', false);
        oxRegistry::getUtils()->seoIsActive(true);

        $myDB = oxDb::getDB();
        $sShopId = $myConfig->getShopId();
        // adding article to recommendlist
        $sQ = 'replace into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $sQ = 'replace into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "1651", "testlist", "test" ),' .
              ' ( "testlist2", "2000", "testlist", "test" ), ( "testlist3", "1126", "testlist", "test" ) ';
        $myDB->Execute($sQ);

        $oRecomm = new oxRecommList();
        $oRecomm->load("testlist");

        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue('1651'));

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'addTplParam', 'setSearchTitle', 'getSearchTitle', 'getActiveRecommList'));
        $oLocatorTarget->expects($this->any())->method('addTplParam');
        $oLocatorTarget->expects($this->any())->method('setSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecomm));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_TAG));

        modConfig::setRequestParameter("searchrecomm", 'test');
        $oLocator = new testOxLocator();
        $oLocator->UNITsetRecommListLocatorData($oLocatorTarget, $oCurrArticle);

        $this->assertEquals(2, $oRecomm->iProductPos);
        $this->assertEquals(3, $oRecomm->iCntOfProd);
        $sPrevLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1126&amp;recommid=testlist&amp;listtype=recommlist&amp;searchrecomm=test";
        $sNextLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=2000&amp;recommid=testlist&amp;listtype=recommlist&amp;searchrecomm=test";

        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=recommlist&amp;recommid=testlist&amp;pgNr=1&amp;searchrecomm=test", $oRecomm->toListLink);
        $this->assertEquals($sPrevLink, $oRecomm->prevProductLink);
        $this->assertEquals($sNextLink, $oRecomm->nextProductLink);
    }

    // set locator data for my account recommlists
    public function testSetRecommListLocatorDataNoSearchParam()
    {
        oxTestModules::addFunction('oxarticlelist', 'loadRecommArticleIds', '{parent::loadRecommArticleIds($aA[0], " order by oxobject2list.oxobjectid asc" );}');
        $myConfig = oxRegistry::getConfig();

        // seo off
        $this->setConfigParam('blSeoMode', false);
        oxRegistry::getUtils()->seoIsActive(true);

        $myDB = oxDb::getDB();
        $sShopId = $myConfig->getShopId();
        // adding article to recommendlist
        $sQ = 'replace into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $sQ = 'replace into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "1651", "testlist", "test" ),' .
              ' ( "testlist2", "2000", "testlist", "test" ), ( "testlist3", "1126", "testlist", "test" ) ';
        $myDB->Execute($sQ);

        $oRecomm = new oxRecommList();
        $oRecomm->load("testlist");

        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue('1651'));

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType', 'addTplParam', 'setSearchTitle', 'getSearchTitle', 'getActiveRecommList'));
        $oLocatorTarget->expects($this->any())->method('addTplParam');
        $oLocatorTarget->expects($this->any())->method('setSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecomm));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        modConfig::setRequestParameter("searchrecomm", null);
        $oLocator = new testOxLocator();
        $oLocator->UNITsetRecommListLocatorData($oLocatorTarget, $oCurrArticle);

        $this->assertEquals(2, $oRecomm->iProductPos);
        $this->assertEquals(3, $oRecomm->iCntOfProd);
        $sPrevLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1126&amp;recommid=testlist&amp;listtype=recommlist";
        $sNextLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=2000&amp;recommid=testlist&amp;listtype=recommlist";

        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=recommlist&amp;recommid=testlist&amp;pgNr=1", $oRecomm->toListLink);
        $this->assertEquals($sPrevLink, $oRecomm->prevProductLink);
        $this->assertEquals($sNextLink, $oRecomm->nextProductLink);
    }

    public function testLoadIdsInList()
    {
        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->once())->method('getId')->will($this->returnValue('1651'));

        $sActCat = '30e44ab83fdee7564.23264141';
        $sActCat = '8a142c3e4143562a5.46426637';

        $oLocator = new testOxLocator();

        $oCategory = new oxcategory();
        $oCategory->load($sActCat);

        // testing
        $oIdList = $oLocator->UNITloadIdsInList($oCategory, $oCurrArticle, 'oxid');
        $this->assertEquals('1651', $oIdList['1651']);


        $this->assertEquals(32, $oIdList->count());
    }

    public function testLoadIdsInListNonExistingArticle()
    {
        $oCurrArticle = $this->getMock('oxarticle', array('getId'));
        $oCurrArticle->expects($this->once())->method('getId')->will($this->returnValue('xxx'));

        $sActCat = '30e44ab83fdee7564.23264141';
        $sActCat = '8a142c3e4143562a5.46426637';

        $oLocator = new testOxLocator();

        $oCategory = new oxcategory();
        $oCategory->load($sActCat);

        // testing
        $oIdList = $oLocator->UNITloadIdsInList($oCategory, $oCurrArticle, 'oxid');
        $this->assertEquals('1651', $oIdList['1651']);


        $this->assertEquals(32, $oIdList->count());
    }

    public function testLoadIdsInListForPriceCat()
    {
        $sInsert = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXTITLE`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`) " .
                   "values ('test','test','test','1','10','50')";


        oxDb::getDb()->Execute($sInsert);

        $oLocator = new testOxLocator();

        $oCategory = oxNew("oxcategory");
        $oCategory->oxcategories__oxtitle = new oxField('test', oxField::T_RAW);
        $oCategory->oxcategories__oxpricefrom = new oxField(10, oxField::T_RAW);
        $oCategory->oxcategories__oxpriceto = new oxField(50, oxField::T_RAW);

        // testing
        $oIdList = $oLocator->UNITloadIdsInList($oCategory, new oxarticle(), 'oxid');
        $this->assertEquals('1651', $oIdList['1651']);

        $this->assertEquals(24, count($oIdList));
    }

    public function testLoadIdsInListForPriceCatNonExistingArticle()
    {
        $sInsert = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXTITLE`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`) " .
                   "values ('test','test','test','1','10','50')";


        oxDb::getDb()->Execute($sInsert);

        $oLocator = new testOxLocator();

        $oCategory = oxNew("oxcategory");
        $oCategory->oxcategories__oxtitle = new oxField('test', oxField::T_RAW);
        $oCategory->oxcategories__oxpricefrom = new oxField(10, oxField::T_RAW);
        $oCategory->oxcategories__oxpriceto = new oxField(50, oxField::T_RAW);

        // testing
        $oIdList = $oLocator->UNITloadIdsInList($oCategory, new oxarticle(), 'oxid');
        $this->assertEquals('1651', $oIdList['1651']);

        $this->assertEquals(24, count($oIdList));
    }

    public function testGetPageNumber()
    {
        $oLocator = new oxlocator();
        $this->assertEquals('pgNr=5', $oLocator->UNITgetPageNumber(5));
        $this->assertEquals('', $oLocator->UNITgetPageNumber(-3));
        $this->assertEquals('', $oLocator->UNITgetPageNumber('nonumber'));
    }

    public function testGetProductPos()
    {
        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType'));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $aTest = array();
        $sBackId = $aTest["1951"] = "1951";
        $aTest["2000"] = "2000";
        $sNextId = $aTest["1771"] = "1771";
        $aTest["2028"] = "2028";

        $oTest = new oxlist();
        $oTest->assign($aTest);

        $oArticle = $this->getMock('oxarticle', array('getId'));
        $oArticle->expects($this->any())->method('getId')->will($this->returnValue('2000'));
        $oArticle->oxarticles__oxparentid = new oxField(null, oxField::T_RAW);

        $oLocator = new testOxLocator();
        $this->assertEquals($oLocator->UNITgetProductPos($oArticle, $oTest, $oLocatorTarget), 2);

        $this->assertNotNull($oLocator->_oBackProduct);
        $this->assertNotNull($oLocator->_oNextProduct);

        $this->assertEquals($oLocator->_oBackProduct->getId(), $sBackId);
        $this->assertEquals($oLocator->_oNextProduct->getId(), $sNextId);

        $aTest = array();
        $aTest["1951"] = "1951";
        $sBackId = $aTest["1771"] = "1771";
        $aTest["2000"] = "2000";

        $oTest = new oxlist();
        $oTest->assign($aTest);

        $oLocator = new testOxLocator();
        $this->assertEquals($oLocator->UNITgetProductPos($oArticle, $oTest, $oLocatorTarget), 3);

        $this->assertNotNull($oLocator->_oBackProduct);
        $this->assertEquals($oLocator->_oBackProduct->getId(), $sBackId);
        $this->assertNull($oLocator->_oNextProduct);

        $aTest = array();
        $aTest["2000"] = "2000";
        $sNextId = $aTest["1771"] = "1771";
        $aTest["1951"] = "1951";

        $oTest = new oxlist();
        $oTest->assign($aTest);

        $oArticle = $this->getMock('oxarticle', array('getId'));
        $oArticle->expects($this->any())->method('getId')->will($this->returnValue('2000-1'));
        $oArticle->oxarticles__oxparentid = new oxField('2000', oxField::T_RAW);

        $oLocator = new testOxLocator();
        $this->assertEquals($oLocator->UNITgetProductPos($oArticle, $oTest, $oLocatorTarget), 1);
        $this->assertEquals($oLocator->_oBackProduct, null);

        $this->assertNotNull($oLocator->_oNextProduct);
        $this->assertEquals($oLocator->_oNextProduct->getId(), $sNextId);

        $aTest = array();
        $this->assertEquals(0, $oLocator->UNITgetProductPos($oArticle, new oxlist(), $oLocatorTarget));
    }

    /**
     * #0006220 test case
     */
    public function testGetProductPosMixTypeKeys()
    {
        $oArticle = $this->_insertArticle('1234567');
        $oOtherArticle = $this->_insertArticle('1234567A');

        $oLocatorTarget = $this->getMock('oxubase', array('getLinkType'));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $array = array();
        $array['943ed656e21971fb2f1827facbba9bec'] = '943ed656e21971fb2f1827facbba9bec';
        $array['1234567'] = '1234567';
        $array['1234567A'] = '1234567A';
        $array['6b6e718666bc8867719ab25a8020a978'] = '6b6e718666bc8867719ab25a8020a978';

        $oList = new oxlist();
        $oList->assign($array);

        $oLocator = new testOxLocator();
        $this->assertSame(2, $oLocator->UNITgetProductPos($oArticle, $oList, $oLocatorTarget));
        $this->assertNotNull($oLocator->_oBackProduct);
        $this->assertNotNull($oLocator->_oNextProduct);
        $this->assertSame('943ed656e21971fb2f1827facbba9bec', $oLocator->_oBackProduct->getId());
        $this->assertSame('1234567A', $oLocator->_oNextProduct->getId());

        $oLocator = new testOxLocator();
        $this->assertSame(3, $oLocator->UNITgetProductPos($oOtherArticle, $oList, $oLocatorTarget));
        $this->assertNotNull($oLocator->_oBackProduct);
        $this->assertNotNull($oLocator->_oNextProduct);
        $this->assertSame('1234567', $oLocator->_oBackProduct->getId());
        $this->assertSame('6b6e718666bc8867719ab25a8020a978', $oLocator->_oNextProduct->getId());

    }

    /**
     * Make a copy of article for testing.
     *
     * @param string $sOxid Set this oxid for the article copy.
     *
     * @return oxArticle
     */
    private function _insertArticle($sOxid = '1234567')
    {
        $oArticle = oxNew('oxarticle');
        $oArticle->disableLazyLoading();
        $oArticle->load(self::SOURCE_ARTICLE_ID);
        $oArticle->setId($sOxid );
        $oArticle->save();

        return $oArticle;
    }
}
