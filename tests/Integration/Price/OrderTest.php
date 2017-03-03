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
namespace Integration\Price;

use oxDb;
use oxField;
use oxOrder;
use oxOrderArticle;
use oxRegistry;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

require_once __DIR__. '/BasketConstruct.php';

/**
 * Final order calculation test
 * Known action cycle to test:
 * 1.) Save basket
 * 2.) Proceed order
 * 3.) Change order in different ways
 *     a.) By articles quantity
 *     b.) By discount amount
 *     c.) By adding / removing articles
 * 4.) Recalculate
 */
/**
 * Class OrderTest
 *
 * @group slow-tests
 *
 * @package Integration\Price
 */
class OrderTest extends BaseTestCase
{
    /** @var string Test case directory */
    private $testCaseDirectory = "testcases/order";

    /** @var array Specified test cases (optional) */
    private $testCases = array(
        // "testCase.php"
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
     * Resets db tables, required configs
     */
    protected function reset()
    {
        $database = oxDb::getDb();
        $database->execute("TRUNCATE oxarticles");
        $database->execute("TRUNCATE oxcategories");
        $database->execute("TRUNCATE oxdiscount");
        $database->execute("TRUNCATE oxobject2discount");
        $database->execute("TRUNCATE oxwrapping");
        $database->execute("TRUNCATE oxdelivery");
        $database->execute("TRUNCATE oxdel2delset");
        $database->execute("TRUNCATE oxobject2payment");
        $database->execute("TRUNCATE oxobject2category");
        $database->execute("TRUNCATE oxvouchers");
        $database->execute("TRUNCATE oxvoucherseries");
        $database->execute("TRUNCATE oxuser");
        $database->execute("TRUNCATE oxdeliveryset");
        $database->execute("TRUNCATE oxpayments");
        $database->execute("TRUNCATE oxprice2article");
    }

    /**
     * Order startup data and expected calculations results
     *
     * @return array
     */
    public function providerOrderCalculation()
    {
        return $this->getTestCases($this->testCaseDirectory, $this->testCases);
    }

    /**
     * Tests order calculations
     *
     * @dataProvider providerOrderCalculation
     *
     * @param array $testCase
     */
    public function testOrderCalculation($testCase)
    {
        if ($testCase['skipped'] == 1) {
            $this->markTestSkipped("testcase is skipped");
        }
        // expectations
        $expected = $testCase['expected'];
        // actions
        $actions = $testCase['actions'];

        // load calculated basket from provided data
        $basketConstruct = new BasketConstruct();
        $basket = $basketConstruct->calculateBasket($testCase);

        $user = $basket->getBasketUser();

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        /** @var oxOrder|MockObject $order */
        $order = $this->getMock('oxOrder', array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery'));
        $order->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $order->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $order->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));

        // if basket has products
        if ($basket->getProductsCount()) {
            $success = $order->finalizeOrder($basket, $user);
            $this->assertEquals(0, $success);
        }

        // check order totals
        $this->checkTotals($expected, 1, $order);
        if (!empty($actions)) {
            foreach ($actions as $function => $parameters) {
                $this->$function($parameters, $order);
            }
            oxRegistry::set("oxDeliveryList", null);
            $order->recalculateOrder();
            $this->checkTotals($expected, 2, $order);
        }
    }

