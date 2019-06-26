<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxprice;
use \oxbasket;
use \oxArticle;
use \oxField;
use \stdClass;
use \oxDb;

class modoxprice extends oxprice
{
    protected $_dPrice = null;

    public function getBruttoPrice()
    {
        return $this->_dPrice;
    }

    public function setPrice($newPrice, $dVat = null)
    {
        $this->_dPrice = $newPrice;
    }
}

class oxBasket_Extended extends oxbasket
{
    public $oBasketSummaryCache = null;

    public function getBasketSummary()
    {
        if ($this->oBasketSummaryCache) {
            return $this->oBasketSummaryCache;
        } else {
            return parent::getBasketSummary();
        }
    }
}

class oxArticle_Extended extends oxArticle
{
    public $aCategoryIdsCache = null;
    public $oBasketSummaryCache = null;
    public $dBasePriceCache = null;
    public $oPriceCache = null;

    public function getCategoryIds($blActCats = false, $blSkipCache = false)
    {
        if ($this->aCategoryIdsCache) {
            return $this->aCategoryIdsCache;
        } else {
            return parent::GetCategoryIds();
        }
    }

    public function getBasketSummary()
    {
        if ($this->oBasketSummaryCache) {
            return $this->oBasketSummaryCache;
        } else {
            return parent::getBasketSummary();
        }
    }

    public function getBasePrice($dAmount = 1)
    {
        if ($this->dBasePriceCache) {
            return $this->dBasePriceCache;
        } else {
            return parent::getBasePrice();
        }
    }

    public function getPrice($dAmount = 1)
    {
        if ($this->oPriceCache) {
            return $this->oPriceCache;
        } else {
            return parent::getPrice();
        }
    }
}

/**
 * OxDiscountList tester
 */
class DiscountTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->_removeDiscounts();

        oxRemClassModule('oxArticle_Extended');
        oxRemClassModule('oxBasket_Extended');

        $this->cleanUpTable('oxdiscount');
        $this->cleanUpTable('oxobject2discount');
        parent::tearDown();
    }

    protected function _removeDiscounts()
    {
        $myDB = oxDb::getDb();

        $myDB->Execute("delete from oxdiscount where oxid like 'test%' ");
        $myDB->Execute("delete from oxobject2discount where oxid like 'test%' ");
        $myDB->Execute("delete from oxobject2discount where oxid='testIsForArticle'");
        $myDB->Execute("delete from oxobject2discount where oxid='testCheckForArticleNotSpecForItem'");
    }

    /**
     * Testing if deletion does not leave records in DB
     */
    public function testDelete()
    {
        $myDB = oxDb::getDb();

        $oDiscount = oxNew('oxDiscount');
        $sId = 'testDelete';
        $oDiscount->setId($sId);
        $oDiscount->save();

        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUE('testDeleteoxid','" . $sId . "','nothing','empty')");
        $oDiscount->load($sId);

        // now deleting and checking for records in DB
        $oDiscount->delete();
        $this->assertEquals(0, (int) $myDB->getOne('select count(*) from oxdiscount where oxid = "' . $sId . '"'));
        $this->assertEquals(0, (int) $myDB->getOne('select count(*) from oxobject2discount where oxdiscountid = "' . $sId . '"'));
    }

    public function testDeleteIfIdSet()
    {
        $myDB = oxDb::getDb();

        $oDiscount = oxNew('oxDiscount');
        $sId = 'testDelete';
        $oDiscount->setId($sId);
        $oDiscount->save();

        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUE('testDeleteoxid','" . $sId . "','nothing','empty')");
        $oDiscount->load($sId);

        // now deleting and checking for records in DB
        $oDiscount->delete($sId);
        $this->assertEquals(0, (int) $myDB->getOne('select count(*) from oxdiscount where oxid = "' . $sId . '"'));
        $this->assertEquals(0, (int) $myDB->getOne('select count(*) from oxobject2discount where oxdiscountid = "' . $sId . '"'));
    }

    public function testDeleteNotSetValue()
    {
        $oDiscount = oxNew('oxDiscount');

        $this->assertFalse($oDiscount->delete());
    }

    /**
     * Saving the Discount record with the field oxsort set to NULL will do an automatic increment of oxsort,
     */
    public function testSaveOxSortNull()
    {
        $id = '_testSaveOxSortNull';
        $shopId = 1;

        $discount = oxNew('oxDiscount');
        $discount->setId($id);
        $discount->oxdiscount__oxtitle = new oxField($id);
        $discount->oxdiscount__oxsort = null;
        $discount->oxdiscount__oxshopid = new oxField($shopId, oxField::T_RAW);
        $discount->save();

        $oxsort = $discount->oxdiscount__oxsort->value;
        $this->assertGreaterThan(0, $oxsort, 'The value of oxsort must be greater than the default value 0');
    }

    /**
     * Saving the Discount record with the field oxsort set to a non numeric value will trigger an InputException
     */
    public function testSaveOxSortNotNumeric()
    {
        $this->expectException('OxidEsales\EshopCommunity\Core\Exception\InputException');
        $this->expectExceptionMessage('DISCOUNT_ERROR_OXSORT_NOT_A_NUMBER');

        $id = '_testSaveOxSortNotNumeric';
        $shopId = 1;

        $discount = oxNew('oxDiscount');
        $discount->setId($id);
        $discount->oxdiscount__oxtitle = new oxField($id);
        $discount->oxdiscount__oxsort = new oxField('NotNumeric');
        $discount->oxdiscount__oxshopid = new oxField($shopId, oxField::T_RAW);

        $discount->save();
    }

    /**
     * Saving the Discount record with the field oxsort set to a duplicate value will trigger an InputException
     */
    public function testSaveOxSortNotUnique()
    {
        $this->expectException('OxidEsales\EshopCommunity\Core\Exception\InputException');
        $this->expectExceptionMessage('DISCOUNT_ERROR_OXSORT_NOT_UNIQUE');
        $oxSort = 1;

        /** Save the first record */
        $id_1 = '_testSaveOxSortNotUnique_1';
        $shopId = 1;

        $discount = oxNew('oxDiscount');
        $discount->setId($id_1);
        $discount->oxdiscount__oxtitle = new oxField($id_1);
        $discount->oxdiscount__oxsort = new oxField($oxSort, oxField::T_RAW);
        $discount->oxdiscount__oxshopid = new oxField($shopId, oxField::T_RAW);

        $discount->save();

        /**
         * Saving the second record with oxsort set to the same value will trigger an InputException
         */
        $id_2 = '_testSaveOxSortNotUnique_2';
        $shopId = 1;

        $discount = oxNew('oxDiscount');
        $discount->setId($id_2);
        $discount->oxdiscount__oxtitle = new oxField($id_2);
        $discount->oxdiscount__oxsort = new oxField($oxSort, oxField::T_RAW);
        $discount->oxdiscount__oxshopid = new oxField($shopId, oxField::T_RAW);

        $discount->save();
    }

    /**
     * When article base price is higher than discount priceTo - discount should not be valid
     */
    public function testIsForArticle_ArticleBasePriceTooLow()
    {
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->oxdiscount__oxpriceto = new oxField(10);
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getBasePrice'));
        $oArticle->expects($this->any())->method('getBasePrice')->will($this->returnValue(50));

        $this->assertFalse($oDiscount->isForArticle($oArticle));
    }

    /**
     * When discount has no articles or categories assigned, it is considered global
     */
    public function testIsGlobalDiscount_True()
    {
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->setId('testIsGlobalDiscount');
        $oDiscount->save();
        $this->assertTrue($oDiscount->isGlobalDiscount());
    }

    /**
     * When discount has any article assigned, it is considered not global
     */
    public function testIsGlobalDiscount_DiscountForArticle_False()
    {
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->setId('testGlobalDiscount');
        $oDiscount->save();

        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES( 'testIsGlobalDiscount', 'testGlobalDiscount','1000','oxarticles')");

        $this->assertFalse($oDiscount->isGlobalDiscount());
    }

    /**
     * When discount has any category assigned, it is considered not global
     */
    public function testIsGlobalDiscount_DiscountForCategory_False()
    {
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->setId('testGlobalDiscount');
        $oDiscount->save();

        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES( 'testIsGlobalDiscount', 'testGlobalDiscount','1000','oxcategories')");

        $this->assertFalse($oDiscount->isGlobalDiscount());
    }

    /**
     * Testing "for article" check
     */
    // main article
    public function testIsForArticleMainArticle()
    {
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testdid';
        $oDiscount->setId($testDiscId);
        $oDiscount->save();

        $oArticle = oxNew('oxArticle');

        //an item discount should return false
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('itm', oxField::T_RAW);
        $this->assertFalse($oDiscount->isForArticle($oArticle));

        //an discount with amount or price should return false
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(null, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(1, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForArticle($oArticle));

        $oDiscount->oxdiscount__oxprice = new oxField(1, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(null, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForArticle($oArticle));

        //if there is a discount for the article return true
        $testAid = 'testaid';
        $oDiscount->oxdiscount__oxprice = new oxField(null, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(null, oxField::T_RAW);
        $oArticle->setId($testAid);
        //$oArticle->save();

        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES( 'testIsForArticle', '" . $testDiscId . "','" . $testAid . "','oxarticles')");
        $this->assertTrue($oDiscount->isForArticle($oArticle));

        //global discount for all articles
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testdid';
        $oDiscount->setId($testDiscId);
        $oDiscount->save();
        oxDb::getDb()->Execute("delete from oxobject2discount where oxid='testIsForArticle'");
        $this->assertTrue($oDiscount->isForArticle($oArticle));

        //no article discount but fitting category
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testdid';
        $oDiscount->setId($testDiscId);
        $oDiscount->save();
        $oArticle = new oxArticle_Extended();
        $testCatId = 'testcatid';
        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES('testIsForArticle','" . $testDiscId . "','" . $testCatId . "','oxcategories')");
        $oArticle->aCategoryIdsCache = array($testCatId);
        $this->assertTrue($oDiscount->isForArticle($oArticle));

        //no article discount for fitting category
        $oArticle = oxNew('oxArticle');
        $oArticle->setId($testAid);
        $this->assertFalse($oDiscount->isForArticle($oArticle));
    }

    //no article discount for fitting category
    public function testIsForArticleFittingOnlyCat()
    {
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testdid';
        $oDiscount->setId($testDiscId);
        $oDiscount->save();

        //an discount with amount or price should return false
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $testAid = 'testaid';
        $oDiscount->oxdiscount__oxprice = new oxField(null, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(null, oxField::T_RAW);

        $testCatId = 'testcatid';
        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES('testIsForArticle','" . $testDiscId . "','" . $testCatId . "','oxcategories')");

        //no article discount for fitting category
        $oArticle = oxNew('oxArticle');
        $oArticle->setId($testAid);
        $this->assertFalse($oDiscount->isForArticle($oArticle));
    }

    //no article discount
    public function testIsForArticleNoFittingDiscounts()
    {
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testdid';
        $oDiscount->setId($testDiscId);
        $oDiscount->save();

        //an discount with amount or price should return false
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(null, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(null, oxField::T_RAW);

        //no article discount for fitting category
        $oArticle = new oxArticle_Extended();
        $testCatId = 'testcatid';
        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES('testIsForArticle','" . $testDiscId . "','" . $testCatId . "','oxcategories')");
        $oArticle->aCategoryIdsCache = array('testcatid2');
        $this->assertFalse($oDiscount->isForArticle($oArticle));
    }

    // variant ( FS#2625 )
    public function testIsForArticleVariant()
    {
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testdid';
        $oDiscount->setId($testDiscId);
        $oDiscount->save();

        $testAid = 'testaid';
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxparentid = new oxField($testAid, oxField::T_RAW);
        //$oArticle->save();

        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES( 'testIsForArticle', '" . $testDiscId . "','" . $testAid . "','oxarticles')");
        $this->assertTrue($oDiscount->isForArticle($oArticle));

        //global discount for all articles
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testdid';
        $oDiscount->setId($testDiscId);
        $oDiscount->save();
        oxDb::getDb()->Execute("delete from oxobject2discount where oxid='testIsForArticle'");
        $this->assertTrue($oDiscount->isForArticle($oArticle));
    }

    //amount discount from 0 to n, price discount is off (M:792)
    public function testIsForArticleWithAmountFromZeroToN()
    {
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testdid';
        $oDiscount->setId($testDiscId);
        $oDiscount->save();

        //an discount with amount or price should return false
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxpriceto = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamountto = new oxField(999, oxField::T_RAW);

        //no article discount for fitting category
        $oArticle = new oxArticle_Extended();
        $testCatId = 'testcatid';
        $oArticle->dBasePriceCache = 15;
        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES('testIsForArticle','" . $testDiscId . "','" . $oArticle->getId() . "','oxarticles')");
        $this->assertTrue($oDiscount->isForArticle($oArticle));
    }

    //amount discount is off, price discount from 0 to n
    public function testIsForArticleWithPriceDiscountFromZeroToN()
    {
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testdid';
        $oDiscount->setId($testDiscId);
        $oDiscount->save();

        //an discount with amount or price should return false
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxpriceto = new oxField(999, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamountto = new oxField(0, oxField::T_RAW);

        //no article discount for fitting category
        $oArticle = new oxArticle_Extended();
        $testCatId = 'testcatid';
        $oArticle->dBasePriceCache = 15;
        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES('testIsForArticle','" . $testDiscId . "','" . $oArticle->getId() . "','oxarticles')");
        $this->assertTrue($oDiscount->isForArticle($oArticle));
    }

    /**
     * Tests for basket item check
     */
    /**
     * Test case:
     * discount is setup for 3 products (amounts 3, 1, 2), amount
     * from 5 to 10
     */
    public function testIsForBasketItemForTestCase()
    {
        $this->getConfig()->setConfigParam("blVariantParentBuyable", 1);
        $sDiscountId = '_' . uniqid(rand());

        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $sQ = "insert into oxdiscount ( oxid, oxshopid, oxactive, oxtitle, oxamount, oxamountto, oxpriceto,  oxaddsumtype, oxaddsum )
                   values ( '{$sDiscountId}', '" . $this->getConfig()->getBaseShopId() . "', '1', 'Test', '5', '10', '0', 'abs', '10' )";
        } else {
            $sQ = "insert into oxdiscount ( oxid, oxshopid, oxactive, oxtitle, oxamount, oxamountto, oxpriceto, oxaddsumtype, oxaddsum )
                   values ( '{$sDiscountId}', '" . $this->getConfig()->getBaseShopId() . "', '1', 'Test', '5', '10', '0', 'abs', '10' )";
        }
        $this->addToDatabase($sQ, 'oxdiscount');

        // inserting test discount
        $sQ = "insert into oxobject2discount ( oxid, oxdiscountid, oxobjectid, oxtype )
               values
               ( '_test" . uniqid(rand(), true) . ".', '{$sDiscountId}', '1126', 'oxarticles' ),
               ( '_test" . uniqid(rand(), true) . ".', '{$sDiscountId}', '1127', 'oxarticles' ),
               ( '_test" . uniqid(rand(), true) . ".', '{$sDiscountId}', '1131', 'oxarticles' ) ";
        oxDb::getDb()->Execute($sQ);

        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('1127', 1);
        $oBasket->addToBasket('1131', 4);
        $oBasket->addToBasket('1142', 6);

        $oDiscount = oxNew('oxDiscount');
        $oDiscount->load($sDiscountId);

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1127');
        $this->assertTrue($oDiscount->isForBasketItem($oArticle) && $oDiscount->isForBasketAmount($oBasket));

        $oBasket->addToBasket('1126', 6);

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1127');

        $this->assertTrue($oDiscount->isForBasketItem($oArticle));
        $this->assertFalse($oDiscount->isForBasketAmount($oBasket));
    }

    /**
     * Test case:
     * discount is setup for 3 products (amounts 3, 1, 2), price
     * from 500 to 1000
     */
    public function testIsForBasketItemForTestCase2()
    {
        $this->getConfig()->setConfigParam("blVariantParentBuyable", 1);
        $sDiscountId = '_' . uniqid(rand());

        // inserting test discount
        $query = "insert into oxdiscount ( oxid, oxshopid, oxactive, oxtitle, oxamount, oxamountto, oxpriceto, oxprice, oxaddsumtype, oxaddsum )
               values ( '{$sDiscountId}', '" . $this->getConfig()->getBaseShopId() . "', '1', 'Test', '0', '0', '1000', '500', 'abs', '10' )";

        $this->addToDatabase($query, 'oxdiscount');

        // inserting test discount
        $query = "insert into oxobject2discount ( oxid, oxdiscountid, oxobjectid, oxtype )
               values
               ( '_test" . uniqid(rand(), true) . ".', '{$sDiscountId}', '1126', 'oxarticles' ),
               ( '_test" . uniqid(rand(), true) . ".', '{$sDiscountId}', '1127', 'oxarticles' ),
               ( '_test" . uniqid(rand(), true) . ".', '{$sDiscountId}', '1131', 'oxarticles' ) ";
        oxDb::getDb()->Execute($query);

        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('1127', 10); // 80
        $oBasket->addToBasket('1131', 10); // 230

        $oDiscount = oxNew('oxDiscount');
        $oDiscount->load($sDiscountId);

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1127');
        $this->assertTrue($oDiscount->isForBasketItem($oArticle));
        $this->assertFalse($oDiscount->isForBasketAmount($oBasket));

        $oBasket->addToBasket('1126', 10);

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1127');

        $this->assertTrue($oDiscount->isForBasketItem($oArticle) && $oDiscount->isForBasketAmount($oBasket));
        $this->assertTrue($oDiscount->isForBasketItem($oArticle) && $oDiscount->isForBasketAmount($oBasket));
    }

    // testing discount params check
    public function testIsForBasketItem()
    {
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testIsForBasketItem';
        $oDiscount->setId($testDiscId);

        $oArticle = oxNew('oxArticle');
        $oBasket = oxNew('oxBasket');

        //an discount with amount or price should return false
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(null, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(1, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBasketItem($oArticle, $oBasket));

        $oDiscount->oxdiscount__oxprice = new oxField(1, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(null, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBasketItem($oArticle, $oBasket));
    }

    // variant check
    public function testIsForBasketItemVariantCheck()
    {
        $testAid = 'xxx';
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxparentid = new oxField($testAid);

        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES( 'testIsForArticle', 'testdid', '{$testAid}', 'oxarticles' )");

        $oDiscount = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, array('_checkForArticleCategories'));
        $oDiscount->expects($this->never())->method('_checkForArticleCategories');

        // setting up discount
        $oDiscount->oxdiscount__oxamount = new oxField(1);
        $oDiscount->oxdiscount__oxprice = new oxField(1);
        $oDiscount->setId('testdid');

        // testing
        $oDiscount->isForBasketItem($oArticle);
    }

    // main article check
    public function testIsForBasketItemMainArticleCheck()
    {
        $testAid = 'xxx';
        $oArticle = oxNew('oxArticle');
        $oArticle->setId($testAid);

        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES( 'testIsForArticle', 'testdid', '{$testAid}', 'oxarticles' )");

        $oDiscount = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, array('_checkForArticleCategories'));
        $oDiscount->expects($this->never())->method('_checkForArticleCategories');

        // setting up discount
        $oDiscount->oxdiscount__oxamount = new oxField(1);
        $oDiscount->oxdiscount__oxprice = new oxField(1);
        $oDiscount->setId('testdid');

        // testing
        $oDiscount->isForBasketItem($oArticle);
    }

    //if general discount
    public function testIsForBasketItemIfGeneralDiscount()
    {
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testIsForBasketItem';
        $oDiscount->setId($testDiscId);

        $oArticle = oxNew('oxArticle');
        $oBasket = oxNew('oxBasket');

        //an discount with amount or price should return false
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('itm', oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(0, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBasketItem($oArticle, $oBasket));
    }

    //if bundel discount
    public function testIsForBasketItemIfBundelDiscount()
    {
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testIsForBasketItem';
        $oDiscount->setId($testDiscId);

        $oArticle = oxNew('oxArticle');
        $oBasket = oxNew('oxBasket');

        //an discount with amount or price should return false
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('itm', oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(1, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(1, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBasketItem($oArticle, $oBasket));
    }

    public function testIsForBasket()
    {
        $oDiscount = oxNew('oxDiscount');
        $testDiscId = 'testIsForBasketDisId';
        $oDiscount->setId($testDiscId);

        $oBasket = oxNew('oxBasket');

        //an discount with amount or price should return false
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(0, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBasket($oBasket));


        $oDiscount->oxdiscount__oxamount = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamountto = new oxField(10, oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(0, oxField::T_RAW);
        $oDiscount->oxdiscount__oxpriceto = new oxField(20, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(1, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBasket($oBasket));

        $oDiscount->oxdiscount__oxprice = new oxField(1, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(0, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBasket($oBasket));

        $oBasket = new oxBasket_Extended();
        $oBasket->oBasketSummaryCache = new stdClass();
        $oBasket->oBasketSummaryCache->iArticleCount = 5;
        $oBasket->oBasketSummaryCache->dArticleDiscountablePrice = 10;
        oxDb::getDb()->Execute("insert into oxobject2discount (OXID, OXDISCOUNTID, OXOBJECTID, OXTYPE) VALUES('testIsForBasket','" . $testDiscId . "','nothing','oxarticles')");

        $oDiscount->oxdiscount__oxamount = new oxField(1, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBasket($oBasket));
        oxDb::getDb()->Execute("delete from oxobject2discount where oxid = 'testIsForBasket'");
        $this->assertTrue($oDiscount->isForBasket($oBasket));

        $oDiscount->oxdiscount__oxprice = new oxField(11, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBasket($oBasket));
        $oDiscount->oxdiscount__oxprice = new oxField(1, oxField::T_RAW);

        $oDiscount->oxdiscount__oxamount = new oxField(6, oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBasket($oBasket));
    }

    public function testIsForBasketItemPriceCurrencyChecks()
    {
        $oSummary = new stdClass();
        $oSummary->iArticleCount = 5;
        $oSummary->dArticleDiscountablePrice = 101;

        $oCurr = new stdclass;
        $oCurr->rate = 5;

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array("getBasketSummary", "getBasketCurrency"));
        $oBasket->expects($this->atLeastOnce())->method('getBasketSummary')->will($this->returnValue($oSummary));
        $oBasket->expects($this->atLeastOnce())->method('getBasketCurrency')->will($this->returnValue($oCurr));

        $oDiscount = oxNew('oxDiscount');
        $oDiscount->oxdiscount__oxprice = new oxField(100);
        $oDiscount->oxdiscount__oxpriceto = new oxField(150);

        $this->assertFalse($oDiscount->isForBasket($oBasket));

        $oCurr = new stdclass;
        $oCurr->rate = 1;

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array("getBasketSummary", "getBasketCurrency"));
        $oBasket->expects($this->atLeastOnce())->method('getBasketSummary')->will($this->returnValue($oSummary));
        $oBasket->expects($this->atLeastOnce())->method('getBasketCurrency')->will($this->returnValue($oCurr));

        $oDiscount = oxNew('oxDiscount');
        $oDiscount->oxdiscount__oxprice = new oxField(100);
        $oDiscount->oxdiscount__oxpriceto = new oxField(150);

        $this->assertTrue($oDiscount->isForBasket($oBasket));
    }

    /**
     * Testing bundle checker
     */
    public function testIsForBundleItem()
    {
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->oxdiscount__oxaddsumtype = new oxField("test", oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBundleItem(null, null));
    }

    /**
     * Testing with simulated data
     */
    public function testIsForBundleItemWithData()
    {
        $testAid = 'xxx';
        $oArticle = oxNew('oxArticle');
        $oArticle->setId($testAid);

        $oDiscount = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, array('_checkForArticleCategories'));
        $oDiscount->expects($this->once())->method('_checkForArticleCategories')->with($this->isInstanceOf('\OxidEsales\EshopCommunity\Application\Model\Article'));
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('itm');
        $oDiscount->setId('testdid');

        // testing
        $oDiscount->isForBundleItem($oArticle);
    }

    /**
     * Testing basket bundle checker
     */
    // configuration check
    public function testIsForBundleBasket()
    {
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->oxdiscount__oxaddsumtype = new oxField("test", oxField::T_RAW);
        $this->assertFalse($oDiscount->isForBundleBasket(null));
    }

    // testing if further functionality is executed
    public function testIsForBundleBasketFncCheck()
    {
        $oBasket = oxNew('oxbasket');

        $oDiscount = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, array('isForBasket'));
        $oDiscount->expects($this->once())->method('isForBasket')->with($this->equalTo($oBasket));
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('itm', oxField::T_RAW);

        $oDiscount->isForBundleBasket($oBasket);
    }

    public function testGetAbsValue_abs()
    {
        $oDiscount = oxNew('oxdiscount');

        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField(50, oxField::T_RAW);

        $this->assertEquals(50, $oDiscount->getAbsValue(100));
    }

    public function testGetAbsValueAbsForAmount()
    {
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField(5, oxField::T_RAW);
        $this->assertEquals(10, $oDiscount->getAbsValue(100, 2));
    }

    public function testGetAbsValue_perc()
    {
        $oDiscount = oxNew('oxdiscount');

        $oDiscount->oxdiscount__oxaddsumtype = new oxField('%', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField(50, oxField::T_RAW);

        $this->assertEquals(50, $oDiscount->getAbsValue(100));
    }

    public function testGetBundleAmount()
    {
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->oxdiscount__oxitmamount = new oxField(10, oxField::T_RAW);
        $this->assertEquals($oDiscount->getBundleAmount(5), 10);

        $oDiscount->oxdiscount__oxitmmultiple = new oxField(3, oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(4, oxField::T_RAW);
        $this->assertEquals($oDiscount->getBundleAmount(9), 20);
    }

    /**
     * Testing simple voucher getter
     */
    public function testGetSimpleDiscount()
    {
        $sShopId = $this->getConfig()->getBaseShopId();
        $myDB = oxDb::getDb();
        $sQ = 'insert into oxdiscount ';
        $sQ .= '(oxid, oxshopid, oxactive, oxtitle, oxamount, oxamountto, oxprice, oxpriceto, oxaddsumtype, oxaddsum) values ';
        $sQ .= "('testdid', '$sShopId', '1', 'test for shop $sShopId', '0', '9999', '0', '9999', 'abs', '10') ";
        $myDB->Execute($sQ);
        // EE version changes

        $oDiscount = oxNew('oxDiscount');
        $oDiscount->load('testdid');
        $oDiscount->oxdiscount__oxactive = new oxField('1', oxField::T_RAW);

        $oSimpleDiscount = new stdClass();
        $oSimpleDiscount->sOXID = $oDiscount->getId();
        $oSimpleDiscount->sDiscount = $oDiscount->oxdiscount__oxtitle->value;
        $oSimpleDiscount->sType = $oDiscount->oxdiscount__oxaddsumtype->value;

        $this->assertEquals($oSimpleDiscount, $oDiscount->getSimpleDiscount());
    }

    public function testIsForBasketAmountIfnotisforbasket()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getBasePrice', 'getId'));
        $oArticle->expects($this->never())->method('getId')->will($this->returnValue('asd'));
        $oArticle->expects($this->never())->method('getBasePrice')->will($this->returnValue(2));

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array('getArticle'));
        $oBasketItem->expects($this->once())->method('getArticle')->will($this->returnValue($oArticle));

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getContents'));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array($oBasketItem)));

        $oDiscount = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, array('isForAmount', 'isForBasketItem', 'isForBundleItem'));
        $oDiscount->expects($this->once())->method('isForAmount')->with($this->equalTo(0))->will($this->returnValue(true));
        $oDiscount->expects($this->once())->method('isForBasketItem')->with($this->equalTo($oArticle))->will($this->returnValue(false));
        $oDiscount->expects($this->never())->method('isForBundleItem');

        $this->assertTrue($oDiscount->isForBasketAmount($oBasket));
    }

    public function testIsForBasketAmountIfNotIsForBundle()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getBasePrice', 'getId'));
        $oArticle->expects($this->never())->method('getId')->will($this->returnValue('asd'));
        $oArticle->expects($this->never())->method('getBasePrice')->will($this->returnValue(2));

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array('getArticle'));
        $oBasketItem->expects($this->once())->method('getArticle')->will($this->returnValue($oArticle));

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getContents'));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array($oBasketItem)));

        $oDiscount = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, array('isForAmount', 'isForBundleItem', 'isForBasketItem'));
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('itm', oxField::T_RAW);
        $oDiscount->expects($this->once())->method('isForAmount')->with($this->equalTo(0))->will($this->returnValue(true));
        $oDiscount->expects($this->once())->method('isForBundleItem')->with($this->equalTo($oArticle))->will($this->returnValue(false));
        $oDiscount->expects($this->never())->method('isForBasketItem');

        $this->assertTrue($oDiscount->isForBasketAmount($oBasket));
    }

    public function testIsForBasketAmountForPricedDiscountIfBundleType()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getPrice', 'getId'));
        $oArticle->expects($this->once())->method('getPrice')->will($this->returnValue(new oxprice(10)));

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array('getArticle', 'getAmount'));
        $oBasketItem->expects($this->once())->method('getArticle')->will($this->returnValue($oArticle));
        $oBasketItem->expects($this->once())->method('getAmount')->will($this->returnValue(5));

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getContents', 'getBasketSummary'));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array($oBasketItem)));

        $oDiscount = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, array('isForAmount', 'isForBundleItem', 'isForBasketItem'));

        $oDiscount->oxdiscount__oxaddsumtype = new oxField('itm', oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(5, oxField::T_RAW);

        $oDiscount->expects($this->once())->method('isForAmount')->with($this->equalTo(50))->will($this->returnValue(true));
        $oDiscount->expects($this->once())->method('isForBundleItem')->with($this->equalTo($oArticle))->will($this->returnValue(true));
        $oDiscount->expects($this->never())->method('isForBasketItem');

        $this->assertTrue($oDiscount->isForBasketAmount($oBasket));
    }

    public function testIsForBasketAmountForAmountDiscountIfSimpleDiscountType()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getBasePrice', 'getId', 'getPrice'));
        $oArticle->expects($this->never())->method('getPrice');

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array('getArticle', 'getAmount'));
        $oBasketItem->expects($this->once())->method('getArticle')->will($this->returnValue($oArticle));
        $oBasketItem->expects($this->once())->method('getAmount')->will($this->returnValue(5));

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getContents', 'getBasketSummary'));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array('asd' => $oBasketItem)));

        $oDiscount = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, array('isForAmount', 'isForBasketItem'));

        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxamount = new oxField(5, oxField::T_RAW);

        $oDiscount->expects($this->once())->method('isForAmount')->with($this->equalTo(5))->will($this->returnValue(true));
        $oDiscount->expects($this->once())->method('isForBasketItem')->with($this->equalTo($oArticle))->will($this->returnValue(true));

        $this->assertTrue($oDiscount->isForBasketAmount($oBasket));
    }

    /**
     * Tests #1571, case: discount should not be applied for different currency amount range
     *
     */
    public function testIsForBasketAmountForDifferentCurrency()
    {

        //setting default currency to another one
        $this->getConfig()->setActShopCurrency(1);

        //getting a real demo product becaues with mock it is not easy to make sure the price set is NOT in active currency
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');
        //$oArticle = $this->getMock( 'oxarticle', array( 'getPrice', 'getId' ) );
        //$oArticle->expects( $this->any() )->method( 'getPrice' )->will( $this->returnValue( new oxprice( 150 ) ));

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array('getArticle', 'getAmount'));
        $oBasketItem->expects($this->once())->method('getArticle')->will($this->returnValue($oArticle));
        $oBasketItem->expects($this->once())->method('getAmount')->will($this->returnValue(1));

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getContents', 'getBasketSummary'));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array($oBasketItem)));

        $oDiscount = $this->getMock(\OxidEsales\Eshop\Application\Model\Discount::class, array('isForAmount', 'isForBasketItem'));
        $oDiscount->expects($this->once())->method("isForBasketItem")->will($this->returnValue(true));

        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxprice = new oxField(5, oxField::T_RAW);

        $oDiscount->expects($this->once())->method('isForAmount')->will($this->returnValue(true));

        //making sure article price is calculated in pounds, and is not equal to eur34
        $this->assertEquals(29.12, $oArticle->getPrice()->getBruttoPrice());

        $oDiscount->isForBasketAmount($oBasket);
    }

    /**
     * Testing oxDiscount::_getProductCheckQuery()
     *
     * @return null
     */
    public function testGetProductCheckQuery()
    {
        $oProduct1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array("getParentId", "getProductId"));
        $oProduct1->expects($this->once())->method('getParentId')->will($this->returnValue("ProductParentId"));
        $oProduct1->expects($this->once())->method('getProductId')->will($this->returnValue("ProductId"));

        $oProduct2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array("getParentId", "getProductId"));
        $oProduct2->expects($this->once())->method('getParentId')->will($this->returnValue(false));
        $oProduct2->expects($this->once())->method('getProductId')->will($this->returnValue("ProductId"));

        $sQ1 = " and ( oxobjectid = 'ProductId' or oxobjectid = 'ProductParentId' )";
        $sQ2 = " and oxobjectid = 'ProductId'";

        $oDiscount = oxNew('oxDiscount');
        $this->assertEquals($sQ1, $oDiscount->UNITgetProductCheckQuery($oProduct1));
        $this->assertEquals($sQ2, $oDiscount->UNITgetProductCheckQuery($oProduct2));
    }

    /**
     * Test case for #0002599: itm discount (product) is not given for variant-product
     *
     * When there is itm discount created, which is applied only for some particular
     * products, which have variants, the discount is not applied for Variant products,
     * when these are added to basket.
     * The problem is that only Parent-article is assigned to discount the variant-products
     * are not valuated in this case, and discount is applied strictly for Parent-product only.
     * This discount should be applied for Variant-products, when only Parent product is
     * assigned to discount.
     *
     * @return null
     */
    public function testForCase2599()
    {
        // creating test discount
        $this->getConfig()->setConfigParam("blVariantParentBuyable", 1);
        $sDiscountId = '_' . uniqid(rand());

        // inserting test discount
        $query = "insert into oxdiscount ( oxid, oxshopid, oxactive, oxtitle, oxamount, oxamountto, oxpriceto, oxaddsumtype, oxaddsum )
               values ( '{$sDiscountId}', '" . $this->getConfig()->getBaseShopId() . "', '1', 'Test', '5', '10', '0', 'itm', '10' )";
        $this->addToDatabase($query, 'oxdiscount');

        // assigning test discount
        $query = "insert into oxobject2discount ( oxid, oxdiscountid, oxobjectid, oxtype )
               values
               ( '_test" . uniqid(rand(), true) . ".', '{$sDiscountId}', 'product1', 'oxarticles' ),
               ( '_test" . uniqid(rand(), true) . ".', '{$sDiscountId}', 'product2', 'oxarticles' ),
               ( '_test" . uniqid(rand(), true) . ".', '{$sDiscountId}', 'product3', 'oxarticles' ) ";
        oxDb::getDb()->Execute($query);

        $oParentProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array("getParentId", "getProductId"));
        $oParentProduct->expects($this->once())->method('getParentId')->will($this->returnValue(false));
        $oParentProduct->expects($this->once())->method('getProductId')->will($this->returnValue("product1"));

        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array("getParentId", "getProductId"));
        $oProduct->expects($this->once())->method('getParentId')->will($this->returnValue("product1"));
        $oProduct->expects($this->once())->method('getProductId')->will($this->returnValue("product4"));

        $oUnrelatedProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array("getParentId", "getProductId"));
        $oUnrelatedProduct->expects($this->once())->method('getParentId')->will($this->returnValue(false));
        $oUnrelatedProduct->expects($this->once())->method('getProductId')->will($this->returnValue("UnrelatedProductId"));

        // testing
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->load($sDiscountId);
        $this->assertTrue($oDiscount->isForBundleItem($oParentProduct));
        $this->assertTrue($oDiscount->isForBundleItem($oProduct));
        $this->assertFalse($oDiscount->isForBundleItem($oUnrelatedProduct));
    }

    public function testGetNextOxsortReturnsIncrementedValue()
    {
        $shopId = 1;
        $query = 'SELECT MAX(oxsort) FROM oxdiscount';
        $currentValue = oxDb::getDb()->getOne($query, [$shopId]);

        $oDiscount = oxNew('oxDiscount');
        $incrementedValue = $oDiscount->getNextOxsort($shopId);

        $this->assertGreaterThan($currentValue, $incrementedValue);
    }
}
