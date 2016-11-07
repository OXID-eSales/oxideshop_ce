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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Integration\Admin;

use oxBasket;
use oxDb;
use oxField;
use oxOrder;
use oxRegistry;
use oxUtilsObject;

class OrderRecalculationTest extends \OxidTestCase
{
    /**
     * Make a copy of Stewart+Brown Shirt Kisser Fish parent and variant L violet for testing
     */
    const SOURCE_ARTICLE_ID = '6b6d966c899dd9977e88f842f67eb751';
    const SOURCE_ARTICLE_PARENT_ID = '6b6099c305f591cb39d4314e9a823fc1';

    const TEST_ARTICLE_PRICE = 11.90;

    const TESTVOUCHER_ID_PREFIX = 'testvoucher_relative_';

    /**
     * Generated oxids for test article, user, order, discount and vouchers
     * @var string
     */
    private $testArticleId = null;
    private $testArticleParentId = null;
    private $testOrderId = null;
    private $testUserId = null;
    private $voucherSeriesId = null;
    private $discountId = null;

    /**
     * Store original shop configuration and session values.
     * @var mixed
     */
    private $originalSessionChallenge = null;

    /**
     * Fixture setUp.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->insertArticle();
        $this->insertUser();

        $this->originalSessionChallenge = oxRegistry::getSession()->getVariable('sess_challenge');
    }

    /*
    * Fixture tearDown.
    */
    protected function tearDown()
    {
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxorderarticles');
        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxuserpayments');
        $this->cleanUpTable('oxuserbaskets');
        $this->cleanUpTable('oxuserbasketitems');
        $this->cleanUpTable('oxobject2delivery');
        $this->cleanUpTable('oxvouchers');
        $this->cleanUpTable('oxvoucherseries');
        $this->cleanUpTable('oxdiscount');
        $this->cleanUpTable('oxobject2discount');

        oxRegistry::getSession()->delBasket();
        oxRegistry::getSession()->deleteVariable('_newitem');
        oxRegistry::getSession()->setVariable('sess_challenge', $this->originalSessionChallenge);
        $_POST = array();

        parent::tearDown();
    }

    /**
     * Data provier for testPlaceOrderWithoutVouchersTriggerRecalculateOrderMain
     * @return array
     */
    public function providerPlaceOrderWithoutVouchersTriggerRecalculateOrderMain()
    {
        $data = array();

        $editValues                = array(
            'oxorder__oxordernr'   => '123',
            'oxorder__oxbillnr'    => '321',
            'oxorder__oxdiscount'  => '0',
            'oxorder__oxpaid'      => date('Y-m-d H:i:s'),
            'oxorder__oxtrackcode' => 'tracking_code',
            'oxorder__oxdelcost'   => '0'
        );
        $data['no_recalculate'][0] = $editValues;
        $data['no_recalculate'][1] = $editValues['oxorder__oxdiscount'];

        $editValues = array(
            'oxorder__oxordernr'   => '123',
            'oxorder__oxbillnr'    => '321',
            'oxorder__oxdiscount'  => '10',
            'oxorder__oxpaid'      => date('Y-m-d H:i:s'),
            'oxorder__oxtrackcode' => 'tracking_code',
            'oxorder__oxdelcost'   => '0'
        );

        $data['do_recalculate'][0] = $editValues;
        $data['do_recalculate'][1] = $editValues['oxorder__oxdiscount'];

        return $data;
    }

