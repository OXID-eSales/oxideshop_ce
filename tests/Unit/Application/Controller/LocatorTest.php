<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use oxArticle;
use \oxLocator;
use \oxField;
use \oxlist;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

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

class LocatorTest extends \OxidTestCase
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
        $this->_iSeoMode = $this->getConfig()->getActiveShop()->oxshops__oxseoactive->value;
        $this->getConfig()->getActiveShop()->oxshops__oxseoactive = new oxField(0, oxField::T_RAW);

        oxRegistry::getUtils()->seoIsActive(true);

        $this->setRequestParameter("listtype", null);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->getConfig()->setGlobalParameter('listtype', null);

        $sDelete = "Delete from oxcategories where oxtitle = 'test'";
        oxDb::getDb()->Execute($sDelete);
        oxDb::getDb()->execute('delete from oxrecommlists where oxid like "testlist%" ');
        oxDb::getDb()->execute('delete from oxobject2list where oxlistid like "testlist%" ');
        oxDb::getDb()->execute('delete from oxarticles where oxid like "%1234567%" ');

        // restoring
        $this->getConfig()->getActiveShop()->oxshops__oxseoactive = new oxField($this->_iSeoMode, oxField::T_RAW);

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
        $oLocator = oxNew('oxLocator');
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
        $oCurrArticle = oxNew('oxArticle');

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('setListType'));
        $oLocatorTarget->expects($this->once())->method('setListType');

        $oLocator = $this->getMock(\OxidEsales\Eshop\Application\Component\Locator::class, array('_setListLocatorData'));
        $oLocator->expects($this->once())->method('_setListLocatorData')->with($this->equalTo($oLocatorTarget), $this->equalTo($oCurrArticle));
        $oLocator->setLocatorData($oCurrArticle, $oLocatorTarget, 'xxx');
    }

    public function testSetListLocatorData()
    {
        // seo off
        $this->getConfig()->setConfigParam('blSeoMode', false);
        $numberOfCategoryArticles = $this->getTestConfig()->getShopEdition() == 'EE'? 0 : 10;
        $this->getConfig()->setConfigParam('iNrofCatArticles', $numberOfCategoryArticles);

        oxRegistry::getUtils()->seoIsActive(true);

        $config = $this->getConfig();

        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue('1651'));

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sActCat = '30e44ab83fdee7564.23264141';
            $sPrevId = '1351';
            $sNextId = '1661';
        } else {
            $sActCat = '8a142c3e4143562a5.46426637';
            $sPrevId = '1477';
            $sNextId = '1672';
        }

        $oCategory = oxNew('oxcategory');
        $oCategory->load($sActCat);

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActiveCategory', 'getCategoryTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $oLocatorTarget->expects($this->once())->method('getCategoryTree')->will($this->returnValue(oxNew('oxcategorylist')));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $oLocator = new testOxLocator();

        $oLocator->UNITsetListLocatorData($oLocatorTarget, $oCurrArticle);

        $expectedPosition = $this->getTestConfig()->getShopEdition() == 'EE'? 3 : 9;
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE'? 6 : 32;
        $this->assertEquals($expectedPosition, $oCategory->iProductPos);
        $this->assertEquals($expectedCount, $oCategory->iCntOfProd);

        $iPgNr = $this->getTestConfig()->getShopEdition() == 'EE'? 2 : 0;
        $this->assertEquals($config->getShopHomeUrl() . "cl=alist&amp;cnid={$sActCat}" . (($iPgNr) ? "&amp;pgNr={$iPgNr}" : ""), $oCategory->toListLink);
        $this->assertEquals($config->getShopHomeUrl() . "cl=details&amp;anid=" . $sNextId, $oCategory->nextProductLink);
        $this->assertEquals($config->getShopHomeUrl() . "cl=details&amp;anid=" . $sPrevId, $oCategory->prevProductLink);
    }

    public function testSetListLocatorDataSeo()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue('1651'));

        $sActCat = $this->getTestConfig()->getShopEdition() == 'EE'? '30e44ab83fdee7564.23264141' : '8a142c3e4143562a5.46426637';

        $oCategory = oxNew('oxCategory');
        $oCategory->load($sActCat);

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActiveCategory', 'getCategoryTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $oLocatorTarget->expects($this->once())->method('getCategoryTree')->will($this->returnValue(oxNew('oxcategorylist')));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(true));

        $oLocator = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\testOxLocator::class, array('getConfig'));
        $oLocator->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        // testing
        $oLocator->UNITsetListLocatorData($oLocatorTarget, $oCurrArticle);

        $sShopUrl = $this->getConfig()->getShopUrl();

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sToListLink = $sShopUrl . 'Party/?pgNr=2';
            $sPrevProdLink = $sShopUrl . 'Party/Bar-Equipment/Kuehlwuerfel-NORDIC-ROCKS-Eiswuerfel-Ersatz.html';
            $sNextProdLink = $sShopUrl . 'Party/Schuerze-BAVARIA.html';
        } else {
            $sToListLink = $sShopUrl . 'Geschenke/?pgNr=8';
            $sNextProdLink = $sShopUrl . 'Geschenke/Wohnen/Uhren/Wanduhr-PHOTOFRAME.html';
            $sPrevProdLink = $sShopUrl . 'Geschenke/Bar-Equipment/Champagnerverschluss-GOLF.html';
        }
        $expectedPosition = $this->getTestConfig()->getShopEdition() == 'EE'? 3 : 9;
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE'? 6 : 32;
        $this->assertEquals($expectedPosition, $oCategory->iProductPos);
        $this->assertEquals($expectedCount, $oCategory->iCntOfProd);

        $this->assertEquals($sToListLink, $oCategory->toListLink);
        $this->assertEquals($sNextProdLink, $oCategory->nextProductLink);
        $this->assertEquals($sPrevProdLink, $oCategory->prevProductLink);
    }

    public function testSetVendorLocatorData()
    {
        $this->switchOffSeo();

        $myConfig = $this->getConfig();

        $sArt = '1964';
        $sPrevLink = '';
        $sNextLink = '';
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sArt = '1142';
            $sNextLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1477&amp;listtype=vendor&amp;cnid=v_d2e44d9b31fcce448.08890330";
            $sPrevLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1131&amp;listtype=vendor&amp;cnid=v_d2e44d9b31fcce448.08890330";
        }
        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArt));

        $sActCat = $this->getTestConfig()->getShopEdition() == 'EE'? 'v_d2e44d9b31fcce448.08890330' : 'v_77442e37fdf34ccd3.94620745';

        $oVendor = oxNew('oxVendor');
        $oVendor->load(str_replace('v_', '', $sActCat));

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActVendor', 'getVendorTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActVendor')->will($this->returnValue($oVendor));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_VENDOR));

        $oLocator = new testOxLocator();
        $oLocator->UNITsetVendorLocatorData($oLocatorTarget, $oCurrArticle);

        $expectedPosition = $this->getTestConfig()->getShopEdition() == 'EE'? 2 : 1;
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE'? 14 : 1;
        $this->assertEquals($expectedPosition, $oVendor->iProductPos);
        $this->assertEquals($expectedCount, $oVendor->iCntOfProd);

        $sPgNr = $this->getTestConfig()->getShopEdition() == 'EE'? "&amp;pgNr=1" : '';
        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=vendorlist&amp;cnid={$sActCat}{$sPgNr}", $oVendor->toListLink);
        $this->assertEquals($sNextLink, $oVendor->nextProductLink);
        $this->assertEquals($sPrevLink, $oVendor->prevProductLink);
    }

    public function testSetVendorLocatorDataSeo()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $sArt = $this->getTestConfig()->getShopEdition() == 'EE'? '1142' : '1964';
        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArt));
        $oCurrArticle->setLinkType(1);

        $sActCat = $this->getTestConfig()->getShopEdition() == 'EE'? 'v_d2e44d9b31fcce448.08890330' : 'v_77442e37fdf34ccd3.94620745';

        $oVendor = oxNew('oxVendor');
        $oVendor->load(str_replace('v_', '', $sActCat));

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActVendor', 'getVendorTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActVendor')->will($this->returnValue($oVendor));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_VENDOR));

        $oLocator = new testOxLocator();
        $oLocator->UNITsetVendorLocatorData($oLocatorTarget, $oCurrArticle);

        $sShopUrl = $this->getConfig()->getShopUrl();

        $expectedPosition = $this->getTestConfig()->getShopEdition() == 'EE' ? 2 : 1;
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 14 : 1;
        $this->assertEquals($expectedPosition, $oVendor->iProductPos);
        $this->assertEquals($expectedCount, $oVendor->iCntOfProd);

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sToListLink = $sShopUrl . 'Nach-Lieferant/Hersteller-1/?pgNr=1';
            $sPrevProdLink = $sShopUrl . 'Nach-Lieferant/Hersteller-1/Flaschenverschluss-EGO.html';
            $sNextProdLink = $sShopUrl . 'Nach-Lieferant/Hersteller-1/Champagnerverschluss-GOLF.html';
        } else {
            $sToListLink = $sShopUrl . 'Nach-Lieferant/Bush/';
            $sPrevProdLink = null;
            $sNextProdLink = null;
        }

        $this->assertEquals($sToListLink, $oVendor->toListLink);
        $this->assertEquals($sNextProdLink, $oVendor->nextProductLink);
        $this->assertEquals($sPrevProdLink, $oVendor->prevProductLink);
    }

    public function testSetManufacturerLocatorData()
    {
        $this->switchOffSeo();

        $myConfig = $this->getConfig();

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sArt = '1142';
            $sNextLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1477&amp;listtype=manufacturer&amp;mnid=" . md5("d2e44d9b31fcce448.08890330");
            $sPrevLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=1131&amp;listtype=manufacturer&amp;mnid=" . md5("d2e44d9b31fcce448.08890330");
        } else {
            $sArt = '1964';
            $sPrevLink = '';
            $sNextLink = '';
        }
        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArt));

        $sActCat = $this->getTestConfig()->getShopEdition() == 'EE'? md5('d2e44d9b31fcce448.08890330') : md5('77442e37fdf34ccd3.94620745');

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->load($sActCat);

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActManufacturer', 'getManufacturerTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActManufacturer')->will($this->returnValue($oManufacturer));
        $oLocatorTarget->expects($this->once())->method('getManufacturerTree')->will($this->returnValue(oxNew('oxmanufacturerlist')));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_MANUFACTURER));

        $oLocator = new testOxLocator();
        $oLocator->UNITsetManufacturerLocatorData($oLocatorTarget, $oCurrArticle);

        $expectedPosition = $this->getTestConfig()->getShopEdition() == 'EE' ? 2 : 1;
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 14 : 1;
        $this->assertEquals($expectedPosition, $oManufacturer->iProductPos);
        $this->assertEquals($expectedCount, $oManufacturer->iCntOfProd);

        $sPgNr = $this->getTestConfig()->getShopEdition() == 'EE' ? "&amp;pgNr=1" : '';
        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=manufacturerlist&amp;mnid={$sActCat}{$sPgNr}", $oManufacturer->toListLink);
        $this->assertEquals($sNextLink, $oManufacturer->nextProductLink);
        $this->assertEquals($sPrevLink, $oManufacturer->prevProductLink);
    }

    public function testSetManufacturerLocatorDataSeo()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $sArt = $this->getTestConfig()->getShopEdition() == 'EE'? '1142' : '1964';
        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArt));
        $oCurrArticle->setLinkType(1);

        $sActCat = $this->getTestConfig()->getShopEdition() == 'EE'? md5('d2e44d9b31fcce448.08890330') : md5('77442e37fdf34ccd3.94620745');

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->load($sActCat);

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType', 'getSortingSql', 'setCatTreePath', 'getCatTreePath', 'getActManufacturer', 'getManufacturerTree', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('setCatTreePath');
        $oLocatorTarget->expects($this->any())->method('getCatTreePath');
        $oLocatorTarget->expects($this->once())->method('getActManufacturer')->will($this->returnValue($oManufacturer));
        $oLocatorTarget->expects($this->once())->method('getManufacturerTree')->will($this->returnValue(oxNew('oxmanufacturerlist')));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_MANUFACTURER));

        $oLocator = new testOxLocator();
        $oLocator->UNITsetManufacturerLocatorData($oLocatorTarget, $oCurrArticle);

        $sShopUrl = $this->getConfig()->getShopUrl();

        $expectedPosition = $this->getTestConfig()->getShopEdition() == 'EE' ? 2 : 1;
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 14 : 1;
        $this->assertEquals($expectedPosition, $oManufacturer->iProductPos);
        $this->assertEquals($expectedCount, $oManufacturer->iCntOfProd);

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sToListLink = $sShopUrl . 'Nach-Hersteller/Hersteller-1/?pgNr=1';
            $sPrevProdLink = $sShopUrl . 'Nach-Hersteller/Hersteller-1/Flaschenverschluss-EGO.html';
            $sNextProdLink = $sShopUrl . 'Nach-Hersteller/Hersteller-1/Champagnerverschluss-GOLF.html';
        } else {
            $sToListLink = $sShopUrl . 'Nach-Hersteller/Bush/';
            $sPrevProdLink = null;
            $sNextProdLink = null;
        }

        $this->assertEquals($sToListLink, $oManufacturer->toListLink);
        $this->assertEquals($sNextProdLink, $oManufacturer->nextProductLink);
        $this->assertEquals($sPrevProdLink, $oManufacturer->prevProductLink);
    }

    public function testSetSearchLocatorData()
    {
        $this->switchOffSeo();

        $config = $this->getConfig();

        $sArtId = '1651';
        $sPrevLink = '';
        $sNextLink = '';
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sPrevLink = $config->getShopHomeUrl() . "cl=details&amp;anid=1651&amp;searchparam=Bier&amp;listtype=search";
            $sNextLink = $config->getShopHomeUrl() . "cl=details&amp;anid=2357&amp;searchparam=Bier&amp;listtype=search";
            $sArtId = '1889';
        }

        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArtId));

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType', 'getSortingSql', 'addTplParam', 'setSearchTitle', 'getSearchTitle', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('addTplParam');
        $oLocatorTarget->expects($this->any())->method('setSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $this->setRequestParameter("searchparam", 'Bier');
        $oLocator = new testOxLocator();
        $oLocator->UNITsetSearchLocatorData($oLocatorTarget, $oCurrArticle);
        $this->setRequestParameter("searchparam", null);

        $oSearch = $oLocatorTarget->getActSearch();

        $expectedPosition = $this->getTestConfig()->getShopEdition() == 'EE' ? 2 : 1;
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 3 : 1;
        $this->assertEquals($expectedPosition, $oSearch->iProductPos);
        $this->assertEquals($expectedCount, $oSearch->iCntOfProd);

        $sPgNr = $this->getTestConfig()->getShopEdition() == 'EE' ? "&amp;pgNr=1" : '';
        $this->assertEquals($config->getShopHomeUrl() . "cl=search{$sPgNr}&amp;searchparam=Bier&amp;listtype=search", $oSearch->toListLink);
        $this->assertEquals($sNextLink, $oSearch->nextProductLink);
        $this->assertEquals($sPrevLink, $oSearch->prevProductLink);
    }

    public function testSetSearchLocatorDataFromVendor()
    {
        $this->switchOffSeo();

        $config = $this->getConfig();

        $sArt = '5065';
        $sPrevId = '5064';
        $sNextId = '5067';
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sArt = '1142';
            $sPrevId = '1131';
            $sNextId = '1477';
        }

        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue($sArt));

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType', 'prepareSortColumns', 'getSortingSql', 'addTplParam', 'setSearchTitle', 'getSearchTitle', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('addTplParam');
        $oLocatorTarget->expects($this->any())->method('setSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $sSearchVendor = $this->getTestConfig()->getShopEdition() == 'EE'? 'd2e44d9b31fcce448.08890330' : '68342e2955d7401e6.18967838';
        $this->setRequestParameter("searchparam", 'a');
        $this->setRequestParameter("searchvendor", $sSearchVendor);
        $oLocator = new testOxLocator();
        $oLocator->UNITsetSearchLocatorData($oLocatorTarget, $oCurrArticle);
        $this->setRequestParameter("searchparam", null);
        $this->setRequestParameter("searchvendor", null);

        $oSearch = $oLocatorTarget->getActSearch();

        $expectedPosition = 2;
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 12 : 5;
        $this->assertEquals($expectedPosition, $oSearch->iProductPos);
        $this->assertEquals($expectedCount, $oSearch->iCntOfProd);

        $iPgNr = 1;
        $this->assertEquals($config->getShopHomeUrl() . "cl=search&amp;pgNr={$iPgNr}&amp;searchparam=a&amp;listtype=search&amp;searchvendor={$sSearchVendor}", $oSearch->toListLink);
        $this->assertEquals($config->getShopHomeUrl() . "cl=details&amp;anid={$sNextId}&amp;searchparam=a&amp;listtype=search&amp;searchvendor={$sSearchVendor}", $oSearch->nextProductLink);
        $this->assertEquals($config->getShopHomeUrl() . "cl=details&amp;anid={$sPrevId}&amp;searchparam=a&amp;listtype=search&amp;searchvendor={$sSearchVendor}", $oSearch->prevProductLink);
    }

    public function testSetSearchLocatorDataFromCat()
    {
        $this->switchOffSeo();

        $myConfig = $this->getConfig();

        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue('1651'));

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType', 'prepareSortColumns', 'getSortingSql', 'addTplParam', 'setSearchTitle', 'getSearchTitle', 'showSorting'));
        $oLocatorTarget->expects($this->once())->method('getSortingSql')->with($this->equalTo('alist'))->will($this->returnValue('oxid'));
        $oLocatorTarget->expects($this->any())->method('addTplParam');
        $oLocatorTarget->expects($this->any())->method('setSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));


        $sSearchCat = '8a142c3e4143562a5.46426637';
        $sNextLink = '';
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sSearchCat = '30e44ab841af13e46.42570689';
            $sNextLink = $myConfig->getShopHomeUrl() . "cl=details&amp;anid=2357&amp;searchparam=Bier&amp;listtype=search&amp;searchcnid=$sSearchCat";
        }

        $this->setRequestParameter("searchparam", 'Bier');
        $this->setRequestParameter("searchcnid", $sSearchCat);
        $oLocator = new testOxLocator();
        $oLocator->UNITsetSearchLocatorData($oLocatorTarget, $oCurrArticle);
        $this->setRequestParameter("searchparam", null);
        $this->setRequestParameter("searchcnid", null);

        $oSearch = $oLocatorTarget->getActSearch();

        $expectedPosition = 1;
        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 2 : 1;
        $this->assertEquals($expectedPosition, $oSearch->iProductPos);
        $this->assertEquals($expectedCount, $oSearch->iCntOfProd);

        $this->assertEquals($myConfig->getShopHomeUrl() . "cl=search&amp;searchparam=Bier&amp;listtype=search&amp;searchcnid=$sSearchCat", $oSearch->toListLink);
        $this->assertEquals($sNextLink, $oSearch->nextProductLink);
        $this->assertNull($oSearch->prevProductLink);
    }

    // set locator data after recommlist search
    public function testSetRecommListLocatorData()
    {
        oxTestModules::addFunction('oxarticlelist', 'loadRecommArticleIds', '{parent::loadRecommArticleIds($aA[0], " order by oxobject2list.oxobjectid asc" );}');
        $myConfig = $this->getConfig();

        $this->switchOffSeo();

        $myDB = oxDb::getDB();
        $sShopId = $myConfig->getShopId();
        // adding article to recommendlist
        $sQ = 'replace into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $sQ = 'replace into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "1651", "testlist", "test" ),' .
              ' ( "testlist2", "2000", "testlist", "test" ), ( "testlist3", "1126", "testlist", "test" ) ';
        $myDB->Execute($sQ);

        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load("testlist");

        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue('1651'));

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType', 'addTplParam', 'setSearchTitle', 'getSearchTitle', 'getActiveRecommList'));
        $oLocatorTarget->expects($this->any())->method('addTplParam');
        $oLocatorTarget->expects($this->any())->method('setSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecomm));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_RECOMM));

        $this->setRequestParameter("searchrecomm", 'test');
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
        $myConfig = $this->getConfig();

        $this->switchOffSeo();

        $myDB = oxDb::getDB();
        $sShopId = $myConfig->getShopId();
        // adding article to recommendlist
        $sQ = 'replace into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $sQ = 'replace into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "1651", "testlist", "test" ),' .
              ' ( "testlist2", "2000", "testlist", "test" ), ( "testlist3", "1126", "testlist", "test" ) ';
        $myDB->Execute($sQ);

        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load("testlist");

        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->any())->method('getId')->will($this->returnValue('1651'));

        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType', 'addTplParam', 'setSearchTitle', 'getSearchTitle', 'getActiveRecommList'));
        $oLocatorTarget->expects($this->any())->method('addTplParam');
        $oLocatorTarget->expects($this->any())->method('setSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getSearchTitle');
        $oLocatorTarget->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecomm));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $this->setRequestParameter("searchrecomm", null);
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
        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->once())->method('getId')->will($this->returnValue('1651'));

        $sActCat = $this->getTestConfig()->getShopEdition() == 'EE'? '30e44ab83fdee7564.23264141' : '8a142c3e4143562a5.46426637';

        $oLocator = new testOxLocator();

        $oCategory = oxNew('oxcategory');
        $oCategory->load($sActCat);

        // testing
        $oIdList = $oLocator->UNITloadIdsInList($oCategory, $oCurrArticle, 'oxid');
        $this->assertEquals('1651', $oIdList['1651']);

        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE'? 6 : 32;
        $this->assertEquals($expectedCount, $oIdList->count());
    }

    public function testLoadIdsInListNonExistingArticle()
    {
        $oCurrArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oCurrArticle->expects($this->once())->method('getId')->will($this->returnValue('xxx'));

        $sActCat = $this->getTestConfig()->getShopEdition() == 'EE'? '30e44ab83fdee7564.23264141' : '8a142c3e4143562a5.46426637';

        $oLocator = new testOxLocator();

        $oCategory = oxNew('oxcategory');
        $oCategory->load($sActCat);

        // testing
        $oIdList = $oLocator->UNITloadIdsInList($oCategory, $oCurrArticle, 'oxid');
        $this->assertEquals('1651', $oIdList['1651']);

        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE'? 6 : 32;
        $this->assertEquals($expectedCount, $oIdList->count());
    }

    public function testLoadIdsInListForPriceCat()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $query = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXTITLE`,`OXACTIVE`,`OXPRICEFROM`," .
                "`OXPRICETO`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`)
                       values ('test','test','test','1','10','50','','','','')";
        } else {
            $query = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXTITLE`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`) " .
                "values ('test','test','test','1','10','50')";
        }

        oxDb::getDb()->execute($query);

        $oLocator = new testOxLocator();

        $oCategory = oxNew("oxCategory");
        $oCategory->oxcategories__oxtitle = new oxField('test', oxField::T_RAW);
        $oCategory->oxcategories__oxpricefrom = new oxField(10, oxField::T_RAW);
        $oCategory->oxcategories__oxpriceto = new oxField(50, oxField::T_RAW);

        // testing
        $oIdList = $oLocator->UNITloadIdsInList($oCategory, oxNew('oxArticle'), 'oxid');
        $this->assertEquals('1651', $oIdList['1651']);

        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE'? 34 : 24;
        $this->assertEquals($expectedCount, count($oIdList));
    }

    public function testLoadIdsInListForPriceCatNonExistingArticle()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $query = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXTITLE`,`OXACTIVE`,`OXPRICEFROM`," .
                "`OXPRICETO`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`)
                       values ('test','test','test','1','10','50','','','','')";
        } else {
            $query = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXTITLE`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`) " .
                "values ('test','test','test','1','10','50')";
        }

        oxDb::getDb()->execute($query);

        $oLocator = new testOxLocator();

        $oCategory = oxNew("oxCategory");
        $oCategory->oxcategories__oxtitle = new oxField('test', oxField::T_RAW);
        $oCategory->oxcategories__oxpricefrom = new oxField(10, oxField::T_RAW);
        $oCategory->oxcategories__oxpriceto = new oxField(50, oxField::T_RAW);

        // testing
        $oIdList = $oLocator->UNITloadIdsInList($oCategory, oxNew('oxArticle'), 'oxid');
        $this->assertEquals('1651', $oIdList['1651']);

        $expectedCount = $this->getTestConfig()->getShopEdition() == 'EE'? 34 : 24;
        $this->assertEquals($expectedCount, count($oIdList));
    }

    public function testGetPageNumber()
    {
        $oLocator = oxNew('oxlocator');
        $this->assertEquals('pgNr=5', $oLocator->UNITgetPageNumber(5));
        $this->assertEquals('', $oLocator->UNITgetPageNumber(-3));
        $this->assertEquals('', $oLocator->UNITgetPageNumber('nonumber'));
    }

    public function testGetProductPos()
    {
        $oLocatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType'));
        $oLocatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $aTest = array();
        $sBackId = $aTest["1951"] = "1951";
        $aTest["2000"] = "2000";
        $sNextId = $aTest["1771"] = "1771";
        $aTest["2028"] = "2028";

        $oTest = oxNew('oxlist');
        $oTest->assign($aTest);

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
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

        $oTest = oxNew('oxlist');
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

        $oTest = oxNew('oxlist');
        $oTest->assign($aTest);

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getId'));
        $oArticle->expects($this->any())->method('getId')->will($this->returnValue('2000-1'));
        $oArticle->oxarticles__oxparentid = new oxField('2000', oxField::T_RAW);

        $oLocator = new testOxLocator();
        $this->assertEquals($oLocator->UNITgetProductPos($oArticle, $oTest, $oLocatorTarget), 1);
        $this->assertEquals($oLocator->_oBackProduct, null);

        $this->assertNotNull($oLocator->_oNextProduct);
        $this->assertEquals($oLocator->_oNextProduct->getId(), $sNextId);

        $this->assertEquals(0, $oLocator->UNITgetProductPos($oArticle, new oxlist(), $oLocatorTarget));
    }

    /**
     * #0006220 test case
     */
    public function testGetProductPosMixTypeKeys()
    {
        $article = $this->insertArticle('1234567');
        $otherArticle = $this->insertArticle('1234567A');

        $locatorTarget = $this->getMock(\OxidEsales\Eshop\Application\Controller\FrontendController::class, array('getLinkType'));
        $locatorTarget->expects($this->any())->method('getLinkType')->will($this->returnValue(OXARTICLE_LINKTYPE_CATEGORY));

        $array = array();
        $array['943ed656e21971fb2f1827facbba9bec'] = '943ed656e21971fb2f1827facbba9bec';
        $array['1234567'] = '1234567';
        $array['1234567A'] = '1234567A';
        $array['6b6e718666bc8867719ab25a8020a978'] = '6b6e718666bc8867719ab25a8020a978';

        $list= new oxlist();
        $list->assign($array);

        $locator = new testOxLocator();
        $this->assertSame(2, $locator->UNITgetProductPos($article, $list, $locatorTarget));
        $this->assertNotNull($locator->_oBackProduct);
        $this->assertNotNull($locator->_oNextProduct);
        $this->assertSame('943ed656e21971fb2f1827facbba9bec', $locator->_oBackProduct->getId());
        $this->assertSame('1234567A', $locator->_oNextProduct->getId());

        $locator = new testOxLocator();
        $this->assertSame(3, $locator->UNITgetProductPos($otherArticle, $list, $locatorTarget));
        $this->assertNotNull($locator->_oBackProduct);
        $this->assertNotNull($locator->_oNextProduct);
        $this->assertSame('1234567', $locator->_oBackProduct->getId());
        $this->assertSame('6b6e718666bc8867719ab25a8020a978', $locator->_oNextProduct->getId());
    }

    /**
     * Switch the SEO functionality off and reset the seo use cache.
     */
    protected function switchOffSeo()
    {
        $this->setConfigParam('blSeoMode', false);
        oxRegistry::getUtils()->seoIsActive(true);
    }

    /**
     * Make a copy of article for testing.
     *
     * @param string $oxid Set this oxid for the article copy.
     *
     * @return oxArticle
     */
    private function insertArticle($oxid = '1234567')
    {
        $article = oxNew('oxarticle');
        $article->disableLazyLoading();
        $article->load(self::SOURCE_ARTICLE_ID);
        $article->setId($oxid);
        $article->save();

        return $article;
    }
}
