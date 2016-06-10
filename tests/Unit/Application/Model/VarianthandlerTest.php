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

use OxidEsales\Eshop\Core\ShopIdCalculator;
use \oxVariantHandler;
use \stdClass;
use \oxField;
use \oxDb;

class oxVariantHandlerForOxvarianthandlerTest extends oxVariantHandler
{

    public function fillVariantSelections($oVariantList, $iVarSelCnt, &$aFilter, $sActVariantId)
    {
        return parent::_fillVariantSelections($oVariantList, $iVarSelCnt, $aFilter, $sActVariantId);
    }
}

class VarianthandlerTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxselectlist');
        $this->cleanUpTable('oxattribute', 'oxtitle');
        $this->cleanUpTable('oxobject2attribute', 'oxobjectid');
        $this->cleanUpTable('oxarticles');

        $myDB = oxDb::getDB();
        $sQ = 'delete from oxarticles where oxparentid = "2000" ';
        $myDB->Execute($sQ);
        parent::tearDown();
    }

    /**
     * oxVariantHandler::init() test case
     *
     * @return null
     */
    public function testInit()
    {
        $oHandler = $this->getProxyClass("oxVariantHandler");
        $this->assertNull($oHandler->getNonPublicVar("_oArticles"));
        $oHandler->init("testData");
        $this->assertEquals("testData", $oHandler->getNonPublicVar("_oArticles"));
    }

    public function testGetValuePrice()
    {
        $this->getConfig()->setConfigParam('bl_perfUseSelectlistPrice', 1);
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', 1);
        $oValue = new stdClass();
        $oValue->price = '10';
        $oValue->fprice = '10,00';
        $oValue->priceUnit = 'abs';
        $oValue->name = 'red';
        $oValue->value = '';

        $oVariantHandler = oxNew("oxVariantHandler");
        $this->assertEquals(10, $oVariantHandler->UNITgetValuePrice($oValue, 10));
        $oValue->priceUnit = '%';
        $this->assertEquals(1, $oVariantHandler->UNITgetValuePrice($oValue, 10));

        $oValue = new stdClass();
        $oValue->price = -10;
        $oValue->fprice = '10,00';
        $oValue->priceUnit = '%';
        $oValue->name = 'red';
        $oValue->value = '';
        $this->assertEquals(-1, $oVariantHandler->UNITgetValuePrice($oValue, 10));
    }

    public function testAssignValues()
    {
        $myDB = oxDb::getDB();
        $oValue = new stdClass();
        $oValue->price = '10';
        $oValue->fprice = '10,00';
        $oValue->priceUnit = 'abs';
        $oValue->name = 'red';
        $oValue->value = '';
        $aValues[0] = $oValue;
        $oValue2 = new stdClass();
        $oValue2->price = '10';
        $oValue2->fprice = '10,00';
        $oValue2->priceUnit = 'abs';
        $oValue2->name = 'rot';
        $oValue2->value = '';
        $aValues[1] = $oValue2;
        $aValues = array($aValues);

        $oArticle = oxNew("oxArticle");
        $oArticle->load('2000');

        $oVariantHandler = oxNew("oxVariantHandler");
        $aVar = $oVariantHandler->UNITassignValues($aValues, oxNew('oxArticleList'), $oArticle, array('en', 'de'));
        $oRez = $myDB->select("select oxvarselect, oxvarselect_1 from oxarticles where oxparentid = '2000'");
        while (!$oRez->EOF) {
            $oRez->fields = array_change_key_case($oRez->fields, CASE_LOWER);
            $this->assertEquals('red', $oRez->fields[0]);
            $this->assertEquals('rot', $oRez->fields[1]);
            $oRez->moveNext();
        }
    }

    public function testGenVariantFromSell()
    {
        $this->getConfig()->setConfigParam('blUseMultidimensionVariants', 1);
        $myDB = oxDb::getDB();
        $sVal = 'red!P!10__@@blue!P!10__@@black!P!10__@@';

        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $sSql = "insert into oxselectlist (oxid, oxshopid, oxtitle, oxident, oxvaldesc) values ('_testSell', '$sShopId', 'oxsellisttest', 'oxsellisttest', '$sVal')";
        $this->addToDatabase($sSql, 'oxselectlist');

        $oArticle = oxNew("oxArticle");
        $oArticle->load('2000');
        $oVariantHandler = oxNew("oxVariantHandler");
        $oVariantHandler->genVariantFromSell(array('_testSell'), $oArticle);
        $this->assertEquals(3, $myDB->getOne("select count(*) from oxarticles where oxparentid = '2000'"));
        //twice
        $oVariantHandler->genVariantFromSell(array('_testSell'), $oArticle);
        $this->assertEquals(9, $myDB->getOne("select count(*) from oxarticles where oxparentid = '2000'"));
        $this->assertTrue((bool) strpos($myDB->getOne("select oxvarselect from oxarticles where oxparentid = '2000' limit 1"), "|"));
        $this->assertEquals(18, $myDB->getOne("select count(*) from oxobject2attribute where oxobjectid in ( select art.oxid from oxarticles as art where art.oxparentid = '2000')"));
    }

    /**
     * test for bug#1447
     *
     */
    public function testGenVariantFromSellOxVarCountUpdated()
    {
        $this->getConfig()->setConfigParam('blUseMultidimensionVariants', 1);
        $sVal = 'red!P!10__@@blue!P!10__@@black!P!10__@@';

        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $sSql = "insert into oxselectlist (oxid, oxshopid, oxtitle, oxident, oxvaldesc) values ('_testSell', '$sShopId', 'oxsellisttest', 'oxsellisttest', '$sVal')";
        $this->addToDatabase($sSql, 'oxselectlist');

        $oArticle = oxNew("oxArticle");
        $oArticle->load('2000');
        $oVariantHandler = oxNew("oxVariantHandler");
        $oVariantHandler->genVariantFromSell(array('_testSell'), $oArticle);

        $oArticle2 = oxNew("oxArticle");
        $oArticle2->load('2000');
        $this->assertEquals(3, $oArticle2->oxarticles__oxvarcount->value);

        /**
         * $this->assertEquals( 3, $myDB->getOne( "select count(*) from oxarticles where oxparentid = '2000'" ));
         * //twice
         * $oVariantHandler->genVariantFromSell(array('_testSell'), $oArticle );
         * $this->assertEquals( 9, $myDB->getOne( "select count(*) from oxarticles where oxparentid = '2000'" ));
         * $this->assertTrue( (bool) strpos($myDB->getOne( "select oxvarselect from oxarticles where oxparentid = '2000' limit 1" ), "|"));
         * $this->assertEquals( 18, $myDB->getOne( "select count(*) from oxobject2attribute where oxobjectid in ( select art.oxid from oxarticles as art where art.oxparentid = '2000')" ));
         */
    }

    public function testCreateNewVariant()
    {
        $aParams = array('oxarticles__oxvarselect'      => "_testVar",
                         'oxarticles__oxartnum'         => "123",
                         'oxarticles__oxprice'          => "10",
                         'oxarticles__oxvarselect_1'    => "_testVar_1",
                         'oxarticles__oxid'             => "_testVar",
                         'oxarticles__oxisconfigurable' => "1",
        );
        $oVariantHandler = oxNew("oxVariantHandler");
        $sVariantId = $oVariantHandler->UNITcreateNewVariant($aParams, "_testArt");
        $oVariant = oxNew("oxArticle");
        $oVariant->load($sVariantId);
        $this->assertEquals("_testVar", $sVariantId);
        $this->assertEquals("_testVar", $oVariant->oxarticles__oxvarselect->value);
        $this->assertEquals("_testArt", $oVariant->oxarticles__oxparentid->value);
        $this->assertEquals("123", $oVariant->oxarticles__oxartnum->value);
        $this->assertEquals("10", $oVariant->oxarticles__oxprice->value);
        $this->assertEquals("1", $oVariant->oxarticles__oxisconfigurable->value);

        $oVariant = oxNew("oxArticle");
        $oVariant->loadInLang(1, $sVariantId);
        $this->assertEquals("_testVar_1", $oVariant->oxarticles__oxvarselect->value);
    }

    /**
     * oxVariantHandler::isMdVariant() test case
     *
     * @return null
     */
    public function testIsMdVariant()
    {
        $this->getConfig()->setConfigParam("blUseMultidimensionVariants", true);

        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxvarselect = new oxField(" value | value ");

        $oVariantHandler = oxNew("oxVariantHandler");
        $this->assertTrue($oVariantHandler->isMdVariant($oArticle));
    }

    /**
     * oxVariantHandler::buildMdVariants() test case
     *
     * @return null
     */
    public function testBuildMdVariants()
    {
        $oPrice = $this->getMock("oxPrice", array("getBruttoPrice"));
        $oPrice->expects($this->exactly(2))->method('getBruttoPrice')->will($this->returnValue(999));

        $oVar1 = $this->getMock("oxArticle", array("getPrice", "getLink"));
        $oVar1->expects($this->once())->method('getPrice')->will($this->returnValue($oPrice));
        $oVar1->expects($this->once())->method('getLink')->will($this->returnValue("testLink"));
        $oVar1->oxarticles__oxvarselect = new oxField("var1value1 | var1value2 | var1value3");

        $oVar2 = $this->getMock("oxArticle", array("getPrice", "getLink"));
        $oVar2->expects($this->once())->method('getPrice')->will($this->returnValue($oPrice));
        $oVar2->expects($this->once())->method('getLink')->will($this->returnValue("testLink"));
        $oVar2->oxarticles__oxvarselect = new oxField("var2value1 | var2value2 | var2value3");

        $oVariants = oxNew('oxList');
        $oVariants->offsetSet("var1", $oVar1);
        $oVariants->offsetSet("var2", $oVar2);

        $oVariantHandler = oxNew("oxVariantHandler");
        $oVariantHandler->buildMdVariants($oVariants, "testParentId");
    }

    /**
     * oxVariantHandler::_fillVariantSelections() test case
     *
     * @return null
     */
    public function testFillVariantSelections()
    {
        $aFilter = array();

        // empty variant list
        $oHandler = new oxVariantHandlerForOxvarianthandlerTest();
        $this->assertEquals(array(), $oHandler->fillVariantSelections(array(), 100, $aFilter, ""));

        // filled variant list
        $oVariant1 = oxNew('oxbase');
        $oVariant1->setId("test1");
        $oVariant1->oxarticles__oxvarselect = new oxField("a | b | c");

        $oVariant2 = oxNew('oxbase');
        $oVariant2->setId("test2");
        $oVariant2->oxarticles__oxvarselect = new oxField("a | b");

        $oVariant3 = oxNew('oxbase');
        $oVariant3->setId("test3");
        $oVariant3->oxarticles__oxvarselect = new oxField("a");

        $aArray[$oVariant1->getId()][] = array('name' => 'a', 'disabled' => null, 'active' => false, 'hash' => md5('a'));
        $aArray[$oVariant1->getId()][] = array('name' => 'b', 'disabled' => null, 'active' => false, 'hash' => md5('b'));

        $aArray[$oVariant2->getId()][] = array('name' => 'a', 'disabled' => null, 'active' => false, 'hash' => md5('a'));
        $aArray[$oVariant2->getId()][] = array('name' => 'b', 'disabled' => null, 'active' => false, 'hash' => md5('b'));

        $aArray[$oVariant3->getId()][] = array('name' => 'a', 'disabled' => null, 'active' => false, 'hash' => md5('a'));

        // checking
        $oHandler = new oxVariantHandlerForOxvarianthandlerTest();
        $this->assertEquals($aArray, $oHandler->fillVariantSelections(array($oVariant1, $oVariant2, $oVariant3), 2, $aFilter, "test1"));
        $this->assertEquals(array("0cc175b9c0f1b6a831c399e269772661", "92eb5ffee6ae2fec3ad71c777531578f"), $aFilter);
    }


    /**
     * oxVariantHandler::_applyVariantSelectionsFilter() test case
     *
     * @return null
     */
    public function testApplyVariantSelectionsFilter()
    {
        // test data
        $aArray["test1"][] = array('name' => 'a1', 'disabled' => null, 'active' => false, 'hash' => md5('a1'));
        $aArray["test1"][] = array('name' => 'b1', 'disabled' => null, 'active' => false, 'hash' => md5('b1'));
        $aArray["test1"][] = array('name' => 'c1', 'disabled' => null, 'active' => false, 'hash' => md5('c1'));

        $aArray["test2"][] = array('name' => 'a1', 'disabled' => null, 'active' => false, 'hash' => md5('a1'));
        $aArray["test2"][] = array('name' => 'b2', 'disabled' => null, 'active' => false, 'hash' => md5('b2'));
        $aArray["test2"][] = array('name' => 'c2', 'disabled' => null, 'active' => false, 'hash' => md5('c2'));

        $aArray["test3"][] = array('name' => 'a2', 'disabled' => null, 'active' => false, 'hash' => md5('a2'));
        $aArray["test3"][] = array('name' => 'b2', 'disabled' => null, 'active' => false, 'hash' => md5('b2'));
        $aArray["test3"][] = array('name' => 'c3', 'disabled' => null, 'active' => false, 'hash' => md5('c3'));

        $aArray["test4"][] = array('name' => 'a1', 'disabled' => null, 'active' => false, 'hash' => md5('a1'));
        $aArray["test4"][] = array('name' => '', 'disabled' => null, 'active' => false, 'hash' => md5(''));
        $aArray["test4"][] = array('name' => '', 'disabled' => null, 'active' => false, 'hash' => md5(''));

        $oHandler = oxNew('oxVariantHandler');

        // no filter
        $aFilter = array();
        $this->assertEquals(array($aArray, null, false), $oHandler->UNITapplyVariantSelectionsFilter($aArray, $aFilter));

        // filter 1
        // expected result
        $aResult = array();
        $aResult["test1"][] = array('name' => 'a1', 'disabled' => null, 'active' => true, 'hash' => md5('a1'));
        $aResult["test1"][] = array('name' => 'b1', 'disabled' => false, 'active' => false, 'hash' => md5('b1'));
        $aResult["test1"][] = array('name' => 'c1', 'disabled' => false, 'active' => false, 'hash' => md5('c1'));

        $aResult["test2"][] = array('name' => 'a1', 'disabled' => null, 'active' => true, 'hash' => md5('a1'));
        $aResult["test2"][] = array('name' => 'b2', 'disabled' => false, 'active' => false, 'hash' => md5('b2'));
        $aResult["test2"][] = array('name' => 'c2', 'disabled' => false, 'active' => false, 'hash' => md5('c2'));

        $aResult["test3"][] = array('name' => 'a2', 'disabled' => null, 'active' => false, 'hash' => md5('a2'));
        $aResult["test3"][] = array('name' => 'b2', 'disabled' => true, 'active' => false, 'hash' => md5('b2'));
        $aResult["test3"][] = array('name' => 'c3', 'disabled' => true, 'active' => false, 'hash' => md5('c3'));

        $aResult["test4"][] = array('name' => 'a1', 'disabled' => null, 'active' => true, 'hash' => md5('a1'));
        $aResult["test4"][] = array('name' => '', 'disabled' => false, 'active' => false, 'hash' => md5(''));
        $aResult["test4"][] = array('name' => '', 'disabled' => false, 'active' => false, 'hash' => md5(''));

        $aFilter = array(md5('a1'), '', '');
        $this->assertEquals(array($aResult, "test1", false), $oHandler->UNITapplyVariantSelectionsFilter($aArray, $aFilter));

        // filter 2
        // expected result
        $aResult = array();
        $aResult["test1"][] = array('name' => 'a1', 'disabled' => false, 'active' => false, 'hash' => md5('a1'));
        $aResult["test1"][] = array('name' => 'b1', 'disabled' => null, 'active' => true, 'hash' => md5('b1'));
        $aResult["test1"][] = array('name' => 'c1', 'disabled' => false, 'active' => false, 'hash' => md5('c1'));

        $aResult["test2"][] = array('name' => 'a1', 'disabled' => true, 'active' => false, 'hash' => md5('a1'));
        $aResult["test2"][] = array('name' => 'b2', 'disabled' => null, 'active' => false, 'hash' => md5('b2'));
        $aResult["test2"][] = array('name' => 'c2', 'disabled' => true, 'active' => false, 'hash' => md5('c2'));

        $aResult["test3"][] = array('name' => 'a2', 'disabled' => true, 'active' => false, 'hash' => md5('a2'));
        $aResult["test3"][] = array('name' => 'b2', 'disabled' => null, 'active' => false, 'hash' => md5('b2'));
        $aResult["test3"][] = array('name' => 'c3', 'disabled' => true, 'active' => false, 'hash' => md5('c3'));

        $aResult["test4"][] = array('name' => 'a1', 'disabled' => true, 'active' => false, 'hash' => md5('a1'));
        $aResult["test4"][] = array('name' => '', 'disabled' => null, 'active' => false, 'hash' => md5(''));
        $aResult["test4"][] = array('name' => '', 'disabled' => true, 'active' => false, 'hash' => md5(''));

        $aFilter = array('', md5('b1'));
        $this->assertEquals(array($aResult, "test1", false), $oHandler->UNITapplyVariantSelectionsFilter($aArray, $aFilter));

        // filter 3
        // expected result
        $aResult = array();
        $aResult["test1"][] = array('name' => 'a1', 'disabled' => null, 'active' => true, 'hash' => md5('a1'));
        $aResult["test1"][] = array('name' => 'b1', 'disabled' => null, 'active' => true, 'hash' => md5('b1'));
        $aResult["test1"][] = array('name' => 'c1', 'disabled' => false, 'active' => false, 'hash' => md5('c1'));

        $aResult["test2"][] = array('name' => 'a1', 'disabled' => true, 'active' => true, 'hash' => md5('a1'));
        $aResult["test2"][] = array('name' => 'b2', 'disabled' => null, 'active' => false, 'hash' => md5('b2'));
        $aResult["test2"][] = array('name' => 'c2', 'disabled' => true, 'active' => false, 'hash' => md5('c2'));

        $aResult["test3"][] = array('name' => 'a2', 'disabled' => true, 'active' => false, 'hash' => md5('a2'));
        $aResult["test3"][] = array('name' => 'b2', 'disabled' => true, 'active' => false, 'hash' => md5('b2'));
        $aResult["test3"][] = array('name' => 'c3', 'disabled' => true, 'active' => false, 'hash' => md5('c3'));

        $aResult["test4"][] = array('name' => 'a1', 'disabled' => true, 'active' => true, 'hash' => md5('a1'));
        $aResult["test4"][] = array('name' => '', 'disabled' => null, 'active' => false, 'hash' => md5(''));
        $aResult["test4"][] = array('name' => '', 'disabled' => true, 'active' => false, 'hash' => md5(''));

        $aFilter = array(md5('a1'), md5('b1'));

        $this->assertEquals(array($aResult, "test1", false), $oHandler->UNITapplyVariantSelectionsFilter($aArray, $aFilter));

        // filter 4
        // expected result
        $aResult = array();
        $aResult["test1"][] = array('name' => 'a1', 'disabled' => true, 'active' => true, 'hash' => md5('a1'));
        $aResult["test1"][] = array('name' => 'b1', 'disabled' => null, 'active' => false, 'hash' => md5('b1'));
        $aResult["test1"][] = array('name' => 'c1', 'disabled' => true, 'active' => false, 'hash' => md5('c1'));

        $aResult["test2"][] = array('name' => 'a1', 'disabled' => null, 'active' => true, 'hash' => md5('a1'));
        $aResult["test2"][] = array('name' => 'b2', 'disabled' => null, 'active' => true, 'hash' => md5('b2'));
        $aResult["test2"][] = array('name' => 'c2', 'disabled' => false, 'active' => false, 'hash' => md5('c2'));

        $aResult["test3"][] = array('name' => 'a2', 'disabled' => null, 'active' => false, 'hash' => md5('a2'));
        $aResult["test3"][] = array('name' => 'b2', 'disabled' => true, 'active' => true, 'hash' => md5('b2'));
        $aResult["test3"][] = array('name' => 'c3', 'disabled' => true, 'active' => false, 'hash' => md5('c3'));

        $aResult["test4"][] = array('name' => 'a1', 'disabled' => true, 'active' => true, 'hash' => md5('a1'));
        $aResult["test4"][] = array('name' => '', 'disabled' => null, 'active' => false, 'hash' => md5(''));
        $aResult["test4"][] = array('name' => '', 'disabled' => true, 'active' => false, 'hash' => md5(''));

        $aFilter = array(md5('a1'), md5('b2'));

        $this->assertEquals(array($aResult, "test2", false), $oHandler->UNITapplyVariantSelectionsFilter($aArray, $aFilter));

        // filter 5
        // expected result
        $aResult = array();
        $aResult["test1"][] = array('name' => 'a1', 'disabled' => true, 'active' => false, 'hash' => md5('a1'));
        $aResult["test1"][] = array('name' => 'b1', 'disabled' => true, 'active' => false, 'hash' => md5('b1'));
        $aResult["test1"][] = array('name' => 'c1', 'disabled' => true, 'active' => false, 'hash' => md5('c1'));

        $aResult["test2"][] = array('name' => 'a1', 'disabled' => true, 'active' => false, 'hash' => md5('a1'));
        $aResult["test2"][] = array('name' => 'b2', 'disabled' => true, 'active' => true, 'hash' => md5('b2'));
        $aResult["test2"][] = array('name' => 'c2', 'disabled' => true, 'active' => false, 'hash' => md5('c2'));

        $aResult["test3"][] = array('name' => 'a2', 'disabled' => null, 'active' => true, 'hash' => md5('a2'));
        $aResult["test3"][] = array('name' => 'b2', 'disabled' => null, 'active' => true, 'hash' => md5('b2'));
        $aResult["test3"][] = array('name' => 'c3', 'disabled' => null, 'active' => true, 'hash' => md5('c3'));

        $aResult["test4"][] = array('name' => 'a1', 'disabled' => true, 'active' => false, 'hash' => md5('a1'));
        $aResult["test4"][] = array('name' => '', 'disabled' => true, 'active' => false, 'hash' => md5(''));
        $aResult["test4"][] = array('name' => '', 'disabled' => true, 'active' => false, 'hash' => md5(''));

        $aFilter = array(md5('a2'), md5('b2'), md5('c3'));

        $this->assertEquals(array($aResult, "test3", true), $oHandler->UNITapplyVariantSelectionsFilter($aArray, $aFilter));

    }

    /**
     * oxVariantHandler::buildVariantSelections() test case
     *
     * @return null
     */
    public function testBuildVariantSelectionsList()
    {
        $aVarSelects = array("test1", "test2");

        // expected result
        $aSelections["test1"][] = array('name' => 'a', 'disabled' => false, 'active' => true, 'hash' => md5('a'));
        $aSelections["test1"][] = array('name' => 'b', 'disabled' => false, 'active' => false, 'hash' => md5('b'));

        $aSelections["test2"][] = array('name' => 'a', 'disabled' => false, 'active' => true, 'hash' => md5('a'));
        $aSelections["test2"][] = array('name' => 'b', 'disabled' => false, 'active' => false, 'hash' => md5('b'));

        $aSelections["test3"][] = array('name' => 'a', 'disabled' => false, 'active' => true, 'hash' => md5('a'));
        $aSelections["test3"][] = array('name' => '', 'disabled' => true, 'active' => false, 'hash' => md5(''));

        $oHandler = oxNew('oxVariantHandler');
        $aList = $oHandler->UNITbuildVariantSelectionsList($aVarSelects, $aSelections);

        // testing
        $this->assertNotNull($aList);
        $this->assertEquals(2, count($aList));

        $this->assertEquals(1, count($aList[0]));
        $this->assertEquals(1, count($aList[1]));

        $oSel1 = current($aList[0]->getSelections());
        $oSel2 = current($aList[1]->getSelections());

        $this->assertNotNull($oSel1);
        $this->assertEquals('a', $oSel1->getName());
        $this->assertEquals(md5('a'), $oSel1->getValue());
        $this->assertFalse($oSel1->isDisabled());
        $this->assertTrue($oSel1->isActive());


        $this->assertNotNull($oSel2);
        $this->assertEquals('b', $oSel2->getName());
        $this->assertEquals(md5('b'), $oSel2->getValue());
        $this->assertFalse($oSel2->isDisabled());
        $this->assertFalse($oSel2->isActive());
    }

    /**
     * oxVariantHandler::buildVariantSelections() test case
     *
     * @return null
     */
    public function testBuildVariantSelectionsNoLimit()
    {
        $oHandler = $this->getMock("oxVariantHandler", array('_getSelections', "_fillVariantSelections", "_applyVariantSelectionsFilter", "_buildVariantSelectionsList"));
        $oHandler->expects($this->once())->method('_getSelections')
            ->with($this->equalTo("testvarname"))
            ->will($this->returnValue(array('t1', 't2', 't3')));
        $oHandler->expects($this->once())->method('_fillVariantSelections')
            ->with(
                $this->equalTo(array('xdxvarid' => 'oVariant')),
                $this->equalTo(3),
                $this->equalTo('$aFilter'),
                $this->equalTo('$sActVariantId')
            )
            ->will($this->returnValue("rawselections"));

        $oHandler->expects($this->once())->method('_applyVariantSelectionsFilter')
            ->with($this->equalTo("rawselections"), $this->equalTo('$aFilter'))
            ->will($this->returnValue(array("rawselections", 'xdxvarid', 'perfecto?')));

        $oHandler->expects($this->once())->method('_buildVariantSelectionsList')
            ->with($this->equalTo(array('t1', 't2', 't3')), $this->equalTo("rawselections"))
            ->will($this->returnValue("selections"));

        $this->assertEquals(
            array("selections" => "selections", "rawselections" => "rawselections", 'oActiveVariant' => 'oVariant', 'blPerfectFit' => 'perfecto?'),
            $oHandler->buildVariantSelections("testvarname", array('xdxvarid' => 'oVariant'), '$aFilter', '$sActVariantId')
        );
    }

    /**
     * oxVariantHandler::buildVariantSelections() test case
     *
     * @return null
     */
    public function testBuildVariantSelectionsWithLimit()
    {
        $oHandler = $this->getMock("oxVariantHandler", array('_getSelections', "_fillVariantSelections", "_applyVariantSelectionsFilter", "_buildVariantSelectionsList"));
        $oHandler->expects($this->once())->method('_getSelections')
            ->with($this->equalTo("testvarname"))
            ->will($this->returnValue(array('t1', 't2', 't3')));
        $oHandler->expects($this->once())->method('_fillVariantSelections')
            ->with(
                $this->equalTo(array('xdxvarid' => 'oVariant')),
                $this->equalTo(2),
                $this->equalTo('$aFilter'),
                $this->equalTo('$sActVariantId')
            )
            ->will($this->returnValue("rawselections"));

        $oHandler->expects($this->once())->method('_applyVariantSelectionsFilter')
            ->with($this->equalTo("rawselections"), $this->equalTo('$aFilter'))
            ->will($this->returnValue(array("rawselections", 'xdxvarid', 'perfecto?')));

        $oHandler->expects($this->once())->method('_buildVariantSelectionsList')
            ->with($this->equalTo(array('t1', 't2')), $this->equalTo("rawselections"))
            ->will($this->returnValue("selections"));

        $this->assertEquals(
            array("selections" => "selections", "rawselections" => "rawselections", 'oActiveVariant' => 'oVariant', 'blPerfectFit' => 'perfecto?'),
            $oHandler->buildVariantSelections("testvarname", array('xdxvarid' => 'oVariant'), '$aFilter', '$sActVariantId', 2)
        );
    }
}