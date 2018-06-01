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
 * @copyright (C) OXID eSales AG 2003-2018
 * @version   OXID eShop CE
 */

class Integration_Checkout_BasketReservationStockUpdateTest extends OxidTestCase
{
    /**
     * Make a copy of Stewart+Brown Shirt Kisser Fish parent and variant L violet for testing
     */
    const SOURCE_ARTICLE_ID = '6b6d966c899dd9977e88f842f67eb751';
    const SOURCE_ARTICLE_PARENT_ID = '6b6099c305f591cb39d4314e9a823fc1';

    /**
     * Generated test article, test user and order ids.
     * @var string
     */
    private $testArticleId = null;
    private $testArticleParentId = null;
    private $testOrderId = null;
    private $testUserId = null;

    /**
     * Fixture setUp.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->insertArticle();
        $this->insertUser();
        $this->getConfig()->setConfigParam('blAllowNegativeStock', false);
        $this->getConfig()->setConfigParam('blUseStock', true);
        $this->getConfig()->setConfigParam('iPsBasketReservationTimeout', 1200);
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $this->getConfig()->setConfigParam('iNewBasketItemMessage', 0);

        $_POST = array();
    }

    /*
    * Fixture tearDown.
    */
    protected function tearDown()
    {
        $_POST = array();

        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxorderarticles');
        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxuserpayments');
        $this->cleanUpTable('oxuserbaskets');
        $this->cleanUpTable('oxuserbasketitems');
        $this->cleanUpTable('oxobject2delivery');

        parent::tearDown();
    }

    /**
     * Mode is no basket reservation.
     */
    public function testPutArticlesToBasketNoReservation()
    {
        $stock = 60;
        $buyAmount = 20;
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);
        $this->getConfig()->setConfigParam('iNewBasketItemMessage', 1);

        $this->setStock($stock);
        $basket = $this->fillBasket($buyAmount);
        $this->assertNewItemMarker($buyAmount);
        $this->checkContents($basket, $buyAmount);

        //without basket reservation there is no stock change when articles are
        //but into basket
        $this->assertEquals($stock, $this->getStock());

        //NOTE: take care when calling getBasketSummary,
        // oxBasket::_blUpdateNeeded is set to false when afterUpdate is called.
        // so in case summary was called before and _blUpdateNeeded ist set to false,
        // basketSummary adds up article count on each call (), see here:
        $this->assertEquals(40, $basket->getBasketSummary()->iArticleCount);
        $this->assertEquals(60, $basket->getBasketSummary()->iArticleCount);
        $this->assertEquals(80, $basket->getBasketSummary()->aArticles[$this->testArticleId]);