    /**
     * Check totals of saved (recalculated) order
     *
     * @param array   $expected
     * @param int     $approach number of order recalculate events starting at 1
     * @param oxOrder $order
     */
    protected function checkTotals($expected, $approach, $order)
    {
        $expectedTotals = $expected[$approach]['totals'];
        $articles = $expected[$approach]['articles'];
        $isNettoMode = $order->isNettoMode();
        $orderArticles = $order->getOrderArticles();

        foreach ($orderArticles as $article) {
            /** @var oxOrderArticle $article */
            $articleId = $article->oxorderarticles__oxartid->value;
            if ($isNettoMode) {
                $unitPrice = $article->getNetPriceFormated();
                $totalPrice = $article->getTotalNetPriceFormated();
            } else {
                $unitPrice = $article->getBrutPriceFormated();
                $totalPrice = $article->getTotalBrutPriceFormated();
            }
            $this->assertEquals($articles[$articleId][0], $unitPrice, "#{$approach} Unit price of order art no #{$articleId}");
            $this->assertEquals($articles[$articleId][1], $totalPrice, "#{$approach} Total price of order art no #{$articleId}");
        }

        $productVats = $order->getProductVats(true);

        $this->assertEquals($expectedTotals['totalNetto'], $order->getFormattedTotalNetSum(), "Product Net Price #$approach");
        $this->assertEquals($expectedTotals['discount'], $order->getFormattedDiscount(), "Discount #$approach");

        if ($productVats) {
            foreach ($productVats as $vat => $vatPrice) {
                $this->assertEquals($expectedTotals['vats'][$vat], $vatPrice, "Vat %{$vat} total cost #$approach");
            }
        }

        $this->assertEquals($expectedTotals['totalBrutto'], $order->getFormattedTotalBrutSum(), "Product Gross Price #$approach");

        if ($expectedTotals['voucher']) {
            $this->assertEquals($expectedTotals['voucher']['brutto'], $order->getFormattedTotalVouchers(), "Voucher costs #$approach");
        }

        if ($expectedTotals['delivery']) {
            $this->assertEquals($expectedTotals['delivery']['brutto'], $order->getFormattedeliveryCost(), "Shipping costs #$approach");
        }

        if ($expectedTotals['wrapping']) {
            $this->assertEquals($expectedTotals['wrapping']['brutto'], $order->getFormattedWrapCost(), "Wrapping costs #$approach");
        }

        if ($expectedTotals['giftcard']) {
            $this->assertEquals($expectedTotals['giftcard']['brutto'], $order->getFormattedGiftCardCost(), "Giftcard costs #$approach");
        }

        if ($expectedTotals['payment']) {
            $this->assertEquals($expectedTotals['payment']['brutto'], $order->getFormattedPayCost(), "Charge Payment Method #$approach");
        }
        $this->assertEquals($expectedTotals['grandTotal'], $order->getFormattedTotalOrderSum(), "Sum total #$approach");
    }

    /* --- Expected functions for changing saved order --- */

    /**
     * Change configs
     *
     * @param array $configOptions
     */
    protected function _changeConfigs($configOptions)
    {
        $config = oxRegistry::getConfig();
        if (!empty($configOptions)) {
            foreach ($configOptions as $sKey => $sValue) {
                $config->setConfigParam($sKey, $sValue);
            }
        }
    }

    /**
     * Add articles
     *
     * @param array  $articlesData new articles to add
     * @param object $order
     */
    protected function _addArticles($articlesData, $order)
    {
        $basketConstruct = new BasketConstruct();
        $articles = $basketConstruct->getArticles($articlesData);
        foreach ($articles as $article) {
            $product = oxNew('oxArticle');
            $product->load($article['id']);
            $amount = $article['amount'];
            $orderArticle = oxNew('oxOrderArticle');
            $orderArticle->oxorderarticles__oxartid = new oxField($product->getId());
            $orderArticle->oxorderarticles__oxartnum = new oxField($product->oxarticles__oxartnum->value);
            $orderArticle->oxorderarticles__oxamount = new oxField($amount);
            $orderArticle->oxorderarticles__oxselvariant = new oxField(oxRegistry::getConfig()->getRequestParameter('sel'));
            $order->recalculateOrder(array($orderArticle));
        }
    }

    /**
     * Removes articles
     *
     * @param array  $articleIds article id's to remove
     * @param object $order
     */
    protected function _removeArticles($articleIds, $order)
    {
        $articleCount = count($articleIds);
        $orderArticles = $order->getOrderArticles();
        foreach ($orderArticles as $orderArticle) {
            /** @var oxOrderArticle $orderArticle */
            for ($i = 0; $i < $articleCount; $i++) {
                if ($orderArticle->oxorderarticles__oxartid->value == $articleIds[$i]) {
                    $orderArticle->delete();
                }
            }
        }
    }

    /**
     * Change articles
     *
     * @param array  $articleAmounts
     * @param object $order
     */
    protected function _changeArticles($articleAmounts, $order)
    {
        $articlesCount = count($articleAmounts);
        $orderArticles = $order->getOrderArticles();
        foreach ($orderArticles as $orderArticle) {
            /** @var oxOrderArticle $orderArticle */
            for ($i = 0; $i < $articlesCount; $i++) {
                if ($orderArticle->oxorderarticles__oxartid->value == $articleAmounts[$i]['oxid']) {
                    $orderArticle->setNewAmount($articleAmounts[$i]['amount']);
                }
            }
        }
    }
}