    /**
     * Place order and simulate clicking admin->order_main.
     * Test case: No vouchers applied.
     * For datasaet 'no_recalculate' order_main-> save does not trigger order recalculation.
     * For dataset  'do_recalculate' recalculation is triggered.
     *
     * @dataProvider providerPlaceOrderWithoutVouchersTriggerRecalculateOrderMain
     */
    public function testPlaceOrderWithoutVouchersTriggerRecalculateOrderMain($editValues, $expectedDiscount)
    {
        $defaultVat = oxRegistry::getSession()->getConfig()->getConfigParam('dDefaultVAT');
        $buyAmount = 10;

        $expectedOrderTotalBruttoSum = $buyAmount * self::TEST_ARTICLE_PRICE;
        $expectedOrderTotalSum = $expectedOrderTotalBruttoSum;
        $expectedOrderTotalNettoSum = $expectedOrderTotalSum * 100.0 / (100.0 + $defaultVat);
        $expectedVoucherDiscount = 0.0;

        $order = $this->placeOrder($buyAmount);

        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals(0.0, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);

        //order is finished, now see what happens in admin when clicking on tab main
        oxRegistry::getSession()->deleteVariable('sess_challenge');
        $orderMain = $this->getMock('order_main', array('getEditObjectId'));
        $orderMain->expects($this->any())->method('getEditObjectId')->will($this->returnValue($this->testOrderId));
        $orderMain->setAdminMode(true);
        $orderMain->render();

        $order = oxNew('oxOrder');
        $order->load($this->testOrderId);
        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals(0.0, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);
        $this->assertEquals('0000-00-00 00:00:00', $order->oxorder__oxpaid->value);

        //simulate changes in admin order_main
        $editValues['oxorder__oxid'] = $this->testOrderId;
        $_POST = array('editval' => $editValues, 'setDelSet' => 'oxidstandard');
        $orderMain->save();

        //NOTE: we do not see that the order was recalculated here and no way to mock oxorder without more changes in oder_main.php
        //So this is only implicitly tested.
        $expectedOrderTotalSum = round($expectedOrderTotalBruttoSum - $expectedDiscount, 2);
        $expectedOrderTotalNettoSum = round($expectedOrderTotalSum * 100.0 / (100.0 + $defaultVat), 2);

        $order = oxNew('oxOrder');
        $order->load($this->testOrderId);
        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals($expectedDiscount, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);
        $this->assertEquals($editValues['oxorder__oxpaid'], $order->oxorder__oxpaid->value);
    }

    /**
     * Place order and simulate clicking admin->order_main.
     * Test case: vouchers applied. (20% off each)
     */
    public function testPlaceOrderWithPercentageVouchersSaveOrderMain()
    {
        //relative discount, 20% off on each voucher
        $this->createVouchers();

        $defaultVat = oxRegistry::getSession()->getConfig()->getConfigParam('dDefaultVAT');
        $buyAmount = 10;
        $payDate = date('Y-m-d H:i:s');

        $expectedOrderTotalBruttoSum = $buyAmount * self::TEST_ARTICLE_PRICE; //119.0
        $expectedOrderTotalSum       = $buyAmount * self::TEST_ARTICLE_PRICE * 0.8 * 0.8; //two 20% vouchers applied 76.16
        $expectedVoucherDiscount     = $buyAmount * self::TEST_ARTICLE_PRICE * (1.0 -0.8*0.8); //42.84
        $expectedOrderTotalNettoSum  = $expectedOrderTotalSum * 100.0 / (100.0 + $defaultVat); //64.0
        $expectedDiscount = 0.0;

        $vouchers = array(self::TESTVOUCHER_ID_PREFIX . '1',
                          self::TESTVOUCHER_ID_PREFIX . '2');
        $order = $this->placeOrder($buyAmount, $vouchers);

        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals($expectedDiscount, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);

        //order is finished, now see what happens in admin when clicking on tab main
        oxRegistry::getSession()->deleteVariable('sess_challenge');
        $orderMain = $this->getMock('order_main', array('getEditObjectId'));
        $orderMain->expects($this->any())->method('getEditObjectId')->will($this->returnValue($this->testOrderId));
        $orderMain->setAdminMode(true);
        $orderMain->render();

        $order = oxNew('oxOrder');
        $order->load($this->testOrderId);
        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals($expectedDiscount, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);
        $this->assertEquals('0000-00-00 00:00:00', $order->oxorder__oxpaid->value);

        //simulate oxpaid date change in admin
        $editValues = array(
            'oxorder__oxid'        => $this->testOrderId,
            'oxorder__oxordernr'   => $order->oxorder__oxordernr->value,
            'oxorder__oxbillnr'    => $order->oxorder__oxbillnr->value,
            'oxorder__oxdiscount'  => '0',
            'oxorder__oxpaid'      => $payDate,
            'oxorder__oxtrackcode' => '',
            'oxorder__oxdelcost'   => '0'
        );
        $_POST = array('editval' => $editValues);
        $orderMain->save();

        //NOTE: we do not see that the order was recalculated here so this is only implicitly tested.
        //In case of order recalculation we'd get wrong VAT results when vouchers are applied.
        $order = oxNew('oxOrder');
        $order->load($this->testOrderId);
        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals($expectedDiscount, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);
        $this->assertEquals($payDate, $order->oxorder__oxpaid->value);

    }

