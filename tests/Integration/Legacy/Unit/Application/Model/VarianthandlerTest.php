<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use \oxVariantHandler;
use \stdClass;
use \oxField;
use \oxDb;

class oxVariantHandlerForOxvarianthandlerTest extends oxVariantHandler
{
    public function fillVariantSelections($oVariantList, $iVarSelCnt, &$aFilter, $sActVariantId)
    {
        return parent::fillVariantSelections($oVariantList, $iVarSelCnt, $aFilter, $sActVariantId);
    }
}

class VarianthandlerTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
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
     */
    public function testInit()
    {
        $oHandler = $this->getProxyClass("oxVariantHandler");
        $this->assertNull($oHandler->getNonPublicVar("_oArticles"));
        $oHandler->init("testData");
        $this->assertSame("testData", $oHandler->getNonPublicVar("_oArticles"));
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
        $this->assertSame(10, $oVariantHandler->getValuePrice($oValue, 10));
        $oValue->priceUnit = '%';
        $this->assertSame(1, $oVariantHandler->getValuePrice($oValue, 10));

        $oValue = new stdClass();
        $oValue->price = -10;
        $oValue->fprice = '10,00';
        $oValue->priceUnit = '%';
        $oValue->name = 'red';
        $oValue->value = '';
        $this->assertSame(-1, $oVariantHandler->getValuePrice($oValue, 10));
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
        $aValues = [$aValues];

        $oArticle = oxNew("oxArticle");
        $oArticle->load('2000');

        $oVariantHandler = oxNew("oxVariantHandler");
        $oVariantHandler->assignValues($aValues, oxNew('oxArticleList'), $oArticle, ['en', 'de']);

        $oRez = $myDB->select("select oxvarselect, oxvarselect_1 from oxarticles where oxparentid = '2000'");
        while (!$oRez->EOF) {
            $oRez->fields = array_change_key_case($oRez->fields, CASE_LOWER);
            $this->assertSame('red', $oRez->fields[0]);
            $this->assertSame('rot', $oRez->fields[1]);
            $oRez->fetchRow();
        }
    }

    public function testGenVariantFromSell()
    {
        $this->getConfig()->setConfigParam('blUseMultidimensionVariants', 1);
        $myDB = oxDb::getDB();
        $sVal = 'red!P!10__@@blue!P!10__@@black!P!10__@@';

        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $sSql = sprintf("insert into oxselectlist (oxid, oxshopid, oxtitle, oxident, oxvaldesc) values ('_testSell', '%d', 'oxsellisttest', 'oxsellisttest', '%s')", $sShopId, $sVal);
        $this->addToDatabase($sSql, 'oxselectlist');

        $oArticle = oxNew("oxArticle");
        $oArticle->load('2000');

        $oVariantHandler = oxNew("oxVariantHandler");
        $oVariantHandler->genVariantFromSell(['_testSell'], $oArticle);
        $this->assertSame(3, $myDB->getOne("select count(*) from oxarticles where oxparentid = '2000'"));
        //twice
        $oVariantHandler->genVariantFromSell(['_testSell'], $oArticle);
        $this->assertSame(9, $myDB->getOne("select count(*) from oxarticles where oxparentid = '2000'"));
        $this->assertTrue((bool) strpos((string) $myDB->getOne("select oxvarselect from oxarticles where oxparentid = '2000' limit 1"), "|"));
        $this->assertSame(18, $myDB->getOne("select count(*) from oxobject2attribute where oxobjectid in ( select art.oxid from oxarticles as art where art.oxparentid = '2000')"));
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

        $sSql = sprintf("insert into oxselectlist (oxid, oxshopid, oxtitle, oxident, oxvaldesc) values ('_testSell', '%d', 'oxsellisttest', 'oxsellisttest', '%s')", $sShopId, $sVal);
        $this->addToDatabase($sSql, 'oxselectlist');

        $oArticle = oxNew("oxArticle");
        $oArticle->load('2000');

        $oVariantHandler = oxNew("oxVariantHandler");
        $oVariantHandler->genVariantFromSell(['_testSell'], $oArticle);

        $oArticle2 = oxNew("oxArticle");
        $oArticle2->load('2000');
        $this->assertSame(3, $oArticle2->oxarticles__oxvarcount->value);

        /**
         * $this->assertEquals( 3, $myDB->getOne( "select count(*) from oxarticles where oxparentid = '2000'" ));
         * //twice
         * $oVariantHandler->genVariantFromSell(array('testSell'), $oArticle );
         * $this->assertEquals( 9, $myDB->getOne( "select count(*) from oxarticles where oxparentid = '2000'" ));
         * $this->assertTrue( (bool) strpos($myDB->getOne( "select oxvarselect from oxarticles where oxparentid = '2000' limit 1" ), "|"));
         * $this->assertEquals( 18, $myDB->getOne( "select count(*) from oxobject2attribute where oxobjectid in ( select art.oxid from oxarticles as art where art.oxparentid = '2000')" ));
         */
    }

    public function testCreateNewVariant()
    {
        $aParams = ['oxarticles__oxvarselect'      => "_testVar", 'oxarticles__oxartnum'         => "123", 'oxarticles__oxprice'          => "10", 'oxarticles__oxvarselect_1'    => "_testVar_1", 'oxarticles__oxid'             => "_testVar", 'oxarticles__oxisconfigurable' => "1"];
        $oVariantHandler = oxNew("oxVariantHandler");
        $sVariantId = $oVariantHandler->createNewVariant($aParams, "_testArt");
        $oVariant = oxNew("oxArticle");
        $oVariant->load($sVariantId);
        $this->assertSame("_testVar", $sVariantId);
        $this->assertSame("_testVar", $oVariant->oxarticles__oxvarselect->value);
        $this->assertSame("_testArt", $oVariant->oxarticles__oxparentid->value);
        $this->assertSame("123", $oVariant->oxarticles__oxartnum->value);
        $this->assertSame("10", $oVariant->oxarticles__oxprice->value);
        $this->assertSame("1", $oVariant->oxarticles__oxisconfigurable->value);

        $oVariant = oxNew("oxArticle");
        $oVariant->loadInLang(1, $sVariantId);
        $this->assertSame("_testVar_1", $oVariant->oxarticles__oxvarselect->value);
    }

    /**
     * oxVariantHandler::isMdVariant() test case
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
     */
    public function testBuildMdVariants()
    {
        $oPrice = $this->getMock(\OxidEsales\Eshop\Core\Price::class, ["getBruttoPrice"]);
        $oPrice->expects($this->exactly(2))->method('getBruttoPrice')->willReturn(999);

        $oVar1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getPrice", "getLink"]);
        $oVar1->expects($this->once())->method('getPrice')->willReturn($oPrice);
        $oVar1->expects($this->once())->method('getLink')->willReturn("testLink");
        $oVar1->oxarticles__oxvarselect = new oxField("var1value1 | var1value2 | var1value3");

        $oVar2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getPrice", "getLink"]);
        $oVar2->expects($this->once())->method('getPrice')->willReturn($oPrice);
        $oVar2->expects($this->once())->method('getLink')->willReturn("testLink");
        $oVar2->oxarticles__oxvarselect = new oxField("var2value1 | var2value2 | var2value3");

        $oVariants = oxNew('oxList');
        $oVariants->offsetSet("var1", $oVar1);
        $oVariants->offsetSet("var2", $oVar2);

        $oVariantHandler = oxNew("oxVariantHandler");
        $oVariantHandler->buildMdVariants($oVariants, "testParentId");
    }

    /**
     * oxVariantHandler::_fillVariantSelections() test case
     */
    public function testFillVariantSelections()
    {
        $aFilter = [];

        // empty variant list
        $oHandler = new oxVariantHandlerForOxvarianthandlerTest();
        $this->assertSame([], $oHandler->fillVariantSelections([], 100, $aFilter, ""));

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

        $aArray[$oVariant1->getId()][] = ['name' => 'a', 'disabled' => null, 'active' => false, 'hash' => md5('a')];
        $aArray[$oVariant1->getId()][] = ['name' => 'b', 'disabled' => null, 'active' => false, 'hash' => md5('b')];

        $aArray[$oVariant2->getId()][] = ['name' => 'a', 'disabled' => null, 'active' => false, 'hash' => md5('a')];
        $aArray[$oVariant2->getId()][] = ['name' => 'b', 'disabled' => null, 'active' => false, 'hash' => md5('b')];

        $aArray[$oVariant3->getId()][] = ['name' => 'a', 'disabled' => null, 'active' => false, 'hash' => md5('a')];

        // checking
        $oHandler = new oxVariantHandlerForOxvarianthandlerTest();
        $this->assertEquals($aArray, $oHandler->fillVariantSelections([$oVariant1, $oVariant2, $oVariant3], 2, $aFilter, "test1"));
        $this->assertSame(["0cc175b9c0f1b6a831c399e269772661", "92eb5ffee6ae2fec3ad71c777531578f"], $aFilter);
    }


    /**
     * oxVariantHandler::_applyVariantSelectionsFilter() test case
     */
    public function testApplyVariantSelectionsFilter()
    {
        // test data
        $aArray["test1"][] = ['name' => 'a1', 'disabled' => null, 'active' => false, 'hash' => md5('a1')];
        $aArray["test1"][] = ['name' => 'b1', 'disabled' => null, 'active' => false, 'hash' => md5('b1')];
        $aArray["test1"][] = ['name' => 'c1', 'disabled' => null, 'active' => false, 'hash' => md5('c1')];

        $aArray["test2"][] = ['name' => 'a1', 'disabled' => null, 'active' => false, 'hash' => md5('a1')];
        $aArray["test2"][] = ['name' => 'b2', 'disabled' => null, 'active' => false, 'hash' => md5('b2')];
        $aArray["test2"][] = ['name' => 'c2', 'disabled' => null, 'active' => false, 'hash' => md5('c2')];

        $aArray["test3"][] = ['name' => 'a2', 'disabled' => null, 'active' => false, 'hash' => md5('a2')];
        $aArray["test3"][] = ['name' => 'b2', 'disabled' => null, 'active' => false, 'hash' => md5('b2')];
        $aArray["test3"][] = ['name' => 'c3', 'disabled' => null, 'active' => false, 'hash' => md5('c3')];

        $aArray["test4"][] = ['name' => 'a1', 'disabled' => null, 'active' => false, 'hash' => md5('a1')];
        $aArray["test4"][] = ['name' => '', 'disabled' => null, 'active' => false, 'hash' => md5('')];
        $aArray["test4"][] = ['name' => '', 'disabled' => null, 'active' => false, 'hash' => md5('')];

        $oHandler = oxNew('oxVariantHandler');

        // no filter
        $aFilter = [];
        $this->assertEquals([$aArray, null, false], $oHandler->applyVariantSelectionsFilter($aArray, $aFilter));

        // filter 1
        // expected result
        $aResult = [];
        $aResult["test1"][] = ['name' => 'a1', 'disabled' => null, 'active' => true, 'hash' => md5('a1')];
        $aResult["test1"][] = ['name' => 'b1', 'disabled' => false, 'active' => false, 'hash' => md5('b1')];
        $aResult["test1"][] = ['name' => 'c1', 'disabled' => false, 'active' => false, 'hash' => md5('c1')];

        $aResult["test2"][] = ['name' => 'a1', 'disabled' => null, 'active' => true, 'hash' => md5('a1')];
        $aResult["test2"][] = ['name' => 'b2', 'disabled' => false, 'active' => false, 'hash' => md5('b2')];
        $aResult["test2"][] = ['name' => 'c2', 'disabled' => false, 'active' => false, 'hash' => md5('c2')];

        $aResult["test3"][] = ['name' => 'a2', 'disabled' => null, 'active' => false, 'hash' => md5('a2')];
        $aResult["test3"][] = ['name' => 'b2', 'disabled' => true, 'active' => false, 'hash' => md5('b2')];
        $aResult["test3"][] = ['name' => 'c3', 'disabled' => true, 'active' => false, 'hash' => md5('c3')];

        $aResult["test4"][] = ['name' => 'a1', 'disabled' => null, 'active' => true, 'hash' => md5('a1')];
        $aResult["test4"][] = ['name' => '', 'disabled' => false, 'active' => false, 'hash' => md5('')];
        $aResult["test4"][] = ['name' => '', 'disabled' => false, 'active' => false, 'hash' => md5('')];

        $aFilter = [md5('a1'), '', ''];
        $this->assertEquals([$aResult, "test1", false], $oHandler->applyVariantSelectionsFilter($aArray, $aFilter));

        // filter 2
        // expected result
        $aResult = [];
        $aResult["test1"][] = ['name' => 'a1', 'disabled' => false, 'active' => false, 'hash' => md5('a1')];
        $aResult["test1"][] = ['name' => 'b1', 'disabled' => null, 'active' => true, 'hash' => md5('b1')];
        $aResult["test1"][] = ['name' => 'c1', 'disabled' => false, 'active' => false, 'hash' => md5('c1')];

        $aResult["test2"][] = ['name' => 'a1', 'disabled' => true, 'active' => false, 'hash' => md5('a1')];
        $aResult["test2"][] = ['name' => 'b2', 'disabled' => null, 'active' => false, 'hash' => md5('b2')];
        $aResult["test2"][] = ['name' => 'c2', 'disabled' => true, 'active' => false, 'hash' => md5('c2')];

        $aResult["test3"][] = ['name' => 'a2', 'disabled' => true, 'active' => false, 'hash' => md5('a2')];
        $aResult["test3"][] = ['name' => 'b2', 'disabled' => null, 'active' => false, 'hash' => md5('b2')];
        $aResult["test3"][] = ['name' => 'c3', 'disabled' => true, 'active' => false, 'hash' => md5('c3')];

        $aResult["test4"][] = ['name' => 'a1', 'disabled' => true, 'active' => false, 'hash' => md5('a1')];
        $aResult["test4"][] = ['name' => '', 'disabled' => null, 'active' => false, 'hash' => md5('')];
        $aResult["test4"][] = ['name' => '', 'disabled' => true, 'active' => false, 'hash' => md5('')];

        $aFilter = ['', md5('b1')];
        $this->assertEquals([$aResult, "test1", false], $oHandler->applyVariantSelectionsFilter($aArray, $aFilter));

        // filter 3
        // expected result
        $aResult = [];
        $aResult["test1"][] = ['name' => 'a1', 'disabled' => null, 'active' => true, 'hash' => md5('a1')];
        $aResult["test1"][] = ['name' => 'b1', 'disabled' => null, 'active' => true, 'hash' => md5('b1')];
        $aResult["test1"][] = ['name' => 'c1', 'disabled' => false, 'active' => false, 'hash' => md5('c1')];

        $aResult["test2"][] = ['name' => 'a1', 'disabled' => true, 'active' => true, 'hash' => md5('a1')];
        $aResult["test2"][] = ['name' => 'b2', 'disabled' => null, 'active' => false, 'hash' => md5('b2')];
        $aResult["test2"][] = ['name' => 'c2', 'disabled' => true, 'active' => false, 'hash' => md5('c2')];

        $aResult["test3"][] = ['name' => 'a2', 'disabled' => true, 'active' => false, 'hash' => md5('a2')];
        $aResult["test3"][] = ['name' => 'b2', 'disabled' => true, 'active' => false, 'hash' => md5('b2')];
        $aResult["test3"][] = ['name' => 'c3', 'disabled' => true, 'active' => false, 'hash' => md5('c3')];

        $aResult["test4"][] = ['name' => 'a1', 'disabled' => true, 'active' => true, 'hash' => md5('a1')];
        $aResult["test4"][] = ['name' => '', 'disabled' => null, 'active' => false, 'hash' => md5('')];
        $aResult["test4"][] = ['name' => '', 'disabled' => true, 'active' => false, 'hash' => md5('')];

        $aFilter = [md5('a1'), md5('b1')];

        $this->assertEquals([$aResult, "test1", false], $oHandler->applyVariantSelectionsFilter($aArray, $aFilter));

        // filter 4
        // expected result
        $aResult = [];
        $aResult["test1"][] = ['name' => 'a1', 'disabled' => true, 'active' => true, 'hash' => md5('a1')];
        $aResult["test1"][] = ['name' => 'b1', 'disabled' => null, 'active' => false, 'hash' => md5('b1')];
        $aResult["test1"][] = ['name' => 'c1', 'disabled' => true, 'active' => false, 'hash' => md5('c1')];

        $aResult["test2"][] = ['name' => 'a1', 'disabled' => null, 'active' => true, 'hash' => md5('a1')];
        $aResult["test2"][] = ['name' => 'b2', 'disabled' => null, 'active' => true, 'hash' => md5('b2')];
        $aResult["test2"][] = ['name' => 'c2', 'disabled' => false, 'active' => false, 'hash' => md5('c2')];

        $aResult["test3"][] = ['name' => 'a2', 'disabled' => null, 'active' => false, 'hash' => md5('a2')];
        $aResult["test3"][] = ['name' => 'b2', 'disabled' => true, 'active' => true, 'hash' => md5('b2')];
        $aResult["test3"][] = ['name' => 'c3', 'disabled' => true, 'active' => false, 'hash' => md5('c3')];

        $aResult["test4"][] = ['name' => 'a1', 'disabled' => true, 'active' => true, 'hash' => md5('a1')];
        $aResult["test4"][] = ['name' => '', 'disabled' => null, 'active' => false, 'hash' => md5('')];
        $aResult["test4"][] = ['name' => '', 'disabled' => true, 'active' => false, 'hash' => md5('')];

        $aFilter = [md5('a1'), md5('b2')];

        $this->assertEquals([$aResult, "test2", false], $oHandler->applyVariantSelectionsFilter($aArray, $aFilter));

        // filter 5
        // expected result
        $aResult = [];
        $aResult["test1"][] = ['name' => 'a1', 'disabled' => true, 'active' => false, 'hash' => md5('a1')];
        $aResult["test1"][] = ['name' => 'b1', 'disabled' => true, 'active' => false, 'hash' => md5('b1')];
        $aResult["test1"][] = ['name' => 'c1', 'disabled' => true, 'active' => false, 'hash' => md5('c1')];

        $aResult["test2"][] = ['name' => 'a1', 'disabled' => true, 'active' => false, 'hash' => md5('a1')];
        $aResult["test2"][] = ['name' => 'b2', 'disabled' => true, 'active' => true, 'hash' => md5('b2')];
        $aResult["test2"][] = ['name' => 'c2', 'disabled' => true, 'active' => false, 'hash' => md5('c2')];

        $aResult["test3"][] = ['name' => 'a2', 'disabled' => null, 'active' => true, 'hash' => md5('a2')];
        $aResult["test3"][] = ['name' => 'b2', 'disabled' => null, 'active' => true, 'hash' => md5('b2')];
        $aResult["test3"][] = ['name' => 'c3', 'disabled' => null, 'active' => true, 'hash' => md5('c3')];

        $aResult["test4"][] = ['name' => 'a1', 'disabled' => true, 'active' => false, 'hash' => md5('a1')];
        $aResult["test4"][] = ['name' => '', 'disabled' => true, 'active' => false, 'hash' => md5('')];
        $aResult["test4"][] = ['name' => '', 'disabled' => true, 'active' => false, 'hash' => md5('')];

        $aFilter = [md5('a2'), md5('b2'), md5('c3')];

        $this->assertEquals([$aResult, "test3", true], $oHandler->applyVariantSelectionsFilter($aArray, $aFilter));
    }

    /**
     * oxVariantHandler::buildVariantSelections() test case
     */
    public function testBuildVariantSelectionsList()
    {
        $aVarSelects = ["test1", "test2"];

        // expected result
        $aSelections["test1"][] = ['name' => 'a', 'disabled' => false, 'active' => true, 'hash' => md5('a')];
        $aSelections["test1"][] = ['name' => 'b', 'disabled' => false, 'active' => false, 'hash' => md5('b')];

        $aSelections["test2"][] = ['name' => 'a', 'disabled' => false, 'active' => true, 'hash' => md5('a')];
        $aSelections["test2"][] = ['name' => 'b', 'disabled' => false, 'active' => false, 'hash' => md5('b')];

        $aSelections["test3"][] = ['name' => 'a', 'disabled' => false, 'active' => true, 'hash' => md5('a')];
        $aSelections["test3"][] = ['name' => '', 'disabled' => true, 'active' => false, 'hash' => md5('')];

        $oHandler = oxNew('oxVariantHandler');
        $aList = $oHandler->buildVariantSelectionsList($aVarSelects, $aSelections);

        // testing
        $this->assertNotNull($aList);
        $this->assertCount(2, $aList);

        $this->assertCount(1, $aList[0]->getSelections());
        $this->assertCount(1, $aList[1]->getSelections());

        $oSel1 = current($aList[0]->getSelections());
        $oSel2 = current($aList[1]->getSelections());

        $this->assertNotNull($oSel1);
        $this->assertSame('a', $oSel1->getName());
        $this->assertSame(md5('a'), $oSel1->getValue());
        $this->assertFalse($oSel1->isDisabled());
        $this->assertTrue($oSel1->isActive());


        $this->assertNotNull($oSel2);
        $this->assertSame('b', $oSel2->getName());
        $this->assertSame(md5('b'), $oSel2->getValue());
        $this->assertFalse($oSel2->isDisabled());
        $this->assertFalse($oSel2->isActive());
    }

    /**
     * oxVariantHandler::buildVariantSelections() test case
     */
    public function testBuildVariantSelectionsNoLimit()
    {
        $oHandler = $this->getMock(\OxidEsales\Eshop\Application\Model\VariantHandler::class, ['getSelections', "fillVariantSelections", "applyVariantSelectionsFilter", "buildVariantSelectionsList"]);
        $oHandler->expects($this->once())->method('getSelections')
            ->with("testvarname")
            ->willReturn(['t1', 't2', 't3']);
        $oHandler->expects($this->once())->method('fillVariantSelections')
            ->with(
                ['xdxvarid' => 'oVariant'],
                3,
                '$aFilter',
                '$sActVariantId'
            )
            ->willReturn("rawselections");

        $oHandler->expects($this->once())->method('applyVariantSelectionsFilter')
            ->with("rawselections", '$aFilter')
            ->willReturn(["rawselections", 'xdxvarid', 'perfecto?']);

        $oHandler->expects($this->once())->method('buildVariantSelectionsList')
            ->with(['t1', 't2', 't3'], "rawselections")
            ->willReturn("selections");

        $this->assertSame(
            ["selections" => "selections", "rawselections" => "rawselections", 'oActiveVariant' => 'oVariant', 'blPerfectFit' => 'perfecto?'],
            $oHandler->buildVariantSelections("testvarname", ['xdxvarid' => 'oVariant'], '$aFilter', '$sActVariantId')
        );
    }

    /**
     * oxVariantHandler::buildVariantSelections() test case
     */
    public function testBuildVariantSelectionsWithLimit()
    {
        $oHandler = $this->getMock(\OxidEsales\Eshop\Application\Model\VariantHandler::class, ['getSelections', "fillVariantSelections", "applyVariantSelectionsFilter", "buildVariantSelectionsList"]);
        $oHandler->expects($this->once())->method('getSelections')
            ->with("testvarname")
            ->willReturn(['t1', 't2', 't3']);
        $oHandler->expects($this->once())->method('fillVariantSelections')
            ->with(
                ['xdxvarid' => 'oVariant'],
                2,
                '$aFilter',
                '$sActVariantId'
            )
            ->willReturn("rawselections");

        $oHandler->expects($this->once())->method('applyVariantSelectionsFilter')
            ->with("rawselections", '$aFilter')
            ->willReturn(["rawselections", 'xdxvarid', 'perfecto?']);

        $oHandler->expects($this->once())->method('buildVariantSelectionsList')
            ->with(['t1', 't2'], "rawselections")
            ->willReturn("selections");

        $this->assertSame(
            ["selections" => "selections", "rawselections" => "rawselections", 'oActiveVariant' => 'oVariant', 'blPerfectFit' => 'perfecto?'],
            $oHandler->buildVariantSelections("testvarname", ['xdxvarid' => 'oVariant'], '$aFilter', '$sActVariantId', 2)
        );
    }
}
