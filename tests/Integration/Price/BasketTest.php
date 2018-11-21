<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Price;

use oxDb;
use oxOrderArticle;
use oxRegistry;

require_once __DIR__. '/BaseTestCase.php';

/**
 * Basket price calculation test
 * Check:
 * - Article unit & total price
 * - Discount amounts
 * - Vat amounts
 * - Additional fees (wrapping, payment, delivery)
 * - Vouchers
 * - Totals (grand, netto, brutto)
 */
class BasketTest extends BaseTestCase
{
    /** @var array Test case directory array */
    private $testCaseDirectories = array(
        "testcases/basket",
        // "testcases/databomb",
    );

    /** @var string Specified test cases (optional) */
    private $testCases = array(
        //"testCase.php",
    );

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->reset();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $this->addTableForCleanup('oxobject2category');
        parent::tearDown();
    }

    /**
     * Resets db tables, required configs
     */
    protected function reset()
    {
        $database = oxDb::getDb();
        $config = oxRegistry::getConfig();
        $database->execute("TRUNCATE oxarticles");
        $database->execute("TRUNCATE oxcategories");
        $database->execute("TRUNCATE oxdiscount");
        $database->execute("TRUNCATE oxobject2discount");
        $database->execute("TRUNCATE oxwrapping");
        $database->execute("TRUNCATE oxdelivery");
        $database->execute("TRUNCATE oxdel2delset");
        $database->execute("TRUNCATE oxobject2payment");
        $database->execute("TRUNCATE oxvouchers");
        $database->execute("TRUNCATE oxvoucherseries");
        $database->execute("TRUNCATE oxobject2delivery");
        $database->execute("TRUNCATE oxobject2category");
        $database->execute("TRUNCATE oxdeliveryset");
        $database->execute("TRUNCATE oxuser");
        $database->execute("TRUNCATE oxprice2article");
        $config->setConfigParam("blShowVATForDelivery", true);
        $config->setConfigParam("blShowVATForPayCharge", true);
    }

    /**
     * Basket startup data and expected calculations results
     *
     * @return array
     */
    public function providerBasketCalculation()
    {
        return $this->getTestCases($this->testCaseDirectories, $this->testCases);
    }

    /**
     * Tests special basket calculations
     *
     * @dataProvider providerBasketCalculation
     *
     * @param array $testCase
     */
    public function testBasketCalculation($testCase)
    {
        if ($testCase['skipped'] == 1) {
            $this->markTestSkipped("testcase is skipped");
        }
        // gathering data arrays
        $expected = $testCase['expected'];

        //if not finished testing data skip test
        if (empty($expected)) {
            $this->markTestSkipped("skipping test case due invalid data provided");
        }

        // load calculated basket from provided data
        $basketConstruct = new BasketConstruct();
        $basket = $basketConstruct->calculateBasket($testCase);

        // check basket item list
        $expectedArticles = $expected['articles'];
        $basketItemList = $basket->getContents();

        $this->assertEquals(count($expectedArticles), count($basketItemList), "Expected basket articles amount doesn't match actual");

        if ($basketItemList) {
            foreach ($basketItemList as $key => $basketItem) {
                /** @var oxOrderArticle $basketItem */
                $articleId = $basketItem->getArticle()->getID();
                $this->assertEquals($expectedArticles[$articleId][0], $basketItem->getFUnitPrice(), "Unit price of article id {$articleId}");
                $this->assertEquals($expectedArticles[$articleId][1], $basketItem->getFTotalPrice(), "Total price of article id {$articleId}");
            }
        }

        // Total discounts
        $expectedDiscounts = $expected['totals']['discounts'];
        $expectedDiscountCount = (is_array($expectedDiscounts)) ? count($expectedDiscounts) : 0;
        $productDiscounts = $basket->getDiscounts();
        $productDiscountsCount = (is_array($productDiscounts)) ? count($productDiscounts) : 0;
        $this->assertEquals($expectedDiscountCount, $productDiscountsCount, "Expected basket discount amount doesn't match actual");
        if (!empty($expectedDiscounts)) {
            foreach ($productDiscounts as $discount) {
                $this->assertEquals($expectedDiscounts[$discount->sOXID], $discount->fDiscount, "Total discount of {$discount->sOXID}");
            }
        }

        // Total vats
        $expectedVats = $expected['totals']['vats'];
        $expectedVatsCount = (is_array($expectedVats)) ? count($expectedVats) : 0;
        $productVats = $basket->getProductVats();
        $productVatsCount = (is_array($productVats)) ? count($productVats) : 0;
        $this->assertEquals($expectedVatsCount, $productVatsCount, "Expected basket different vat amount doesn't match actual");
        if (!empty($expectedVats)) {
            foreach ($productVats as $percent => $sum) {
                $this->assertEquals($expectedVats[$percent], $sum, "Total Vat of {$percent}%");
            }
        }

        // Wrapping costs
        $expectedWrappings = $expected['totals']['wrapping'];
        if (!empty($expectedWrappings)) {
            $this->assertEquals(
                $expectedWrappings['brutto'],
                $basket->getFWrappingCosts(),
                "Total wrappings brutto price"
            );
            $this->assertEquals(
                $expectedWrappings['netto'],
                $basket->getWrappCostNet(),
                "Total wrappings netto price"
            );
            $this->assertEquals(
                $expectedWrappings['vat'],
                $basket->getWrappCostVat(),
                "Total wrappings vat price"
            );
        }

        // Giftcard costs
        $expectedCards = $expected['totals']['giftcard'];
        if (!empty($expectedCards)) {
            $this->assertEquals(
                $expectedCards['brutto'],
                $basket->getFGiftCardCosts(),
                "Total giftcard brutto price"
            );
            $this->assertEquals(
                $expectedCards['netto'],
                $basket->getGiftCardCostNet(),
                "Total giftcard netto price"
            );
            $this->assertEquals(
                $expectedCards['vat'],
                $basket->getGiftCardCostVat(),
                "Total giftcard vat price"
            );
        }

        // Delivery costs
        $expectedDeliveryCosts = $expected['totals']['delivery'];
        if (!empty($expectedDeliveryCosts)) {
            $this->assertEquals(
                $expectedDeliveryCosts['brutto'],
                number_format(round($basket->getDeliveryCosts(), 2), 2, ',', '.'),
                "Delivery total brutto price"
            );
            $this->assertEquals(
                $expectedDeliveryCosts['netto'],
                $basket->getDelCostNet(),
                "Delivery total netto price"
            );
            $this->assertEquals(
                $expectedDeliveryCosts['vat'],
                $basket->getDelCostVat(),
                "Delivery total vat price"
            );
        }

        // Payment costs
        $expectedPayments = $expected['totals']['payment'];
        if (!empty($expectedPayments)) {
            $this->assertEquals(
                $expectedPayments['brutto'],
                number_format(round($basket->getPaymentCosts(), 2), 2, ',', '.'),
                "Payment total brutto price"
            );
            $this->assertEquals(
                $expectedPayments['netto'],
                $basket->getPayCostNet(),
                "Payment total netto price"
            );
            $this->assertEquals(
                $expectedPayments['vat'],
                $basket->getPayCostVat(),
                "Payment total vat price"
            );
        }

        // Vouchers
        $expectedVouchers = $expected['totals']['voucher'];
        if (!empty($expectedVouchers)) {
            $this->assertEquals(
                $expectedVouchers['brutto'],
                number_format(round($basket->getVoucherDiscValue(), 2), 2, ',', '.'),
                "Voucher total discount brutto"
            );
        }

        // Total netto & brutto, grand total
        $this->assertEquals($expected['totals']['totalNetto'], $basket->getProductsNetPrice(), "Total Netto");
        $this->assertEquals($expected['totals']['totalBrutto'], $basket->getFProductsPrice(), "Total Brutto");
        $this->assertEquals($expected['totals']['grandTotal'], $basket->getFPrice(), "Grand Total");
    }
}