    /**
     * Place order and simulate clicking admin->order_main.
     * Test case: vouchers applied. (20% off each) and purchase amount 20% off discount
     */
    public function testPlaceOrderWithPercentageVouchersPlusDiscountSaveOrderMain()
    {
        //relative discount, 20% off on each voucher
        $this->createVouchers();
        //discount of 20% when buying 10 or more articles T-666
        $this->createDiscount(); //is summed up with the voucherDiscount

        $defaultVat = oxRegistry::getSession()->getConfig()->getConfigParam('dDefaultVAT');
        $buyAmount = 10;
        $payDate = date('Y-m-d H:i:s');

        //119.0 and 20% off -> 95.20
        $expectedOrderTotalBruttoSum = round($buyAmount * self::TEST_ARTICLE_PRICE * 0.8,2);
        //two 20% vouchers applied on 95.20 -> 60.928
        $expectedOrderTotalSum       = round($buyAmount * self::TEST_ARTICLE_PRICE * 0.8 * 0.8 * 0.8,2);
        //total discount -> 95.20 - 60.928 = 34.272
        $expectedVoucherDiscount     = round($buyAmount * self::TEST_ARTICLE_PRICE * 0.8 * (1.0 - 0.8*0.8),2);
        //netto sum 100 * 0.8 * 0.8 * 0.8 = 51.20
        $expectedOrderTotalNettoSum  = round($expectedOrderTotalSum * 100.0 / (100.0 + $defaultVat),2);
        $expectedDiscount = 0.0;

        $vouchers = array(self::TESTVOUCHER_ID_PREFIX . '1',
                          self::TESTVOUCHER_ID_PREFIX . '2');
        $order = $this->placeOrder($buyAmount, $vouchers);

        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals($expectedDiscount, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);

        //deactivate vouchers, not relevant unless taken into account when recalculating  the order.
        //leave it in here anyway until voucher and discount issues are fixed.
        $this->endVouchers();
        $this->endDiscount();

        //order is finished, now see what happens in admin when clicking on tab main
        oxRegistry::getSession()->deleteVariable('sess_challenge');
        $orderMain = $this->getMock('order_main', array('getEditObjectId'));
        $orderMain->expects($this->any())->method('getEditObjectId')->will($this->returnValue($this->testOrderId));
        $orderMain->setAdminMode(true);
        $orderMain->render();

        $order = oxNew('oxOrder');
        $order->load($this->testOrderId);
        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals($expectedDiscount, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);
        $this->assertEquals('0000-00-00 00:00:00', $order->oxorder__oxpaid->value);

        //simulate oxpaid date change in admin
        $editValues = array(
            'oxorder__oxid'        => $this->testOrderId,
            'oxorder__oxordernr'   => $order->oxorder__oxordernr->value,
            'oxorder__oxbillnr'    => $order->oxorder__oxbillnr->value,
            'oxorder__oxdiscount'  => '0',
            'oxorder__oxpaid'      => $payDate,
            'oxorder__oxtrackcode' => '',
            'oxorder__oxdelcost'   => '0'
        );
        $_POST = array('editval' => $editValues);
        $orderMain->save();

        $order = oxNew('oxOrder');
        $order->load($this->testOrderId);
        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals($expectedDiscount, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);
        $this->assertEquals($payDate, $order->oxorder__oxpaid->value);

    }

