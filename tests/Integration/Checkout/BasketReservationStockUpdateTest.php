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
namespace OxidEsales\EshopCommunity\Tests\Integration\Checkout;

use OxidEsales\EshopCommunity\Core\ShopIdCalculator;

class BasketReservationStockUpdateTest extends \OxidTestCase
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

        $this->setStock($stock);

        $basket = $this->fillBasket($buyAmount);

        //only one different article but 20 items in basket
        $this->assertEquals(1, $basket->getProductsCount());
        $this->assertEquals($buyAmount, $basket->getItemsCount());

        //without basket reservation there is no stock change when articles are
        //but into basket
        $this->assertEquals($stock, $this->getStock());

        $this->checkContents($basket, $buyAmount);

        //NOTE: take care when calling getBasketSummary,
        // \OxidEsales\Eshop\Application\Model\Basket::_blUpdateNeeded is set to false when afterUpdate is called.
        // so in case summary was called before and _blUpdateNeeded ist set to false,
        // basketSummary adds up article count on each call (), see here:
        $this->assertEquals(40, $basket->getBasketSummary()->iArticleCount);
        $this->assertEquals(60, $basket->getBasketSummary()->iArticleCount);
        $this->assertEquals(80, $basket->getBasketSummary()->aArticles[$this->testArticleId]);

        $basket->onUpdate(); //starts adding up after next call to \OxidEsales\Eshop\Application\Model\Basket::calculateBasket
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

        $basket = \OxidEsales\Eshop\Core\Registry::getSession()->getBasket();
        $this->assertEquals(0, $basket->getBasketSummary()->iArticleCount);

        $this->setSessionParam('basketReservationToken', null);
        $this->assertNull(\OxidEsales\Eshop\Core\Registry::getSession()->getVariable('_newitem'));

        //try to be as close to usual checkout as possible
        $basketComponent = oxNew(\OxidEsales\Eshop\Application\Component\BasketComponent::class);
        $redirectUrl = $basketComponent->tobasket($this->testArticleId, $buyAmount);
        $this->assertEquals('start?', $redirectUrl);

        //newItem not set by \OxidEsales\Eshop\Application\Component\BasketComponent::tobasket
        $this->assertNull(\OxidEsales\Eshop\Core\Registry::getSession()->getVariable('_newitem'));

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
        $basket->setPayment('oxidinvoice');

        // stock reduced in db caused by reservation
        $this->assertEquals($stock - $buyAmount, $this->getStock());

        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->load($this->testUserId);

        $order = $this->createOrder();
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('sess_challenge', $this->testOrderId);

        $blRecalculatingOrder = false;
        $result = $order->finalizeOrder($basket, $user, $blRecalculatingOrder);
        $this->assertEquals(\OxidEsales\Eshop\Application\Model\Order::ORDER_STATE_OK, $result);
        $this->assertEquals($stock - $buyAmount, $this->getStock());

        //make sure qe have the oxorder.oxid we wanted
        $this->assertEquals($this->testOrderId, $order->getId());

        //*start* snippet from oxorder::getShippingSetList
        $orderMain = $this->getProxyClass(\OxidEsales\Eshop\Application\Model\Order::class);
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
        $this->assertTrue(is_a($orderBasketContents[0], '\OxidEsales\EshopCommunity\Application\Model\BasketItem'));
        $this->assertEquals($this->testArticleId, $orderBasketContents[0]->getProductId());
        $this->assertEquals($buyAmount, $orderBasketContents[0]->getAmount());

        //stock was not changed up do now, but without the above snippet, stock is as expected when calling calculate basket
        $this->assertEquals($stock - $buyAmount, $this->getStock(), 'fails before calculate basket'); //ok up to now

        //reservations are only allowed when the shop is not in admin mode.
        $orderBasket->setAdminMode(true);
        $orderBasket->calculateBasket(true);

        $this->assertEquals($stock - $buyAmount, $this->getStock());
    }

    /**
     * Make a copy of article and variant for testing.
     */
    private function insertArticle()
    {
        $this->testArticleId = substr_replace(\OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUId(), '_', 0, 1 );
        $this->testArticleParentId = substr_replace(\OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUId(), '_', 0, 1);

        //copy from original article parent and variant
        $articleParent = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $articleParent->disableLazyLoading();
        $articleParent->load(self::SOURCE_ARTICLE_PARENT_ID);
        $articleParent->setId($this->testArticleParentId);
        $articleParent->oxarticles__oxartnum = new \OxidEsales\Eshop\Core\Field('666-T', \OxidEsales\Eshop\Core\Field::T_RAW);
        $articleParent->save();

        $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $article->disableLazyLoading();
        $article->load(self::SOURCE_ARTICLE_ID);
        $article->setId($this->testArticleId);
        $article->oxarticles__oxparentid = new \OxidEsales\Eshop\Core\Field($this->testArticleParentId, \OxidEsales\Eshop\Core\Field::T_RAW);
        $article->oxarticles__oxprice = new \OxidEsales\Eshop\Core\Field('10.0', \OxidEsales\Eshop\Core\Field::T_RAW);
        $article->oxarticles__oxartnum = new \OxidEsales\Eshop\Core\Field('666-T-V', \OxidEsales\Eshop\Core\Field::T_RAW);
        $article->oxarticles__oxactive = new \OxidEsales\Eshop\Core\Field('1', \OxidEsales\Eshop\Core\Field::T_RAW);
        $article->save();
    }

    /**
     * Create order object with test oxidid (leading underscore).
     *
     * @return object \OxidEsales\Eshop\Application\Model\Order
     */
    private function createOrder()
    {
        $order = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('validateDeliveryAddress', '_sendOrderByEmail'));
        // sending order by email is always successful for tests
        $order->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(1));
        //mocked to circumvent delivery address change md5 check from requestParameter
        $order->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(0));

        $this->testOrderId = substr_replace(\OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUId(), '_', 0, 1);
        $order->setId($this->testOrderId);

        return $order;
    }

    /**
     * Get current stock of article variant.
     */
    private function getStock()
    {
        $article = oxNew('\OxidEsales\Eshop\Application\Model\Article');
        $article->load($this->testArticleId);
        return $article->oxarticles__oxstock->value;
    }

    /**
     * Set current stock of article variant.
     */
    private function setStock($stock)
    {
        $article = oxNew('\OxidEsales\Eshop\Application\Model\Article');
        $article->load($this->testArticleId);
        $article->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field($stock, \OxidEsales\Eshop\Core\Field::T_RAW);
        $article->save();

        $this->assertEquals($stock, $this->getStock());
    }

    /**
     * Set current stock of article variant.
     */
    private function setStockFlag($stockFlag)
    {
        $article = oxNew('\OxidEsales\Eshop\Application\Model\Article');
        $article->load($this->testArticleId);
        $article->oxarticles__oxstockflag = new \OxidEsales\Eshop\Core\Field($stockFlag, \OxidEsales\Eshop\Core\Field::T_RAW);
        $article->save();
    }

    /**
     * @param \OxidEsales\Eshop\Application\Model\Basket $basket
     */
    private function checkContents(\OxidEsales\Eshop\Application\Model\Basket $basket, $expectedAmount)
    {
        $basketArticles = $basket->getBasketArticles();
        $keys = array_keys($basketArticles);
        $this->assertTrue(is_array($basketArticles));
        $this->assertEquals(1, count($basketArticles));
        $this->assertTrue(is_a($basketArticles[$keys[0]], '\OxidEsales\EshopCommunity\Application\Model\Article'));
        $this->assertEquals($this->testArticleId, $basketArticles[$keys[0]]->getId());

        $basketContents = $basket->getContents();
        $keys = array_keys($basketContents);
        $this->assertTrue(is_array($basketContents));
        $this->assertEquals(1, count($basketArticles));
        $this->assertTrue(is_a($basketContents[$keys[0]], '\OxidEsales\EshopCommunity\Application\Model\BasketItem'));

        $basketItem = $basketContents[$keys[0]];
        $this->assertEquals($this->testArticleId, $basketItem->getProductId());
        $this->assertEquals($expectedAmount, $basketItem->getAmount());
    }

    /**
     * insert test user
     */
    private function insertUser()
    {
        $this->testUserId = substr_replace(\OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUId(), '_', 0, 1);

        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->setId($this->testUserId);

        $user->oxuser__oxactive = new \OxidEsales\Eshop\Core\Field('1', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxrights = new \OxidEsales\Eshop\Core\Field('user', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxshopid = new \OxidEsales\Eshop\Core\Field(ShopIdCalculator::BASE_SHOP_ID, \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxusername = new \OxidEsales\Eshop\Core\Field('testuser@oxideshop.dev', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxpassword = new \OxidEsales\Eshop\Core\Field('c630e7f6dd47f9ad60ece4492468149bfed3da3429940181464baae99941d0ffa5562' .
                                                'aaecd01eab71c4d886e5467c5fc4dd24a45819e125501f030f61b624d7d',
            \OxidEsales\Eshop\Core\Field::T_RAW); //password is asdfasdf
        $user->oxuser__oxpasssalt = new \OxidEsales\Eshop\Core\Field('3ddda7c412dbd57325210968cd31ba86', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxcustnr = new \OxidEsales\Eshop\Core\Field('666', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxfname = new \OxidEsales\Eshop\Core\Field('Bla', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxlname = new \OxidEsales\Eshop\Core\Field('Foo', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxstreet = new \OxidEsales\Eshop\Core\Field('blafoostreet', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxstreetnr = new \OxidEsales\Eshop\Core\Field('123', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxcity = new \OxidEsales\Eshop\Core\Field('Hamburg', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxcountryid = new \OxidEsales\Eshop\Core\Field('a7c40f631fc920687.20179984', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxzip = new \OxidEsales\Eshop\Core\Field('22769', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxsal = new \OxidEsales\Eshop\Core\Field('MR', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxactive = new \OxidEsales\Eshop\Core\Field('1', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxboni = new \OxidEsales\Eshop\Core\Field('1000', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxcreate = new \OxidEsales\Eshop\Core\Field('2015-05-20 22:10:51', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxregister = new \OxidEsales\Eshop\Core\Field('2015-05-20 22:10:51', \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->oxuser__oxboni = new \OxidEsales\Eshop\Core\Field('1000', \OxidEsales\Eshop\Core\Field::T_RAW);

        $user->save();

        $newId = substr_replace(\OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUId(), '_', 0, 1);
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $query = 'insert into `oxobject2delivery` (oxid, oxdeliveryid, oxobjectid, oxtype) ' .
                 " values ('$newId', 'oxidstandard', '" . $this->testUserId . "', 'oxdelsetu')";
        $database->execute($query);
    }

    /**
     * Put given amount of testarticle into the basket.
     *
     * @param $buyAmount
     *
     * @return \OxidEsales\Eshop\Application\Model\Order
     */
    private function fillBasket($buyAmount)
    {
        $basket = \OxidEsales\Eshop\Core\Registry::getSession()->getBasket();
        $this->assertEquals(0, $basket->getBasketSummary()->iArticleCount);

        $this->setSessionParam('basketReservationToken', null);
        $this->assertNull(\OxidEsales\Eshop\Core\Registry::getSession()->getVariable('_newitem'));

        //try to be as close to usual checkout as possible
        $basketComponent = oxNew(\OxidEsales\Eshop\Application\Component\BasketComponent::class);
        $redirectUrl = $basketComponent->tobasket($this->testArticleId, $buyAmount);
        $this->assertEquals('start?', $redirectUrl);

        //newItem is an stdClass
        $newItem = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('_newitem');
        $this->assertEquals($this->testArticleId, $newItem->sId);
        $this->assertEquals($buyAmount, $newItem->dAmount);

        $basket = $this->getSession()->getBasket();
        $basket->calculateBasket(true); //calls \OxidEsales\Eshop\Application\Model\Basket::afterUpdate

        return $basket;
    }
}
