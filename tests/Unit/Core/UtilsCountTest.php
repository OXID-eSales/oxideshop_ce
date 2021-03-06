<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxField;
use \oxDb;
use \oxRegistry;

class UtilsCountTest extends \OxidTestCase
{
    /** @var array */
    private $categories = array();

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $oPriceCat = oxNew('oxcategory');
        $oPriceCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oPriceCat->oxcategories__oxparentid = new oxField("oxrootid", oxField::T_RAW);
        $oPriceCat->oxcategories__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oPriceCat->oxcategories__oxtitle = new oxField("Price Cat 1", oxField::T_RAW);
        $oPriceCat->oxcategories__oxpricefrom = new oxField(1, oxField::T_RAW);
        $oPriceCat->oxcategories__oxpriceto = new oxField(100, oxField::T_RAW);
        $oPriceCat->save();

        $this->categories[$oPriceCat->getId()] = $oPriceCat;

        $oPriceCat = oxNew('oxcategory');
        $oPriceCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oPriceCat->oxcategories__oxparentid = new oxField("oxrootid", oxField::T_RAW);
        $oPriceCat->oxcategories__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oPriceCat->oxcategories__oxtitle = new oxField("Price Cat 2", oxField::T_RAW);
        $oPriceCat->oxcategories__oxpricefrom = new oxField(1, oxField::T_RAW);
        $oPriceCat->oxcategories__oxpriceto = new oxField(100, oxField::T_RAW);
        $oPriceCat->save();

        $this->categories[$oPriceCat->getId()] = $oPriceCat;

