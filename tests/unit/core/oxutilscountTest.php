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

class Unit_Core_oxUtilsCountTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $oPriceCat = new oxcategory();
        $oPriceCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oPriceCat->oxcategories__oxparentid = new oxField("oxrootid", oxField::T_RAW);
        $oPriceCat->oxcategories__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId(), oxField::T_RAW);
        $oPriceCat->oxcategories__oxtitle = new oxField("Price Cat 1", oxField::T_RAW);
        $oPriceCat->oxcategories__oxpricefrom = new oxField(1, oxField::T_RAW);
        $oPriceCat->oxcategories__oxpriceto = new oxField(100, oxField::T_RAW);
        $oPriceCat->save();

        $this->aCats[$oPriceCat->getId()] = $oPriceCat;

        $oPriceCat = new oxcategory();
        $oPriceCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oPriceCat->oxcategories__oxparentid = new oxField("oxrootid", oxField::T_RAW);
        $oPriceCat->oxcategories__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId(), oxField::T_RAW);
        $oPriceCat->oxcategories__oxtitle = new oxField("Price Cat 2", oxField::T_RAW);
        $oPriceCat->oxcategories__oxpricefrom = new oxField(1, oxField::T_RAW);
        $oPriceCat->oxcategories__oxpriceto = new oxField(100, oxField::T_RAW);
        $oPriceCat->save();

        $this->aCats[$oPriceCat->getId()] = $oPriceCat;

        oxRegistry::getConfig()->setGlobalParameter('aLocalVendorCache', null);
        oxRegistry::getUtils()->toFileCache('aLocalVendorCache', '');
        oxRegistry::getUtils()->toFileCache('aLocalCatCache', '');

    }

    protected function tearDown()
    {
        foreach ($this->aCats as $oCat) {
            $oCat->delete();
        }

        oxRegistry::getConfig()->setGlobalParameter('aLocalVendorCache', null);
        oxRegistry::getUtils()->toFileCache('aLocalVendorCache', '');
        oxRegistry::getUtils()->toFileCache('aLocalCatCache', '');

        oxRegistry::getUtils()->oxResetFileCache();


        // deleting test articles
        $oArticle = new oxarticle;
        $oArticle->delete('testarticle1');
        $oArticle->delete('testarticle2');
        $oArticle->delete('_testArticle');

        $this->cleanUpTable('oxarticles');

        parent::tearDown();
    }

    public function testSetPriceCatArticleCountWhenPriceFrom0To1AndDbContainsProductWhichPriceIs0()
    {
        $oArticle = new oxArticle();
        $oArticle->setId("_testArticle");
        $oArticle->oxarticles__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId());
        $oArticle->oxarticles__oxactive = new oxField(1);
        $oArticle->oxarticles__oxvarminprice = new oxField(0);
        $oArticle->save();

        $oUtilsCount = new oxUtilsCount();


        $this->assertEquals(5, $oUtilsCount->setPriceCatArticleCount(array(), 'xxx', 'xxx', 0, 1));
    }

    public function testGetCatArticleCount()
    {
        $this->assertEquals('0', oxRegistry::get("oxUtilsCount")->GetCatArticleCount('', true));
        $sCatID = '8a142c3e60a535f16.78077188';
        $sResult = oxDb::getDb()->getOne("SELECT count(*) FROM `oxobject2category` WHERE OXCATNID = '$sCatID'");
        $this->assertEquals($sResult, oxRegistry::get("oxUtilsCount")->GetCatArticleCount($sCatID, true));
        oxRegistry::getUtils()->oxResetFileCache();
        $this->assertEquals('0', oxRegistry::get("oxUtilsCount")->GetCatArticleCount('', true));
        $sCatID = '8a142c3e60a535f16.78077188';
        $sResult = oxDb::getDb()->getOne("SELECT count(*) FROM `oxobject2category` WHERE OXCATNID = '$sCatID'");
        $this->assertEquals($sResult, oxRegistry::get("oxUtilsCount")->GetCatArticleCount($sCatID, true));
    }

    public function testGetPriceCatArticleCount()
    {

        $myUtilsTest = new oxutilscount();

        $aCache = $myUtilsTest->UNITgetCatCache();
        $sRet = oxRegistry::get("oxUtilsCount")->setPriceCatArticleCount($aCache, '30e44ab8338d7bf06.79655612', $myUtilsTest->UNITgetUserViewId(), 1, 100, true);
        $sCount = oxRegistry::get("oxUtilsCount")->getPriceCatArticleCount('30e44ab8338d7bf06.79655612', 1, 100, true);
        $this->assertEquals($sRet, $sCount);
        //to make sure there is no null == null test
        $this->assertTrue($sRet > 0);
    }

    // testing if price category cache is loaded automatically
    public function testGetPriceCatArticleCountCacheRefreshTest()
    {
        $sCatId = 'xxx';
        $oUtilsCount = $this->getMock('oxutilscount', array('_getUserViewId', '_getCatCache', 'setPriceCatArticleCount'));

        $oUtilsCount->expects($this->once())->method('_getUserViewId')->will($this->returnValue('aaa'));
        $oUtilsCount->expects($this->once())->method('_getCatCache')->will($this->returnValue(array('bbb')));
        $oUtilsCount->expects($this->once())->method('setPriceCatArticleCount')->with($this->equalTo(array('bbb')), $this->equalTo($sCatId), $this->equalTo('aaa'), $this->equalTo(10), $this->equalTo(20));

        $oUtilsCount->getPriceCatArticleCount($sCatId, 10, 20);
    }

    public function testGetVendorArticleCount()
    {
        $myUtilsTest = new oxutilscount();

        $aCache = $myUtilsTest->UNITgetVendorCache();


        $sRet = oxRegistry::get("oxUtilsCount")->setVendorArticleCount($aCache, '77442e37fdf34ccd3.94620745', $myUtilsTest->UNITgetUserViewId(), true);
        $sCount = oxRegistry::get("oxUtilsCount")->getVendorArticleCount('77442e37fdf34ccd3.94620745', true, true);

        $this->assertEquals($sRet, $sCount);
        //to make sure there is no null == null test
        $this->assertTrue($sRet > 0);
    }


    // testing if vendor cache is loaded automatically
    public function testGetVendorArticleCountCacheRefreshTest()
    {
        $sVendorId = 'xxx';
        $oUtilsCount = $this->getMock('oxutilscount', array('_getUserViewId', '_getVendorCache', 'setVendorArticleCount'));

        $oUtilsCount->expects($this->once())->method('_getUserViewId')->will($this->returnValue('aaa'));
        $oUtilsCount->expects($this->once())->method('_getVendorCache')->will($this->returnValue(array('bbb')));
        $oUtilsCount->expects($this->once())->method('setVendorArticleCount')->with($this->equalTo(array('bbb')), $this->equalTo($sVendorId), $this->equalTo('aaa'));

        $oUtilsCount->getVendorArticleCount($sVendorId);
    }

    public function testGetManufacturerArticleCount()
    {
        $myUtilsTest = new oxutilscount();

        $aCache = $myUtilsTest->UNITgetManufacturerCache();


        $sRet = oxRegistry::get("oxUtilsCount")->setManufacturerArticleCount($aCache, 'ee4948794e28d488cf1c8101e716a3f4', $myUtilsTest->UNITgetUserViewId(), true);
        $sCount = oxRegistry::get("oxUtilsCount")->getManufacturerArticleCount('ee4948794e28d488cf1c8101e716a3f4', true, true);

        $this->assertEquals($sRet, $sCount);
        //to make sure there is no null == null test
        $this->assertTrue($sRet > 0);
    }

    // testing if Manufacturer cache is loaded automatically
    public function testGetManufacturerArticleCountCacheRefreshTest()
    {
        $sManufacturerId = 'xxx';
        $oUtilsCount = $this->getMock('oxutilscount', array('_getUserViewId', '_getManufacturerCache', 'setManufacturerArticleCount'));

        $oUtilsCount->expects($this->once())->method('_getUserViewId')->will($this->returnValue('aaa'));
        $oUtilsCount->expects($this->once())->method('_getManufacturerCache')->will($this->returnValue(array('bbb')));
        $oUtilsCount->expects($this->once())->method('setManufacturerArticleCount')->with($this->equalTo(array('bbb')), $this->equalTo($sManufacturerId), $this->equalTo('aaa'));

        $oUtilsCount->getManufacturerArticleCount($sManufacturerId);
    }

    public function testSetCatArticleCount()
    {
        $myUtilsTest = new oxutilscount();


        $sRetSet = oxRegistry::get("oxUtilsCount")->setCatArticleCount(array(), '8a142c3e44ea4e714.31136811', $myUtilsTest->UNITgetUserViewId(), true);
        $sRetGet = oxRegistry::get("oxUtilsCount")->getCatArticleCount('8a142c3e44ea4e714.31136811', true);

        $this->assertEquals($sRetSet, $sRetGet);


        $this->assertEquals($sRetSet, 4);
    }

    public function testSetPriceCatArticleCount()
    {
        $myUtilsTest = new oxutilscount();

        $sRetSet = oxRegistry::get("oxUtilsCount")->setPriceCatArticleCount(array(), '30e44ab8338d7bf06.79655612', $myUtilsTest->UNITgetUserViewId(), 10, 100);
        $sRetGet = oxRegistry::get("oxUtilsCount")->getPriceCatArticleCount('30e44ab8338d7bf06.79655612', 10, 100, true);
        $this->assertEquals($sRetSet, $sRetGet);


        $this->assertEquals(35, $sRetSet);
    }

    public function testSetVendorArticleCount()
    {
        $myUtilsTest = new oxutilscount();
        $aCache = null;
        $sCatId = 'root';
        $sActIdent = null;

        // always return 0 if $sCatId ='root'
        $this->assertEquals(oxRegistry::get("oxUtilsCount")->setVendorArticleCount($aCache, $sCatId, $sActIdent, true), 0);
        oxRegistry::getUtils()->oxResetFileCache();


        $aCache = $myUtilsTest->UNITgetVendorCache();
        $sVendorID = '77442e37fdf34ccd3.94620745'; //Hersteller 2 from Demodata
        $sCatId = $sVendorID;
        $sActIdent = $myUtilsTest->UNITgetUserViewId();
        //echo "\n->".setVendorArticleCount($aCache, $sCatId, $sActIdent)."<-";
        $this->assertEquals(oxRegistry::get("oxUtilsCount")->setVendorArticleCount($aCache, $sCatId, $sActIdent, true), 1);
    }

    /*
     * Checking if counting vendors articles does not counts variants, only variant
     * parents (1312).
     */
    public function testSetVendorArticleCount_VariantsCount()
    {
        $myUtilsTest = new oxutilscount();
        oxRegistry::getUtils()->oxResetFileCache();
        $oDb = oxDb::getDb();
        $sShopId = oxRegistry::getConfig()->getShopId();

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

        $sActIdent = $myUtilsTest->UNITgetUserViewId();
        $iCount = $myUtilsTest->setVendorArticleCount(array(), '_testVendorId', $sActIdent);

        $this->assertEquals(2, $iCount);
    }

    /*
     * Checking if counting manufacturers articles does not counts variants, only variant
     * parents (M:1312).
     */
    public function testSetManufacturersArticleCount_VariantsCount()
    {
        $myUtilsTest = new oxutilscount();
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
        $sActIdent = $myUtilsTest->UNITgetUserViewId();
        $iCount = $myUtilsTest->setManufacturerArticleCount(array(), '_testManufacturerId', $sActIdent);

        $this->assertEquals(2, $iCount);
    }


    public function testSetManufacturerArticleCount()
    {
        $myUtilsTest = new oxutilscount();
        $aCache = null;
        $sCatId = 'root';
        $sActIdent = null;

        // always return 0 if $sCatId ='root'
        $this->assertEquals(oxRegistry::get("oxUtilsCount")->setManufacturerArticleCount($aCache, $sCatId, $sActIdent, true), 0);
        oxRegistry::getUtils()->oxResetFileCache();


        $aCache = $myUtilsTest->UNITgetManufacturerCache();
        $sManufacturerID = 'ee4948794e28d488cf1c8101e716a3f4'; //Hersteller 2 from Demodata
        $sCatId = $sManufacturerID;
        $sActIdent = $myUtilsTest->UNITgetUserViewId();
        //echo "\n->".setManufacturerArticleCount($aCache, $sCatId, $sActIdent)."<-";
        $this->assertEquals(oxRegistry::get("oxUtilsCount")->setManufacturerArticleCount($aCache, $sCatId, $sActIdent, true), 1);
    }

    /**
     * Testing category count reset code
     */
    public function testResetCatArticleCountResettingAllCategoryData()
    {
        oxRegistry::getConfig()->setGlobalParameter('aLocalCatCache', 'xxx');

        $oUtilsCount = $this->getMock('oxutilscount', array('_getCatCache', '_setCatCache'));
        $oUtilsCount->expects($this->never())->method('_getCatCache');
        $oUtilsCount->expects($this->never())->method('_setCatCache');

        $oUtilsCount->resetCatArticleCount();

        $this->assertNull(oxRegistry::getConfig()->getGlobalParameter('aLocalCatCache'));
        $this->assertNull(oxRegistry::getUtils()->fromFileCache('staticfilecache|aLocalCatCache'));
    }

    //
    public function testResetCatArticleCountResettingSomeCategoryData()
    {
        $aCache = array('aaa' => '1', 'bbb' => '2', 'ccc' => '3');
        $aRes = array('bbb' => '2', 'ccc' => '3');
        $iCatId = 'aaa';

        $oUtilsCount = $this->getMock('oxutilscount', array('_getCatCache', '_setCatCache'));
        $oUtilsCount->expects($this->once())->method('_getCatCache')->will($this->returnValue($aCache));
        $oUtilsCount->expects($this->once())->method('_setCatCache')->with($this->equalTo($aRes));

        $oUtilsCount->resetCatArticleCount($iCatId);
    }

    /**
     * Testig price categories reset source
     */
    public function testResetPriceCatArticleCountNoDataSetNoReset()
    {
        $oUtilsCount = $this->getMock('oxutilscount', array('_getCatCache', '_setCatCache'));
        $oUtilsCount->expects($this->once())->method('_getCatCache')->will($this->returnValue(false));
        $oUtilsCount->expects($this->never())->method('_setCatCache');

        $oUtilsCount->resetPriceCatArticleCount(10);
    }

    //
    public function testResetPriceCatArticleCount()
    {

        $aRes = array('xxx' => 'yyy');
        $aCache = array_flip(array_keys($this->aCats));
        $aCache = array_merge($aCache, $aRes);

        $oUtilsCount = $this->getMock('oxutilscount', array('_getCatCache', '_setCatCache'));
        $oUtilsCount->expects($this->once())->method('_getCatCache')->will($this->returnValue($aCache));
        $oUtilsCount->expects($this->once())->method('_setCatCache')->with($this->equalTo($aRes));

        $oUtilsCount->resetPriceCatArticleCount(10);
    }

    public function testResetVendorArticleCount()
    {
        $myConfig = oxRegistry::getConfig();
        $myUtilsTest = new oxutilscount();
        $sVendorID = null;

        //case $sVendorID = null;
        $this->assertNull(oxRegistry::get("oxUtilsCount")->resetVendorArticleCount($sVendorID)); //actual test
        $this->assertNull($myConfig->getGlobalParameter('aLocalVendorCache'));
        $this->assertEquals(oxRegistry::getUtils()->fromFileCache('aLocalVendorCache'), '');

        // case loading from cache
        $sVendorID = 'd2e44d9b31fcce448.08890330';
        $sInput = array("d2e44d9b31fcce448.08890330" => array("2fb5911b89dddda329c256f56d1f60c5" => "14"), "d2e44d9b32fd2c224.65443178" => array("2fb5911b89dddda329c256f56d1f60c5" => "14"));
        $sName = 'aLocalVendorCache';
        oxRegistry::getUtils()->toFileCache($sName, $sInput);
        $aCache = $myUtilsTest->UNITgetVendorCache();
        $this->assertNotNull($aCache);
        $this->assertNull(oxRegistry::get("oxUtilsCount")->resetVendorArticleCount($sVendorID)); //actual test
        $aCache = $myUtilsTest->UNITgetCatCache();
        $this->assertFalse(isset($aCache[$sVendorID]));
    }

    public function testResetManufacturerArticleCount()
    {
        $myConfig = oxRegistry::getConfig();
        $myUtilsTest = new oxutilscount();
        $sManufacturerID = null;

        //case $sManufacturerID = null;
        $this->assertNull(oxRegistry::get("oxUtilsCount")->resetManufacturerArticleCount($sManufacturerID)); //actual test
        $this->assertNull($myConfig->getGlobalParameter('aLocalManufacturerCache'));
        $this->assertEquals(oxRegistry::getUtils()->fromFileCache('aLocalManufacturerCache'), '');

        // case loading from cache
        $sManufacturerID = '88a996f859f94176da943f38ee067984';
        $sInput = array("88a996f859f94176da943f38ee067984" => array("2fb5911b89dddda329c256f56d1f60c5" => "14"), "2536d76675ebe5cb777411914a2fc8fb" => array("2fb5911b89dddda329c256f56d1f60c5" => "14"));
        $sName = 'aLocalManufacturerCache';
        oxRegistry::getUtils()->toFileCache($sName, $sInput);
        $aCache = $myUtilsTest->UNITgetManufacturerCache();
        $this->assertNotNull($aCache);
        $this->assertNull(oxRegistry::get("oxUtilsCount")->resetManufacturerArticleCount($sManufacturerID)); //actual test
        $aCache = $myUtilsTest->UNITgetCatCache();
        $this->assertFalse(isset($aCache[$sManufacturerID]));
    }

    public function testGetCatCache()
    {
        $myUtilsTest = new oxutilscount();

        //it is neccessary also to reset global params!
        $myConfig = oxRegistry::getConfig();
        $aLocalCatCache = $myConfig->setGlobalParameter('aLocalCatCache', null);

        $this->assertNull($myUtilsTest->UNITgetCatCache()); //actual test
        // previous test (oxResetFileCache)erases all data, so we provide some data
        $sName = "aLocalCatCache";
        $aArray = array("2fb5911b89dddda329c256f56d1f60c5" => "5");
        $aRetCache = array("30e44ab83159266c7.83602558" => $aArray);
        oxRegistry::getUtils()->toFileCache($sName, $aRetCache);

        $aLocalCache = $myUtilsTest->UNITgetCatCache(); // actual test
        $this->assertTrue($aRetCache === $aLocalCache);
    }

    public function testSetCatCache()
    {
        $myConfig = oxRegistry::getConfig();
        $myUtilsTest = new oxutilscount();

        $aArray = array("2fb5911b89dddda329c256f56d1f60c5" => "5");
        $aCache = array("30e44ab83159266c7.83602558" => $aArray);

        $this->assertNull($myUtilsTest->UNITsetCatCache($aCache)); //actual test
        $this->assertEquals($myConfig->getGlobalParameter('aLocalCatCache'), $aCache);
        $sName = "aLocalCatCache";

        $this->assertEquals(oxRegistry::getUtils()->fromFileCache($sName), $aCache);
    }

    public function testSetVendorCache()
    {
        $myConfig = oxRegistry::getConfig();
        $myUtilsTest = new oxutilscount();

        $aArray = array("2fb5911b89dddda329c256f56d1f60c5" => "14");
        $aCache = array("d2e44d9b31fcce448.08890330" => $aArray);

        $this->assertNull($myUtilsTest->UNITsetVendorCache($aCache)); //actual test
        $this->assertEquals($myConfig->getGlobalParameter('aLocalVendorCache'), $aCache);
        $sName = "aLocalVendorCache";
        //echo "\n->".oxFileCache(false, $sName, $sInput)."<-";
        //echo "\n->".$sInput."<-";
        $this->assertEquals(oxRegistry::getUtils()->fromFileCache($sName, $sInput), $aCache);
        //cleanup
        $this->assertNull($myUtilsTest->UNITsetVendorCache(null));
    }

    public function testSetManufacturerCache()
    {
        $myConfig = oxRegistry::getConfig();
        $myUtilsTest = new oxutilscount();

        $aArray = array("2fb5911b89dddda329c256f56d1f60c5" => "14");
        $aCache = array("88a996f859f94176da943f38ee067984" => $aArray);

        $this->assertNull($myUtilsTest->UNITsetManufacturerCache($aCache)); //actual test
        $this->assertEquals($myConfig->getGlobalParameter('aLocalManufacturerCache'), $aCache);
        $sName = "aLocalManufacturerCache";
        $sInput = $aCache;

        //echo "\n->".oxFileCache(false, $sName, $sInput)."<-";
        //echo "\n->".$sInput."<-";
        $this->assertEquals(oxRegistry::getUtils()->fromFileCache($sName), $sInput);
        //cleanup
        $this->assertNull($myUtilsTest->UNITsetManufacturerCache(null));
    }

    public function testGetVendorCache()
    {

        $myConfig = oxRegistry::getConfig();
        $myUtilsTest = new oxutilscount();

        $aArray = array("2fb5911b89dddda329c256f5614111978" => "14");
        $aCache = array("m4e44d9b31fcce448.08890815" => $aArray);

        $myUtilsTest->UNITsetVendorCache($aCache);

        $this->assertEquals($aCache, $myUtilsTest->UNITgetVendorCache());
        //clean up
        $myUtilsTest->UNITsetVendorCache(null);
    }

    public function testGetManufacturerCache()
    {

        $myConfig = oxRegistry::getConfig();
        $myUtilsTest = new oxutilscount();

        $aArray = array("2fb5911b89dddda329c256f5614111978" => "14");
        $aCache = array("m4e44d9b31fcce448.08890815" => $aArray);

        $myUtilsTest->UNITsetManufacturerCache($aCache);

        $this->assertEquals($aCache, $myUtilsTest->UNITgetManufacturerCache());
        //clean up
        $myUtilsTest->UNITsetManufacturerCache(null);
    }

    public function testGetUserViewId()
    {

        $myConfig = oxRegistry::getConfig();
        $myUtilsTest = new oxutilscount();

        $sExpected = md5($myConfig->GetShopID() . oxRegistry::getLang()->getLanguageTag() . serialize(null) . '0');
        $this->assertEquals($sExpected, $myUtilsTest->UNITgetUserViewId());
    }


    public function testZeroArtManufaturerCache()
    {
        $myUtilsTest = $this->getMock('oxutilscount', array('_setManufacturerCache'));
        $myUtilsTest->expects($this->once())->method('_setManufacturerCache')->with(
            $this->equalTo(
                array(
                     '_testManufacturerId' =>
                         array(
                             '973fb5f4ea0bcdf38b56557db40cb509' => 0,
                         ),
                )
            )
        );

        oxRegistry::getUtils()->oxResetFileCache();
        $oDb = oxDb::getDb();
        $oDb->execute('replace INTO `oxmanufacturers` (`OXID`, `OXSHOPID`) VALUES ("_testManufacturerId", 1);');

        $sActIdent = $myUtilsTest->UNITgetUserViewId();
        $iCount = $myUtilsTest->setManufacturerArticleCount(array(), '_testManufacturerId', $sActIdent);

        $this->assertSame(0, $iCount);
    }
}