        $basket->onUpdate(); //starts adding up after next call to oxBasket::calculateBasket
        $this->assertEquals(20, $basket->getBasketSummary()->aArticles[$this->testArticleId]);
        $this->assertEquals(20, $basket->getBasketSummary()->aArticles[$this->testArticleId]);
    }

    /**
     * Mode is no basket reservation.
     * Put more of the same article in basket than stock says we have.
     */
    public function testPutArticlesToBasketNoReservationMoreThanAvailable()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $stock = 60;
        $buyAmount = $stock + 10;

        //not orderable if out of stock
        $this->setStockFlag(3);
        $this->setStock($stock);

        $basket = oxRegistry::getSession()->getBasket();
        $this->assertEquals(0, $basket->getBasketSummary()->iArticleCount);

        $this->setSessionParam('basketReservationToken', null);
        $this->assertNull(oxRegistry::getSession()->getVariable('_newitem'));

        //try to be as close to usual checkout as possible
        $basketComponent = oxNew('oxcmp_basket');
        $redirectUrl = $basketComponent->tobasket($this->testArticleId, $buyAmount);
        $this->assertEquals('start?', $redirectUrl);

        //newItem not set by oxcmp_basket::tobasket
        $this->assertNull(oxRegistry::getSession()->getVariable('_newitem'));

    }

    /**
     * Mode is basket reservation with timeout.
     */
    public function testPutArticlesToBasketTimeout()
    {
        $stock = 60;
        $buyAmount = 20;

        $this->setStock($stock);
        $basket = $this->fillBasket($buyAmount);
        $this->checkContents($basket, $buyAmount);

        //article stock is reduced in database due to reservation
        $this->assertEquals($stock - $buyAmount, $this->getStock());

        $this->checkContents($basket, $buyAmount);
    }

    /**
     * Mode is basket reservation with timeout.
     * Finalize the order.
     * ESDEV-2901 testcase. Also see https://bugs.oxid-esales.com/view.php?id=6050
     */
    public function testPlaceOrderWithBasketTimeout()
    {
        $stock     = 60;
        $buyAmount = 20;

        $this->setStock($stock);
        $basket = $this->fillBasket($buyAmount);
        $this->checkContents($basket, $buyAmount);
        $basket->setPayment('oxidinvoice');

        // stock reduced in db caused by reservation
        $this->assertEquals($stock-$buyAmount, $this->getStock());

        $user = oxNew('oxUser');
        $user->load($this->testUserId);

        $order = $this->createOrder();
        oxRegistry::getSession()->setVariable('sess_challenge', $this->testOrderId);

        $blRecalculatingOrder = false;
        $result = $order->finalizeOrder($basket, $user, $blRecalculatingOrder);
        $this->assertEquals(oxOrder::ORDER_STATE_OK, $result);
        $this->assertEquals($stock-$buyAmount, $this->getStock());

        //make sure qe have the oxorder.oxid we wanted
        $this->assertEquals($this->testOrderId, $order->getId());

        //*start* snippet from oxorder::getShippingSetList
        $orderMain = $this->getProxyClass('oxOrder');
        $orderMain->load($this->testOrderId);
        $orderBasket = $orderMain->UNITgetOrderBasket();

        $orderArticles = $orderMain->getOrderArticles();

        //relevant code from oxorder::_addOrderArticlesToBasket
        $orderBasketContents = array();
        foreach ($orderArticles as $orderArticle) {
            $orderBasketContents[] = $orderBasket->addOrderArticleToBasket($orderArticle);
        }

        //some checks on result
        $this->assertEquals(1, count($orderBasketContents));
        $this->assertTrue(is_a($orderBasketContents[0], 'oxBasketItem'));
        $this->assertEquals($this->testArticleId, $orderBasketContents[0]->getProductId());
        $this->assertEquals($buyAmount, $orderBasketContents[0]->getAmount());

        //stock was not changed up do now, but without the above snippet, stock is as expected when calling calculate basket
        $this->assertEquals($stock-$buyAmount, $this->getStock(), 'fails before calculate basket'); //ok up to now

        //reservations are only allowed when the shop is not in admin mode.
        $orderBasket->setAdminMode(true);
        $orderBasket->calculateBasket(true);

        $this->assertEquals($stock-$buyAmount, $this->getStock());
    }

    /**
     * @return array
     */
    public function providerPutArticlesToBasketAndRemove()
    {
        $data = array();

        //blUseStock
        //blAllowNegativeStock
        //
        // Stockflag meanings (article specific):
        // 1 GENERAL_STANDARD // 'Standard'
        // 2 GENERAL_OFFLINE  // 'If out of Stock, offline'
        // 3 GENERAL_NONORDER // 'If out of Stock, not orderable'
        // 4 GENERAL_EXTERNALSTOCK // 'External Storehouse'

        //No tampering with stock without basket reservations enabled
        $data['no_reservations'] = array('enableReservation' => false,
            'stock' => 60,
            'expected_stock_after_tobasket' => 60,
            'buy_amount' => 40,
            'stock_flag' => 3,
            'allow_negative_stock' => false,
            'basket_amount' => 40,
            'display_warning_cnt' => 0);

        //Tampering with stock when basket reservations enabled
        $data['do_reservations'] = array('enableReservation' => true,
            'stock' => 60,
            'expected_stock_after_tobasket' => 20,
            'buy_amount' => 40,
            'stock_flag' => 3,
            'allow_negative_stock' => false,
            'basket_amount' => 40,
            'display_warning_cnt' => 0);

        //No tampering with stock without basket reservations enabled, order last item in stock
        $data['no_reservations_low_stock'] = array('enableReservation' => false,
            'stock' => 1,
            'expected_stock_after_tobasket' => 1,
            'buy_amount' => 1,
            'stock_flag' => 3,
            'allow_negative_stock' => false,
            'basket_amount' => 1,
            'display_warning_cnt' => 0);

        //Tampering with stock when basket reservations enabled, order last item in stock
        $data['do_reservations_low_stock'] = array('enableReservation' => true,
            'stock' => 1,
            'expected_stock_after_tobasket' => 0,
            'buy_amount' => 1,
            'stock_flag' => 3,
            'allow_negative_stock' => false,
            'basket_amount' => 1,
            'display_warning_cnt' => 0);

        //No basket reservations enabled, try to buy more than available, stockflag is 3.
        //This will get the one available article put to basket.
        $data['no_reservations_low_stock_order_more'] = array('enableReservation' => false,
            'stock' => 1,
            'expected_stock_after_tobasket' => 1,
            'buy_amount' => 10,
            'stock_flag' => 3,
            'allow_negative_stock' => false,
            'basket_amount' => 1,
            'display_warning_cnt' => 1);

        //Basket reservations enabled, try to buy more than available, stockflag is 3
        $data['do_reservations_low_stock_order_more'] = array('enableReservation' => true,
            'stock' => 1,
            'expected_stock_after_tobasket' => 0,
            'buy_amount' => 10,
            'stock_flag' => 3,
            'allow_negative_stock' => false,
            'basket_amount' => 1,
            'display_warning_cnt' => 1);

        //NOTE: in case the requested Article count exceeds the available, we end up with to be displayed
        //error information in oxRegistry::getSession()->getVariable('Errors'));

        //Use default stock flag (1), no reservations, disallow negative stock.
        //You can put more articles in basket than are in stock.
        $data['no_res_low_stock_order_more_stockflag_default'] = array('enableReservation' => false,
            'stock' => 1,
            'expected_stock_after_tobasket' => 1,
            'buy_amount' => 10,
            'stock_flag' => 1,
            'allow_negative_stock' => false,
            'basket_amount' => 10,
            'display_warning_cnt' => 0);

        //Use default stock flag, disallow negative stock. You cannot put more articles in basket than are in stock
        //when reservations are enabled.
        $data['do_res_low_stock_order_more_stockflag_default'] = array('enableReservation' => true,
            'stock' => 1,
            'expected_stock_after_tobasket' => 0,
            'buy_amount' => 10,
            'stock_flag' => 1,
            'allow_negative_stock' => false,
            'basket_amount' => 1,
            'display_warning_cnt' => 1);

        //Same as data set 'do_res_low_stock_order_more_stockflag_default' but with allowing
        //negative stock values. Basket reservations keep stock as would be expected.
        $data['do_res_low_stock_order_more_stockflag_default_neg_ok'] = array('enableReservation' => true,
            'stock' => 1,
            'expected_stock_after_tobasket' => -9,
            'buy_amount' => 10,
            'stock_flag' => 1,
            'allow_negative_stock' => true,
            'basket_amount' => 10,
            'display_warning_cnt' => 0);
        return $data;
    }

    /**
     * Test case to put articles into basket and remove again.
     * Check the stock levels before and after, they must be the same.
     *
     * @dataProvider providerPutArticlesToBasketAndRemove
     *
     * @param bool    $enableReservation          Enable basket reservation yes/no.
     * @param integer $stock                      Original stock amount for test article.
     * @param integer $expectedStockAfterToBasket Stock amount in oxarticles after article was put into basket
     * @param integer $buyAmount                  Amount to buy
     * @param integer $stockFlag                  Stock flag
     * @param bool    $allowNegativeStock         Allow negative stock yes/no
     * @param integer $basketAmount               Amount that ended up in basket, might be less thann buyamount related
     *                                            to configured out of stock behaviour.
     * @param integer $displayWarningCnt          Number ov eventual display warning due to low stock
     */
    public function testPutArticlesToBasketAndRemove($enableReservation, $stock, $expectedStockAfterToBasket,
                                                     $buyAmount, $stockFlag, $allowNegativeStock, $basketAmount,
                                                     $displayWarningCnt)
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', $enableReservation);
        $this->getConfig()->setConfigParam('blAllowNegativeStock', $allowNegativeStock);
        $this->setStock($stock);
        $this->setStockFlag($stockFlag);

        //Check stock when basket is filled
        $basket = $this->fillBasket($buyAmount);
        $this->checkContents($basket, $basketAmount);

        //Check for expected messages
        $messages = oxRegistry::getSession()->getVariable('Errors');
        $this->assertEquals($displayWarningCnt, count($messages['default']));

        $this->assertEquals($expectedStockAfterToBasket, $this->getStock());

        //Check stock when items were removed from basket, must be back to original value
        $this->removeFromBasket();
        $this->assertEquals($stock, $this->getStock(), 'Stock after remove from basket must match original value.');
    }

    /**
     * Verify that item was reserved immediately when put into basket.
     *
     */
    public function testArticleReservedWhenPutIntoBasket()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $this->getConfig()->setConfigParam('blAllowNegativeStock', false);
        $this->setStock(1);
        $this->setStockFlag(3);

        //One item in basket and we have a reservation now and amount in stock was changed
        $this->addOneItemToBasket();
        $this->assertEquals(0, $this->getStock());
        $this->assertEquals(1, (oxRegistry::getSession()->getBasketReservations()->getReservedAmount($this->testArticleId)));
        $this->assertEquals(1, $this->getAmountInBasket());
        $this->assertEquals(1, (oxRegistry::getSession()->getBasketReservations()->getReservedAmount($this->testArticleId)));

        //As the only item in stock is reserved, should not be possible to add this item to basket anymore.
        $this->addOneItemToBasket(1); //we stick with the one already in basket
        //and get info that item stock does not allow adding any more to basket
        $this->assertNotEmpty(oxRegistry::getSession()->getVariable('Errors'));
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
        $article->oxarticles__oxprice = new oxField('10.0', oxField::T_RAW);
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
        $order = $this->getMock('oxOrder', array('validateDeliveryAddress', '_sendOrderByEmail', 'validatePayment'));
        // sending order by email is always successful for tests
        $order->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(1));
        //mocked to circumvent delivery address change md5 check from requestParameter
        $order->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(0));

        $this->testOrderId = substr_replace( oxUtilsObject::getInstance()->generateUId(), '_', 0, 1 );
        $order->setId($this->testOrderId);

        return $order;
    }

    /**
     * Get current stock of article variant.
     */
    private function getStock()
    {
        $article = oxNew('oxArticle');
        $article->load($this->testArticleId);
        return $article->oxarticles__oxstock->value;
    }

    /**
     * Set current stock of article variant.
     */
    private function setStock($stock)
    {
        $article = oxNew('oxArticle');
        $article->load($this->testArticleId);
        $article->oxarticles__oxstock = new oxField($stock, oxField::T_RAW);
        $article->save();

        $this->assertEquals($stock, $this->getStock());
    }

    /**
     * Set current stock of article variant.
     */
    private function setStockFlag($stockFlag)
    {
        $article = oxNew('oxArticle');
        $article->load($this->testArticleId);
        $article->oxarticles__oxstockflag = new oxField($stockFlag, oxField::T_RAW);
        $article->save();
    }

    /**
     * Check if 'new item marker' has been set in basket.
     *
     * @param integer $buyAmount Expected amount of products put to basket
     */
    private function assertNewItemMarker($buyAmount)
    {
        //newItem is an stdClass
        $newItem = oxRegistry::getSession()->getVariable('_newitem');
        $this->assertEquals($this->testArticleId, $newItem->sId);
        $this->assertEquals($buyAmount, $newItem->dAmount);
    }

    /**
     * Test helper to check basket contents.
     *
     * @param oxBasket $basket
     */
    private function checkContents(oxBasket $basket, $expectedAmount)
    {
        //only one different article but buyAmount items in basket
        $this->assertEquals(1, $basket->getProductsCount());
        $this->assertEquals($expectedAmount, $basket->getItemsCount());

        $basketArticles = $basket->getBasketArticles();
        $keys = array_keys($basketArticles);
        $this->assertTrue(is_array($basketArticles));
        $this->assertEquals(1, count($basketArticles));
        $this->assertTrue(is_a($basketArticles[$keys[0]], 'oxArticle'));
        $this->assertEquals($this->testArticleId, $basketArticles[$keys[0]]->getId());

        $basketContents = $basket->getContents();
        $keys = array_keys($basketContents);
        $this->assertTrue(is_array($basketContents));
        $this->assertEquals(1, count($basketArticles));
        $this->assertTrue(is_a($basketContents[$keys[0]], 'oxBasketItem'));

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
        $user->oxuser__oxshopid = new oxField('oxbaseshop', oxField::T_RAW);
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
     * Put given amount of testarticle into the basket.
     *
     * @param $buyAmount
     *
     * @return oxOrder
     */
    private function fillBasket($buyAmount)
    {
        $basket = oxRegistry::getSession()->getBasket();
        $this->assertEquals(0, $this->getAmountInBasket());

        $this->setSessionParam('basketReservationToken', null);
        $this->assertNull(oxRegistry::getSession()->getVariable('_newitem'));

        //try to be as close to usual checkout as possible
        $basketComponent = oxNew('oxcmp_basket');
        $redirectUrl = $basketComponent->tobasket($this->testArticleId, $buyAmount);
        $this->assertEquals('start?', $redirectUrl);

        $basket = $this->getSession()->getBasket();
        $basket->calculateBasket(true); //calls oxBasket::afterUpdate

        return $basket;
    }

    /**
     * Remove all items from basket.
     */
    private function removeFromBasket()
    {
        $basket = oxRegistry::getSession()->getBasket();
        $countBefore = $this->getAmountInBasket();

        $parameters = array(
            'stoken' => oxRegistry::getSession()->getSessionChallengeToken(),
            'updateBtn' => '',
            'aproducts' => array($basket->getItemKey($this->testArticleId) => array(
                'remove' => '1',
                'aid ' => $this->testArticleId,
                'basketitemid' => $basket->getItemKey($this->testArticleId),
                'override' => 1,
                'am' => $countBefore)
            )
        );
        $this->setRequestParameters($parameters);

        //try to be as close to the checkout as possible
        $basketComponent = oxNew('oxcmp_basket');
        $basketComponent->changeBasket($this->testArticleId);

        $basket = $this->getSession()->getBasket();
        $basket->calculateBasket(true); //calls oxBasket::afterUpdate

        $countAfter = $this->getAmountInBasket();
        $this->assertEquals(0, $countAfter);
    }

    /**
     * Add one test article to basket.
     *
     * @param integer $expected Optional expected amount.
     */
    private function addOneItemToBasket($expected = null)
    {
        $countBefore = $this->getAmountInBasket();
        $expected = is_null($expected) ? $countBefore + 1 : $expected;

        $parameters = array(
            'stoken' => oxRegistry::getSession()->getSessionChallengeToken(),
            'actcontrol' => 'start',
            'lang' => 0,
            'pgNr' => 0,
            'cl' => 'start',
            'fnc' => 'tobasket',
            'aid' => $this->testArticleId,
            'anid' => $this->testArticleId,
            'am' => 1
        );
        $this->setRequestParameters($parameters);

        //try to be as close to the checkout as possible
        $basketComponent = oxNew('oxcmp_basket');
        $basketComponent->toBasket($this->testArticleId, 1);

        $this->assertEquals($expected, $this->getAmountInBasket());
    }

    /**
     * NOTE: Do not use Basket::getBasketSummary() as this method adds up on every call.
     *
     * Test helper to get amount of test artile in basket.
     *
     * @return integer
     */
    private function getAmountInBasket()
    {
        $return = 0;
        $basket = oxRegistry::getSession()->getBasket();
        $basketContents = $basket->getContents();
        $basketItemId = $basket->getItemKey($this->testArticleId);

        if (is_a($basketContents[$basketItemId],'oxBasketItem')) {
            $return = $basketContents[$basketItemId]->getAmount();
        }
        return $return;
    }

    /**
     * Test helper.
     *
     * @param $data
     */
    private function setRequestParameters($data)
    {
        foreach ($data as $key => $value) {
            modConfig::setRequestParameter($key, $value);
        }
    }
}
