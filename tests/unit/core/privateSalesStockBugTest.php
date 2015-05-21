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

class Unit_Core_privatSalesStockBugTest extends OxidTestCase
{
    /**
     * Make a copy of Kuyichi T-Shirt TIGER parent and variant orange L for testing
     */
    const SOURCE_ARTICLE_ID = '10067ab25bf275b7e68bc0431b204d24';
    const SOURCE_ARTICLE_PARENT_ID = 'dc581d8a115035cbfb0223c9c736f513';
    const TEST_USER_OXID = 'e7af1c3b786fd02906ccd75698f4e6b9';

    /**
     * Generated test article and order ids.
     * @var string
     */
    private $testArticleId = null;
    private $testArticleParentId = null;
    private $testOrderId = null;

    /**
     * Store original shop configuration values.
     * @var mixed
     */
    private $originalAllowNegativeStock = null;
    private $originalUseStock = null;
    private $originalReservationTimeout = null;
    private $originalReservationEnabled = null;

    /**
     * Fixture setUp.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->insertArticle();

        //prepare config for private sales basket reservation
        $this->originalAllowNegativeStock = $this->getConfig()->getConfigParam('blAllowNegativeStock');
        $this->originalUseStock = $this->getConfig()->getConfigParam('blUseStock');
        $this->originalReservationTimeout = $this->getConfig()->getConfigParam('iPsBasketReservationTimeout');
        $this->originalReservationEnabled = $this->getConfig()->getConfigParam('blPsBasketReservationEnabled');

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
        //restore config
        $this->getConfig()->setConfigParam('blAllowNegativeStock', $this->originalAllowNegativeStock);
        $this->getConfig()->setConfigParam('blUseStock', $this->originalUseStock);
        $this->getConfig()->setConfigParam('iPsBasketReservationTimeout', $this->originalReservationTimeout);
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', $this->originalReservationEnabled);

        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxorder');

        oxRegistry::getSession()->delBasket();
        oxRegistry::getSession()->deleteVariable('_newitem');

        parent::tearDown();
    }

    /**
     * Mode is no basket reservation.
     */
    public function testPutArticlesToBasketNoReservation()
    {
        $this->markTestSkipped();

        $stock = 60;
        $buyAmount = 20;
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $this->setStock($stock);
        $this->assertEquals($stock, $this->getStock());

        $basket = oxRegistry::getSession()->getBasket();
        $this->assertEquals(0, $basket->getBasketSummary()->iArticleCount);

        $this->setSessionParam('basketReservationToken', null);
        $this->assertNull(oxRegistry::getSession()->getVariable('_newitem'));

        //try to be as close to usual checkout as possible
        $basketComponent = oxNew('oxcmp_basket');
        $redirectUrl = $basketComponent->tobasket($this->testArticleId, $buyAmount);
        $this->assertEquals('start?', $redirectUrl);

        //newItem is an stdClass
        $newItem = oxRegistry::getSession()->getVariable('_newitem');
        $this->assertEquals($this->testArticleId, $newItem->sId);
        $this->assertEquals($buyAmount, $newItem->dAmount);

        $basket = $this->getSession()->getBasket();
        $basket->calculateBasket(true); //calls oxBasket::afterUpdate
        //only one different article but 20 items in basket
        $this->assertEquals(1, $basket->getProductsCount());
        $this->assertEquals($buyAmount, $basket->getItemsCount());

        //without basket reservation there is no stock change when articles are
        //but into basket
        $this->assertEquals($stock, $this->getStock());

        $this->checkContents($basket, $buyAmount);

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
        $this->markTestSkipped();

        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $stock = 60;
        $buyAmount = $stock + 10;

        //not orderable if out of stock
        $this->setStockFlag(3);

        $this->setStock($stock);
        $this->assertEquals($stock, $this->getStock());

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
        $this->markTestSkipped();

        $stock = 60;
        $buyAmount = 20;

        $this->setStock($stock);
        $this->assertEquals($stock, $this->getStock());

        $basket = oxRegistry::getSession()->getBasket();
        $this->assertEquals(0, $basket->getBasketSummary()->iArticleCount);

        $this->setSessionParam('basketReservationToken', null);
        $this->assertNull(oxRegistry::getSession()->getVariable('_newitem'));

        //try to be as close to usual checkout as possible
        $basketComponent = oxNew('oxcmp_basket');
        $redirectUrl = $basketComponent->tobasket($this->testArticleId, $buyAmount);
        $this->assertEquals('start?', $redirectUrl);

        //newItem is an stdClass
        $newItem = oxRegistry::getSession()->getVariable('_newitem');
        $this->assertEquals($this->testArticleId, $newItem->sId);
        $this->assertEquals($buyAmount, $newItem->dAmount);

        $basket = $this->getSession()->getBasket();
        $basket->calculateBasket(true); //calls oxBasket::afterUpdate

        //article stock is reduced in database due to reservation
        $this->assertEquals($stock-$buyAmount, $this->getStock());

        $this->checkContents($basket, $buyAmount);
    }

    /**
     * Mode is basket reservation with timeout.
     * Finalize the order.
     */
    public function testPlaceOrderWithBasketTimeout()
    {
        $stock     = 60;
        $buyAmount = 20;

        $this->setStock($stock);
        $this->assertEquals($stock, $this->getStock());

        $basket = oxRegistry::getSession()->getBasket();
        $this->assertEquals(0, $basket->getBasketSummary()->iArticleCount);

        $this->setSessionParam('basketReservationToken', null);
        $this->assertNull(oxRegistry::getSession()->getVariable('_newitem'));

        //try to be as close to usual checkout as possible
        $basketComponent = oxNew('oxcmp_basket');
        $redirectUrl     = $basketComponent->tobasket($this->testArticleId, $buyAmount);
        $this->assertEquals('start?', $redirectUrl);

        $basket = $this->getSession()->getBasket();
        $basket->setPayment('oxidinvoice');

        $user = oxNew('oxUser');
        $user->load(self::TEST_USER_OXID);

        $order = $this->getOrder();
        $result = $order->validateOrder($basket, $user);

        $this->markTestIncomplete('some order data not yet ok ORDER_STATE_INVALIDDElADDRESSCHANGED');
        $this->assertEquals(oxOrder::ORDER_STATE_OK, $result);

        //$blRecalculatingOrder = true;
       // $result = $order->finalizeOrder($basket, $user, $blRecalculatingOrder);

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
        $articleParent->Load(self::SOURCE_ARTICLE_PARENT_ID);
        $articleParent->setId($this->testArticleParentId);
        $articleParent->save();

        $article = oxNew('oxarticle');
        $article->disableLazyLoading();
        $article->Load(self::SOURCE_ARTICLE_ID);
        $article->setId($this->testArticleId);
        $article->oxarticles__oxparentid = new oxField($this->testArticleParentId, oxField::T_RAW);
        $article->save();

    }

    /**
     * Get order object with test oxidid (leading underscore).
     *
     * @return object oxOrder
     */
    private function getOrder()
    {
        $this->testOrderId = substr_replace( oxUtilsObject::getInstance()->generateUId(), '_', 0, 1 );
        $order = oxNew('oxorder');
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
     * @param oxBasket $basket
     */
    private function checkContents(oxBasket $basket, $expectedAmount)
    {
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
}