    /**
     * Place order and simulate clicking admin->order_main.
     * Test case: vouchers applied. (20% off each) and purchase amount 20% off discount
     */
    public function testPlaceOrderWithPercentageVouchersPlusDiscountSaveOrderMainRecalculates()
    {
        $this->markTestSkipped('Fails due to https://bugs.oxid-esales.com/view.php?id=6161');

        //relative discount, 20% off on each voucher
        $this->createVouchers();
        //discount of 20% when buying 10 or more articles T-666
        $this->createDiscount(); //is summed up with the voucherDiscount

        $defaultVat = oxRegistry::getSession()->getConfig()->getConfigParam('dDefaultVAT');
        $buyAmount = 10;
        $payDate = date('Y-m-d H:i:s');

        //119.0 and 20% off -> 95.20
        $expectedOrderTotalBruttoSum = round($buyAmount * self::TEST_ARTICLE_PRICE * 0.8,2);
        //two 20% vouchers applied on 95.20 -> 60.928
        $expectedOrderTotalSum       = round($buyAmount * self::TEST_ARTICLE_PRICE * 0.8 * 0.8 * 0.8,2);
        //total discount -> 95.20 - 60.928 = 34.272
        $expectedVoucherDiscount     = round($buyAmount * self::TEST_ARTICLE_PRICE * 0.8 * (1.0 - 0.8*0.8),2);
        //netto sum 100 * 0.8 * 0.8 * 0.8 = 51.20
        $expectedOrderTotalNettoSum  = round($expectedOrderTotalSum * 100.0 / (100.0 + $defaultVat),2);
        $expectedDiscount = 0.0;

        $vouchers = array(self::TESTVOUCHER_ID_PREFIX . '1',
                          self::TESTVOUCHER_ID_PREFIX . '2');
        $order = $this->placeOrder($buyAmount, $vouchers);

        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals($expectedDiscount, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);

        //deactivate vouchers, not relevant unless taken into account when recalculating  the order.
        //leave it in here anyway until voucher and discount issues are fixed.
        $this->endVouchers();
        $this->endDiscount();

        //order is finished, now see what happens in admin when clicking on tab main
        oxRegistry::getSession()->deleteVariable('sess_challenge');
        $orderMain = $this->getMock('order_main', array('getEditObjectId'));
        $orderMain->expects($this->any())->method('getEditObjectId')->will($this->returnValue($this->testOrderId));
        $orderMain->setAdminMode(true);
        $orderMain->render();

        $order = oxNew('oxOrder');
        $order->load($this->testOrderId);
        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals($expectedDiscount, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);
        $this->assertEquals('0000-00-00 00:00:00', $order->oxorder__oxpaid->value);

        //simulate oxpaid date change in admin
        $editValues = array(
            'oxorder__oxid'        => $this->testOrderId,
            'oxorder__oxordernr'   => $order->oxorder__oxordernr->value,
            'oxorder__oxbillnr'    => $order->oxorder__oxbillnr->value,
            'oxorder__oxdiscount'  => '0',
            'oxorder__oxpaid'      => $payDate,
            'oxorder__oxtrackcode' => '',
            'oxorder__oxdelcost'   => '0'
        );
        $_POST = array('editval' => $editValues, 'setDelSet' => 'oxidnew');
        $orderMain->save();

        $order = oxNew('oxOrder');
        $order->load($this->testOrderId);
        $this->assertEquals($expectedOrderTotalBruttoSum, $order->oxorder__oxtotalbrutsum->value);
        $this->assertEquals($expectedOrderTotalNettoSum, $order->oxorder__oxtotalnetsum->value);
        $this->assertEquals($expectedOrderTotalSum, $order->oxorder__oxtotalordersum->value);
        $this->assertEquals($expectedDiscount, $order->oxorder__oxdiscount->value);
        $this->assertEquals($expectedVoucherDiscount, $order->oxorder__oxvoucherdiscount->value);
        $this->assertEquals($payDate, $order->oxorder__oxpaid->value);

    }