        $this->getConfig()->setGlobalParameter('aLocalVendorCache', null);
        oxRegistry::getUtils()->toFileCache('aLocalVendorCache', '');
        oxRegistry::getUtils()->toFileCache('aLocalCatCache', '');
    }

    protected function tearDown(): void
    {
        foreach ($this->categories as $category) {
            /** @var oxCategory $category */
            $category->delete();
        }

        $this->getConfig()->setGlobalParameter('aLocalVendorCache', null);
        oxRegistry::getUtils()->toFileCache('aLocalVendorCache', '');
        oxRegistry::getUtils()->toFileCache('aLocalCatCache', '');

        oxRegistry::getUtils()->oxResetFileCache();

        // deleting test articles
        $article = oxNew('oxarticle');
        $article->delete('testarticle1');
        $article->delete('testarticle2');
        $article->delete('testArticle');

        $this->cleanUpTable('oxarticles');

        parent::tearDown();
    }

    public function testSetPriceCatArticleCountWhenPriceFrom0To1AndDbContainsProductWhichPriceIs0()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        $article = oxNew('oxArticle');
        $article->setId("_testArticle");
        $article->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $article->oxarticles__oxactive = new oxField(1);
        $article->oxarticles__oxvarminprice = new oxField(0);
        $article->save();

        $oUtilsCount = oxNew('oxUtilsCount');

        $this->assertEquals(5, $oUtilsCount->setPriceCatArticleCount(array(), 'xxx', 'xxx', 0, 1));
    }

    public function testGetCatArticleCount()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        $this->assertEquals('0', \OxidEsales\Eshop\Core\Registry::getUtilsCount()->GetCatArticleCount('', true));

        oxRegistry::getUtils()->oxResetFileCache();
        $this->assertEquals('0', \OxidEsales\Eshop\Core\Registry::getUtilsCount()->GetCatArticleCount('', true));

        $sCatID = '8a142c3e60a535f16.78077188';
        $sResult = oxDb::getDb()->getOne("SELECT count(*) FROM `oxobject2category` WHERE OXCATNID = '$sCatID'");
        $this->assertEquals($sResult, \OxidEsales\Eshop\Core\Registry::getUtilsCount()->GetCatArticleCount($sCatID, true));
    }

    public function testGetPriceCatArticleCount()
    {
        $myUtilsTest = oxNew('oxUtilsCount');

        $aCache = $myUtilsTest->getCatCache();
        $sRet = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->setPriceCatArticleCount($aCache, '30e44ab8338d7bf06.79655612', $myUtilsTest->getUserViewId(), 1, 100, true);
        $sCount = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->getPriceCatArticleCount('30e44ab8338d7bf06.79655612', 1, 100, true);
        $this->assertEquals($sRet, $sCount);
        //to make sure there is no null == null test
        $this->assertTrue($sRet > 0);
    }

    /**
     * testing if price category cache is loaded automatically
     */
    public function testGetPriceCatArticleCountCacheRefreshTest()
    {
        $sCatId = 'xxx';
        $oUtilsCount = $this->getMock(\OxidEsales\Eshop\Core\UtilsCount::class, array('getUserViewId', 'getCatCache', 'setPriceCatArticleCount'));

        $oUtilsCount->expects($this->once())->method('getUserViewId')->will($this->returnValue('aaa'));
        $oUtilsCount->expects($this->once())->method('getCatCache')->will($this->returnValue(array('bbb')));
        $oUtilsCount->expects($this->once())->method('setPriceCatArticleCount')->with($this->equalTo(array('bbb')), $this->equalTo($sCatId), $this->equalTo('aaa'), $this->equalTo(10), $this->equalTo(20));

        $oUtilsCount->getPriceCatArticleCount($sCatId, 10, 20);
    }

    public function testGetVendorArticleCount()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        $myUtilsTest = oxNew('oxUtilsCount');

        $aCache = $myUtilsTest->getVendorCache();

        $sRet = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->setVendorArticleCount($aCache, '77442e37fdf34ccd3.94620745', $myUtilsTest->getUserViewId(), true);
        $sCount = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->getVendorArticleCount('77442e37fdf34ccd3.94620745', true, true);

        $this->assertEquals($sRet, $sCount);
        $this->assertTrue($sRet > 0);
    }

    /**
     * testing if vendor cache is loaded automatically
     */
    public function testGetVendorArticleCountCacheRefreshTest()
    {
        $sVendorId = 'xxx';
        $oUtilsCount = $this->getMock(\OxidEsales\Eshop\Core\UtilsCount::class, array('getUserViewId', 'getVendorCache', 'setVendorArticleCount'));

        $oUtilsCount->expects($this->once())->method('getUserViewId')->will($this->returnValue('aaa'));
        $oUtilsCount->expects($this->once())->method('getVendorCache')->will($this->returnValue(array('bbb')));
        $oUtilsCount->expects($this->once())->method('setVendorArticleCount')->with($this->equalTo(array('bbb')), $this->equalTo($sVendorId), $this->equalTo('aaa'));

        $oUtilsCount->getVendorArticleCount($sVendorId);
    }

    public function testGetManufacturerArticleCount()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        $myUtilsTest = oxNew('oxUtilsCount');

        $aCache = $myUtilsTest->getManufacturerCache();

        $sRet = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->setManufacturerArticleCount($aCache, 'ee4948794e28d488cf1c8101e716a3f4', $myUtilsTest->getUserViewId(), true);
        $sCount = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->getManufacturerArticleCount('ee4948794e28d488cf1c8101e716a3f4', true, true);

        $this->assertEquals($sRet, $sCount);
        $this->assertTrue($sRet > 0);
    }

    /**
     * Testing if Manufacturer cache is loaded automatically
     */
    public function testGetManufacturerArticleCountCacheRefreshTest()
    {
        $sManufacturerId = 'xxx';
        $oUtilsCount = $this->getMock(\OxidEsales\Eshop\Core\UtilsCount::class, array('getUserViewId', 'getManufacturerCache', 'setManufacturerArticleCount'));

        $oUtilsCount->expects($this->once())->method('getUserViewId')->will($this->returnValue('aaa'));
        $oUtilsCount->expects($this->once())->method('getManufacturerCache')->will($this->returnValue(array('bbb')));
        $oUtilsCount->expects($this->once())->method('setManufacturerArticleCount')->with($this->equalTo(array('bbb')), $this->equalTo($sManufacturerId), $this->equalTo('aaa'));

        $oUtilsCount->getManufacturerArticleCount($sManufacturerId);
    }

    public function testSetCatArticleCount()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        $myUtilsTest = oxNew('oxUtilsCount');
        $sRetSet = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->setCatArticleCount(array(), '8a142c3e44ea4e714.31136811', $myUtilsTest->getUserViewId(), true);
        $sRetGet = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->getCatArticleCount('8a142c3e44ea4e714.31136811', true);

        $this->assertEquals($sRetSet, $sRetGet);
        $this->assertEquals($sRetSet, 4);
    }

    public function testSetPriceCatArticleCount()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        $myUtilsTest = oxNew('oxUtilsCount');

        $sRetSet = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->setPriceCatArticleCount(array(), '30e44ab8338d7bf06.79655612', $myUtilsTest->getUserViewId(), 10, 100);
        $sRetGet = \OxidEsales\Eshop\Core\Registry::getUtilsCount()->getPriceCatArticleCount('30e44ab8338d7bf06.79655612', 10, 100, true);
        $this->assertEquals($sRetSet, $sRetGet);
        $this->assertEquals(35, $sRetSet);
    }

    public function testSetVendorArticleCount()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        $myUtilsTest = oxNew('oxUtilsCount');
        $aCache = null;
        $sCatId = 'root';
        $sActIdent = null;

        // always return 0 if $sCatId ='root'
        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsCount()->setVendorArticleCount($aCache, $sCatId, $sActIdent, true), 0);
        oxRegistry::getUtils()->oxResetFileCache();

        $aCache = $myUtilsTest->getVendorCache();
        $sVendorID = '77442e37fdf34ccd3.94620745'; //Hersteller 2 from Demodata
        $sCatId = $sVendorID;
        $sActIdent = $myUtilsTest->getUserViewId();
        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsCount()->setVendorArticleCount($aCache, $sCatId, $sActIdent, true), 1);
    }

    /**
     * Checking if counting vendors articles does not counts variants, only variant
     * parents (1312).
     */
    public function testSetVendorArticleCount_VariantsCount()
    {
        $myUtilsTest = oxNew('oxUtilsCount');
        oxRegistry::getUtils()->oxResetFileCache();

        //adding articles
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArtId_1');
        $oArticle->oxarticles__oxstock = new oxField(1);
        $oArticle->oxarticles__oxvendorid = new oxField('_testVendorId');
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArtId_2');
        $oArticle->oxarticles__oxstock = new oxField(0);
        $oArticle->oxarticles__oxstockflag = new oxField(2);
        $oArticle->oxarticles__oxvarstock = new oxField(5);
        $oArticle->oxarticles__oxvarcount = new oxField(2);
        $oArticle->oxarticles__oxvendorid = new oxField('_testVendorId');
        $oArticle->save();

        //adding variants
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArtVarId_1');
        $oArticle->oxarticles__oxstock = new oxField(2);
        $oArticle->oxarticles__oxparentid = new oxField('_testArtId_2');
        $oArticle->oxarticles__oxvendorid = new oxField('_testVendorId');
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArtVarId_2');
        $oArticle->oxarticles__oxstock = new oxField(3);
        $oArticle->oxarticles__oxparentid = new oxField('_testArtId_2');
        $oArticle->oxarticles__oxvendorid = new oxField('_testVendorId');
        $oArticle->save();

        $sActIdent = $myUtilsTest->getUserViewId();
        $iCount = $myUtilsTest->setVendorArticleCount(array(), '_testVendorId', $sActIdent);

        $this->assertEquals(2, $iCount);
    }

    /**
     * Checking if counting manufacturers articles does not counts variants, only variant
     * parents (M:1312).
     */
    public function testSetManufacturersArticleCount_VariantsCount()
    {
        $myUtilsTest = oxNew('oxUtilsCount');
        oxRegistry::getUtils()->oxResetFileCache();
        $oDb = oxDb::getDb();
        $oDb->execute('replace INTO `oxmanufacturers` (`OXID`, `OXSHOPID`) VALUES ("_testManufacturerId", 1);');

        //adding articles
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArtId_1');
        $oArticle->oxarticles__oxstock = new oxField(1);
        $oArticle->oxarticles__oxmanufacturerid = new oxField('_testManufacturerId');
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArtId_2');
        $oArticle->oxarticles__oxstock = new oxField(0);
        $oArticle->oxarticles__oxstockflag = new oxField(2);
        $oArticle->oxarticles__oxvarstock = new oxField(5);
        $oArticle->oxarticles__oxvarcount = new oxField(2);
        $oArticle->oxarticles__oxmanufacturerid = new oxField('_testManufacturerId');
        $oArticle->save();

        //adding variants
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArtVarId_1');
        $oArticle->oxarticles__oxstock = new oxField(2);
        $oArticle->oxarticles__oxparentid = new oxField('_testArtId_2');
        $oArticle->oxarticles__oxmanufacturerid = new oxField('_testManufacturerId');
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArtVarId_2');
        $oArticle->oxarticles__oxstock = new oxField(3);
        $oArticle->oxarticles__oxparentid = new oxField('_testArtId_2');
        $oArticle->oxarticles__oxmanufacturerid = new oxField('_testManufacturerId');
        $oArticle->save();
        $sActIdent = $myUtilsTest->getUserViewId();
        $iCount = $myUtilsTest->setManufacturerArticleCount(array(), '_testManufacturerId', $sActIdent);

        $this->assertEquals(2, $iCount);
    }

    public function testSetManufacturerArticleCount()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        $myUtilsTest = oxNew('oxUtilsCount');
        $aCache = null;
        $sCatId = 'root';
        $sActIdent = null;

        // always return 0 if $sCatId ='root'
        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsCount()->setManufacturerArticleCount($aCache, $sCatId, $sActIdent, true), 0);
        oxRegistry::getUtils()->oxResetFileCache();

        $aCache = $myUtilsTest->getManufacturerCache();
        $sManufacturerID = 'ee4948794e28d488cf1c8101e716a3f4'; //Hersteller 2 from Demodata
        $sCatId = $sManufacturerID;
        $sActIdent = $myUtilsTest->getUserViewId();
        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsCount()->setManufacturerArticleCount($aCache, $sCatId, $sActIdent, true), 1);
    }

    /**
     * Testing category count reset code
     */
    public function testResetCatArticleCountResettingAllCategoryData()
    {
        $this->getConfig()->setGlobalParameter('aLocalCatCache', 'xxx');

        $oUtilsCount = $this->getMock(\OxidEsales\Eshop\Core\UtilsCount::class, array('getCatCache', 'setCatCache'));
        $oUtilsCount->expects($this->never())->method('getCatCache');
        $oUtilsCount->expects($this->never())->method('setCatCache');

        $oUtilsCount->resetCatArticleCount();

        $this->assertNull($this->getConfig()->getGlobalParameter('aLocalCatCache'));
        $this->assertNull(oxRegistry::getUtils()->fromFileCache('staticfilecache|aLocalCatCache'));
    }

    public function testResetCatArticleCountResettingSomeCategoryData()
    {
        $aCache = array('aaa' => '1', 'bbb' => '2', 'ccc' => '3');
        $aRes = array('bbb' => '2', 'ccc' => '3');
        $iCatId = 'aaa';

        $oUtilsCount = $this->getMock(\OxidEsales\Eshop\Core\UtilsCount::class, array('getCatCache', 'setCatCache'));
        $oUtilsCount->expects($this->once())->method('getCatCache')->will($this->returnValue($aCache));
        $oUtilsCount->expects($this->once())->method('setCatCache')->with($this->equalTo($aRes));

        $oUtilsCount->resetCatArticleCount($iCatId);
    }

    /**
     * Testig price categories reset source
     */
    public function testResetPriceCatArticleCountNoDataSetNoReset()
    {
        $oUtilsCount = $this->getMock(\OxidEsales\Eshop\Core\UtilsCount::class, array('getCatCache', 'setCatCache'));
        $oUtilsCount->expects($this->once())->method('getCatCache')->will($this->returnValue(false));
        $oUtilsCount->expects($this->never())->method('setCatCache');

        $oUtilsCount->resetPriceCatArticleCount(10);
    }

    public function testResetPriceCatArticleCount()
    {
        $aRes = array('xxx' => 'yyy');
        $aCache = array_flip(array_keys($this->categories));
        $aCache = array_merge($aCache, $aRes);

        $oUtilsCount = $this->getMock(\OxidEsales\Eshop\Core\UtilsCount::class, array('getCatCache', 'setCatCache'));
        $oUtilsCount->expects($this->once())->method('getCatCache')->will($this->returnValue($aCache));
        $oUtilsCount->expects($this->once())->method('setCatCache')->with($this->equalTo($aRes));

        $oUtilsCount->resetPriceCatArticleCount(10);
    }

    public function testResetVendorArticleCount()
    {
        $myConfig = $this->getConfig();
        $utilsTest = oxNew('oxUtilsCount');
        $sVendorID = null;

        // case $sVendorID = null;
        $this->assertNull(\OxidEsales\Eshop\Core\Registry::getUtilsCount()->resetVendorArticleCount($sVendorID)); //actual test
        $this->assertNull($myConfig->getGlobalParameter('aLocalVendorCache'));
        $this->assertEquals(oxRegistry::getUtils()->fromFileCache('aLocalVendorCache'), '');

        // case loading from cache
        $sVendorID = 'd2e44d9b31fcce448.08890330';
        $sInput = array("d2e44d9b31fcce448.08890330" => array("2fb5911b89dddda329c256f56d1f60c5" => "14"), "d2e44d9b32fd2c224.65443178" => array("2fb5911b89dddda329c256f56d1f60c5" => "14"));
        $sName = 'aLocalVendorCache';
        oxRegistry::getUtils()->toFileCache($sName, $sInput);
        $aCache = $utilsTest->getVendorCache();
        $this->assertNotNull($aCache);
        $this->assertNull(\OxidEsales\Eshop\Core\Registry::getUtilsCount()->resetVendorArticleCount($sVendorID)); //actual test
        $aCache = $utilsTest->getCatCache();
        $this->assertFalse(isset($aCache[$sVendorID]));
    }

    public function testResetManufacturerArticleCount()
    {
        $myConfig = $this->getConfig();
        $myUtilsTest = oxNew('oxUtilsCount');
        $sManufacturerID = null;

        //case $sManufacturerID = null;
        $this->assertNull(\OxidEsales\Eshop\Core\Registry::getUtilsCount()->resetManufacturerArticleCount($sManufacturerID)); //actual test
        $this->assertNull($myConfig->getGlobalParameter('aLocalManufacturerCache'));
        $this->assertEquals(oxRegistry::getUtils()->fromFileCache('aLocalManufacturerCache'), '');

        // case loading from cache
        $sManufacturerID = '88a996f859f94176da943f38ee067984';
        $sInput = array("88a996f859f94176da943f38ee067984" => array("2fb5911b89dddda329c256f56d1f60c5" => "14"), "2536d76675ebe5cb777411914a2fc8fb" => array("2fb5911b89dddda329c256f56d1f60c5" => "14"));
        $sName = 'aLocalManufacturerCache';
        oxRegistry::getUtils()->toFileCache($sName, $sInput);
        $aCache = $myUtilsTest->getManufacturerCache();
        $this->assertNotNull($aCache);
        $this->assertNull(\OxidEsales\Eshop\Core\Registry::getUtilsCount()->resetManufacturerArticleCount($sManufacturerID)); //actual test
        $aCache = $myUtilsTest->getCatCache();
        $this->assertFalse(isset($aCache[$sManufacturerID]));
    }

    public function testGetCatCache()
    {
        $myUtilsTest = oxNew('oxUtilsCount');

        //it is necessary also to reset global params!
        $myConfig = $this->getConfig();
        $myConfig->setGlobalParameter('aLocalCatCache', null);

        $this->assertNull($myUtilsTest->getCatCache()); //actual test
        // previous test (oxResetFileCache)erases all data, so we provide some data
        $sName = "aLocalCatCache";
        $aArray = array("2fb5911b89dddda329c256f56d1f60c5" => "5");
        $aRetCache = array("30e44ab83159266c7.83602558" => $aArray);
        oxRegistry::getUtils()->toFileCache($sName, $aRetCache);

        $aLocalCache = $myUtilsTest->getCatCache(); // actual test
        $this->assertTrue($aRetCache === $aLocalCache);
    }

    public function testSetCatCache()
    {
        $myConfig = $this->getConfig();
        $myUtilsTest = oxNew('oxUtilsCount');

        $aArray = array("2fb5911b89dddda329c256f56d1f60c5" => "5");
        $aCache = array("30e44ab83159266c7.83602558" => $aArray);

        $this->assertNull($myUtilsTest->setCatCache($aCache)); //actual test
        $this->assertEquals($myConfig->getGlobalParameter('aLocalCatCache'), $aCache);
        $sName = "aLocalCatCache";

        $this->assertEquals(oxRegistry::getUtils()->fromFileCache($sName), $aCache);
    }

    public function testSetVendorCache()
    {
        $myConfig = $this->getConfig();
        $myUtilsTest = oxNew('oxUtilsCount');

        $aArray = array("2fb5911b89dddda329c256f56d1f60c5" => "14");
        $aCache = array("d2e44d9b31fcce448.08890330" => $aArray);

        $this->assertNull($myUtilsTest->setVendorCache($aCache)); //actual test
        $this->assertEquals($myConfig->getGlobalParameter('aLocalVendorCache'), $aCache);
        $sName = "aLocalVendorCache";
        $this->assertEquals(oxRegistry::getUtils()->fromFileCache($sName), $aCache);
        //cleanup
        $this->assertNull($myUtilsTest->setVendorCache(null));
    }

    public function testSetManufacturerCache()
    {
        $myConfig = $this->getConfig();
        $myUtilsTest = oxNew('oxUtilsCount');

        $aArray = array("2fb5911b89dddda329c256f56d1f60c5" => "14");
        $aCache = array("88a996f859f94176da943f38ee067984" => $aArray);

        $this->assertNull($myUtilsTest->setManufacturerCache($aCache)); //actual test
        $this->assertEquals($myConfig->getGlobalParameter('aLocalManufacturerCache'), $aCache);
        $sName = "aLocalManufacturerCache";
        $sInput = $aCache;

        $this->assertEquals(oxRegistry::getUtils()->fromFileCache($sName), $sInput);
        //cleanup
        $this->assertNull($myUtilsTest->setManufacturerCache(null));
    }

    public function testGetVendorCache()
    {
        $myUtilsTest = oxNew('oxUtilsCount');

        $aArray = array("2fb5911b89dddda329c256f5614111978" => "14");
        $aCache = array("m4e44d9b31fcce448.08890815" => $aArray);

        $myUtilsTest->setVendorCache($aCache);

        $this->assertEquals($aCache, $myUtilsTest->getVendorCache());
        //clean up
        $myUtilsTest->setVendorCache(null);
    }

    public function testGetManufacturerCache()
    {
        $myUtilsTest = oxNew('oxUtilsCount');

        $aArray = array("2fb5911b89dddda329c256f5614111978" => "14");
        $aCache = array("m4e44d9b31fcce448.08890815" => $aArray);

        $myUtilsTest->setManufacturerCache($aCache);

        $this->assertEquals($aCache, $myUtilsTest->getManufacturerCache());
        //clean up
        $myUtilsTest->setManufacturerCache(null);
    }

    public function testGetUserViewId()
    {
        $myConfig = $this->getConfig();
        $myUtilsTest = oxNew('oxUtilsCount');

        $sExpected = md5($myConfig->GetShopID() . oxRegistry::getLang()->getLanguageTag() . serialize(null) . '0');
        $this->assertEquals($sExpected, $myUtilsTest->getUserViewId());
    }

    public function testZeroArtManufaturerCache()
    {
        $myUtilsTest = $this->getMock(\OxidEsales\Eshop\Core\UtilsCount::class, array('setManufacturerCache'));
        $myUtilsTest->expects($this->once())->method('setManufacturerCache')->with(
            $this->equalTo(
                array(
                     '_testManufacturerId' =>
                         array(
                             '2fb5911b89dddda329c256f56d1f60c5' => 0,
                         ),
                )
            )
        );

        oxRegistry::getUtils()->oxResetFileCache();
        $oDb = oxDb::getDb();
        $oDb->execute('replace INTO `oxmanufacturers` (`OXID`, `OXSHOPID`) VALUES ("_testManufacturerId", 1);');

        $sActIdent = $myUtilsTest->getUserViewId();
        $iCount = $myUtilsTest->setManufacturerArticleCount(array(), '_testManufacturerId', $sActIdent);

        $this->assertSame(0, $iCount);
    }
}