    /**
     * Make a copy of article and variant for testing.
     */
    private function insertArticle()
    {
        $this->testArticleId = substr_replace( oxUtilsObject::getInstance()->generateUId(), '_', 0, 1 );
        $this->testArticleParentId = substr_replace( oxUtilsObject::getInstance()->generateUId(), '_', 0, 1 );

        //copy from original article parent and variant
        $articleParent = oxNew('oxarticle');
        $articleParent->disableLazyLoading();
        $articleParent->load(self::SOURCE_ARTICLE_PARENT_ID);
        $articleParent->setId($this->testArticleParentId);
        $articleParent->oxarticles__oxartnum = new oxField('666-T', oxField::T_RAW);
        $articleParent->save();

        $article = oxNew('oxarticle');
        $article->disableLazyLoading();
        $article->load(self::SOURCE_ARTICLE_ID);
        $article->setId($this->testArticleId);
        $article->oxarticles__oxparentid = new oxField($this->testArticleParentId, oxField::T_RAW);
        $article->oxarticles__oxprice = new oxField(self::TEST_ARTICLE_PRICE, oxField::T_RAW);
        $article->oxarticles__oxartnum = new oxField('666-T-V', oxField::T_RAW);
        $article->oxarticles__oxactive = new oxField('1', oxField::T_RAW);
        $article->save();

    }

    /**
     * Create order object with test oxidid (leading underscore).
     *
     * @return object oxOrder
     */
    private function createOrder()
    {
        $order = $this->getMock('oxOrder', array('validateDeliveryAddress', '_sendOrderByEmail'));
        // sending order by email is always successful for tests
        $order->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(1));
        //mocked to circumvent delivery address change md5 check from requestParameter
        $order->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(0));

        $this->testOrderId = substr_replace( oxUtilsObject::getInstance()->generateUId(), '_', 0, 1 );
        $order->setId($this->testOrderId);

        return $order;
    }


    /**
     * @param oxBasket $basket
     */
    private function checkContents(\OxidEsales\EshopCommunity\Application\Model\Basket $basket, $expectedAmount)
    {
        $basketArticles = $basket->getBasketArticles();
        $keys = array_keys($basketArticles);
        $this->assertTrue(is_array($basketArticles));
        $this->assertEquals(1, count($basketArticles));
        $this->assertTrue(is_a($basketArticles[$keys[0]], 'OxidEsales\EshopCommunity\Application\Model\Article'));
        $this->assertEquals($this->testArticleId, $basketArticles[$keys[0]]->getId());

        $basketContents = $basket->getContents();
        $keys = array_keys($basketContents);
        $this->assertTrue(is_array($basketContents));
        $this->assertEquals(1, count($basketArticles));
        $this->assertTrue(is_a($basketContents[$keys[0]], 'OxidEsales\EshopCommunity\Application\Model\BasketItem'));

        $basketItem = $basketContents[$keys[0]];
        $this->assertEquals($this->testArticleId, $basketItem->getProductId());
        $this->assertEquals($expectedAmount, $basketItem->getAmount());
    }

    /**
     * insert test user
     */
    private function insertUser()
    {
        $this->testUserId = substr_replace(oxUtilsObject::getInstance()->generateUId(), '_', 0, 1);

        $user = oxNew('oxUser');
        $user->setId($this->testUserId);

        $user->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $user->oxuser__oxrights = new oxField('user', oxField::T_RAW);
        $user->oxuser__oxshopid = new oxField('1', oxField::T_RAW);
        $user->oxuser__oxusername = new oxField('testuser@oxideshop.dev', oxField::T_RAW);
        $user->oxuser__oxpassword = new oxField('c630e7f6dd47f9ad60ece4492468149bfed3da3429940181464baae99941d0ffa5562' .
                                                'aaecd01eab71c4d886e5467c5fc4dd24a45819e125501f030f61b624d7d',
            oxField::T_RAW); //password is asdfasdf
        $user->oxuser__oxpasssalt = new oxField('3ddda7c412dbd57325210968cd31ba86', oxField::T_RAW);
        $user->oxuser__oxcustnr = new oxField('666', oxField::T_RAW);
        $user->oxuser__oxfname = new oxField('Bla', oxField::T_RAW);
        $user->oxuser__oxlname = new oxField('Foo', oxField::T_RAW);
        $user->oxuser__oxstreet = new oxField('blafoostreet', oxField::T_RAW);
        $user->oxuser__oxstreetnr = new oxField('123', oxField::T_RAW);
        $user->oxuser__oxcity = new oxField('Hamburg', oxField::T_RAW);
        $user->oxuser__oxcountryid = new oxField('a7c40f631fc920687.20179984', oxField::T_RAW);
        $user->oxuser__oxzip = new oxField('22769', oxField::T_RAW);
        $user->oxuser__oxsal = new oxField('MR', oxField::T_RAW);
        $user->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $user->oxuser__oxboni = new oxField('1000', oxField::T_RAW);
        $user->oxuser__oxcreate = new oxField('2015-05-20 22:10:51', oxField::T_RAW);
        $user->oxuser__oxregister = new oxField('2015-05-20 22:10:51', oxField::T_RAW);
        $user->oxuser__oxboni = new oxField('1000', oxField::T_RAW);

        $user->save();

        $newId = substr_replace(oxUtilsObject::getInstance()->generateUId(), '_', 0, 1);
        $oDb = oxDb::getDb();
        $sQ = 'insert into `oxobject2delivery` (oxid, oxdeliveryid, oxobjectid, oxtype ) ' .
              " values ('$newId', 'oxidstandard', '" . $this->testUserId . "', 'oxdelsetu')";
        $oDb->execute($sQ);
    }

    /**
     * Create a voucher series with 4 vouchers.
     */
    private function createVouchers($discounttype = 'relative', $discount = 20, $prefix = self::TESTVOUCHER_ID_PREFIX)
    {
        $startDate = date('Y-m-d 00:00:00', time() - 86400);
        $endDate = date('Y-m-d 00:00:00', time() + 86400);

        $this->voucherSeriesId = substr_replace(oxUtilsObject::getInstance()->generateUId(), '_', 0, 1);
        $voucherSeries = oxNew('oxVoucherSerie');
        $voucherSeries->setId($this->voucherSeriesId);
        $voucherSeries->oxvoucherseries__oxshopid = new oxField('1', oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxserienr = new oxField('voucher_series_relative', oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxseriedescription = new oxField('20 percent', oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxdiscount = new oxField($discount, oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxdiscounttype = new oxField($discounttype, oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxbegindate = new oxField($startDate, oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxenddate = new oxField($endDate, oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxallowsameseries = new oxField('1', oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxalowotherseries = new oxField('0', oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxallowuseanother = new oxField('1', oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxminimumvalue = new oxField('0.00', oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxcalculateonce = new oxField('1', oxField::T_RAW);
        $voucherSeries->save();

        for ($i=1; $i<=4; $i++) {
            $voucherId = substr_replace(oxUtilsObject::getInstance()->generateUId(), '_', 0, 1);
            $voucher = oxNew('oxVoucher');
            $voucher->setId($voucherId);
            $voucher->oxvouchers__oxvouchernr = new oxField($prefix . $i, oxField::T_RAW);
            $voucher->oxvouchers__oxvoucherserieid = new oxField($this->voucherSeriesId, oxField::T_RAW);
            $voucher->save();
        }
    }

    /**
     * Discount on article price when purchasing 10 or more
     *
     */
    private function createDiscount()
    {
        $startDate = date('Y-m-d 00:00:00', time() - 86400);
        $endDate = date('Y-m-d 00:00:00', time() + 86400);

        $this->discountId = substr_replace(oxUtilsObject::getInstance()->generateUId(), '_', 0, 1);
        $discount = oxNew('oxDiscount');
        $discount->setId($this->discountId);
        $discount->oxdiscount__oxshopid = new oxField('1', oxField::T_RAW);
        $discount->oxdiscount__oxactive = new oxField('1', oxField::T_RAW);
        $discount->oxdiscount__oxactivefrom = new oxField($startDate, oxField::T_RAW);
        $discount->oxdiscount__oxactiveto = new oxField($endDate, oxField::T_RAW);
        $discount->oxdiscount__oxtitle = new oxField('test discount', oxField::T_RAW);
        $discount->oxdiscount__oxamount = new oxField('10', oxField::T_RAW);
        $discount->oxdiscount__oxamountto = new oxField('999999', oxField::T_RAW);
        $discount->oxdiscount__oxpriceto = new oxField('999999', oxField::T_RAW);
        $discount->oxdiscount__oxprice = new oxField('0.0', oxField::T_RAW);
        $discount->oxdiscount__oxaddsumtype = new oxField('%', oxField::T_RAW);
        $discount->oxdiscount__oxaddsum = new oxField('20', oxField::T_RAW);
        $discount->save();

        $newId = substr_replace(oxUtilsObject::getInstance()->generateUId(), '_', 0, 1);
        $oDb = oxDb::getDb();
        $sQ = 'insert into `oxobject2discount` (oxid, oxdiscountid, oxobjectid, oxtype ) ' .
              " values ('$newId', '" . $this->discountId . "', '" . $this->testArticleId . "', 'oxarticles')";
        $oDb->execute($sQ);
    }

    /**
     * Set valid timespan for vouchers to past time.
     */
    private function endVouchers()
    {
        $startDate = date('Y-m-d 00:00:00', time() - 3 * 86400);
        $endDate   = date('Y-m-d 00:00:00', time() - 86400);

        $voucherSeries = oxNew('oxVoucherSerie');
        $voucherSeries->load($this->voucherSeriesId);
        $voucherSeries->oxvoucherseries__oxbegindate = new oxField($startDate, oxField::T_RAW);
        $voucherSeries->oxvoucherseries__oxenddate = new oxField($endDate, oxField::T_RAW);
        $voucherSeries->save();
    }

    /**
     * Set valid timespan for vouchers to past time.
     */
    private function endDiscount()
    {
        $startDate = date('Y-m-d 00:00:00', time() - 3 * 86400);
        $endDate   = date('Y-m-d 00:00:00', time() - 86400);

        $discount = oxNew('oxDiscount');
        $discount->load($this->discountId);
        $discount->oxdiscount__oxactive = new oxField('0', oxField::T_RAW);
        $discount->oxdiscount__oxactivefrom = new oxField($startDate, oxField::T_RAW);
        $discount->oxdiscount__oxactiveto = new oxField($endDate, oxField::T_RAW);
        $discount->save();
    }

    /**
     * Place order, buy given amount of testarticle.
     *
     * @param $buyAmount
     * @param $vouchers
     *
     * @return object
     */
    private function placeOrder($buyAmount, $vouchers = array())
    {
        $basket = oxRegistry::getSession()->getBasket();
        $this->assertEquals(0, $basket->getBasketSummary()->iArticleCount);
        $this->assertNull(oxRegistry::getSession()->getVariable('_newitem'));

        foreach ($vouchers as $name){
            $basket->addVoucher($name);
        }

        //try to be as close to usual checkout as possible
        $basketComponent = oxNew('oxcmp_basket');
        $redirectUrl     = $basketComponent->tobasket($this->testArticleId, $buyAmount);
        $this->assertEquals('start?', $redirectUrl);

        $basket = $this->getSession()->getBasket();
        $basket->calculateBasket(false);
        $basket->setPayment('oxidinvoice');
        $this->checkContents($basket, $buyAmount);

        $user = oxNew('oxUser');
        $user->load($this->testUserId);

        $order = $this->createOrder();
        oxRegistry::getSession()->setVariable('sess_challenge', $this->testOrderId);

        $blRecalculatingOrder = false;
        $result = $order->finalizeOrder($basket, $user, $blRecalculatingOrder);
        $this->assertEquals(oxOrder::ORDER_STATE_OK, $result);
        $this->assertEquals($this->testOrderId, $order->getId());

        return $order;
    }
}
