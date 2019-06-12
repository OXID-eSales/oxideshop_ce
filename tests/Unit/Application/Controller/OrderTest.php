<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\EshopCommunity\Application\Model\Payment;
use oxOutOfStockException;
use \oxUtils;
use \oxUtilsObject;
use \oxOrder;
use \oxPayment;
use \Exception;
use \oxException;
use \oxField;
use \oxRegistry;
use \oxTestModules;

/**
 * Test oxUtils module.
 */
class UtilsHelper extends oxUtils
{

    /**
     * Throw an exeption instead of redirect to page.
     *
     * @param string $sUrl url
     * @param boolean $blAddRedirectParam add redirect param
     * @param integer $iHeaderCode header code
     *
     * @throws Exception
     *
     * @return null
     */
    public function redirect($sUrl, $blAddRedirectParam = true, $iHeaderCode = 301)
    {
        throw new Exception($sUrl);
    }
}

/**
 * Test oxUtilsObject module.
 */
class UtilsObjectHelper extends oxUtilsObject
{
    /**
     * Always generate fixed uid.
     *
     * @return string
     */
    public function generateUID()
    {
        return "testUID";
    }
}

/**
 * Test oxOrdert module.
 */
class OrderHelper extends oxOrder
{
    /**
     * Skip finalizeOrder.
     *
     * @param object $oBasket              basket object
     * @param object $oUser                user object
     * @param bool   $blRecalculatingOrder Recalculating Order
     *
     * @return boolean
     */
    public function finalizeOrder(\OxidEsales\Eshop\Application\Model\Basket $oBasket, $oUser, $blRecalculatingOrder = false)
    {
        return 1;
    }

    /**
     * Skip validateStock.
     *
     * @param object $oBasket basket object
     *
     * @return boolean
     */
    public function validateStock($oBasket)
    {
        return true;
    }
}

/**
 * Test oxPayment module.
 */
class PaymentHelper extends oxPayment
{
    public static $dBasketPrice = null;

    /**
     * Skip isValidPayment and change $dBasketPrice.
     *
     * @param array  $aDynvalue    Dynamic values
     * @param string $sShopId      Shop id
     * @param object $oUser        User object
     * @param double $dBasketPrice Basket price
     * @param string $sShipSetId   Shipping set id
     *
     * @return boolean
     */
    public function isValidPayment($aDynvalue, $sShopId, $oUser, $dBasketPrice, $sShipSetId)
    {
        self::$dBasketPrice = $dBasketPrice;
        return true;
    }
}

/**
 * Testing oxorder class.
 */
class OrderTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        oxTestModules::addFunction('oxSeoEncoderManufacturer', '_saveToDb', '{return null;}');

        $oUser = oxNew('oxUser');
        $oUser->setId('_testUserId');
        $oUser->save();

        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\UtilsHelper::class, 'oxUtils');
        PaymentHelper::$dBasketPrice = null;
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        //remove data from db
        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxaddress');
        $this->cleanUpTable('oxobject2group', 'oxobjectid');

        parent::tearDown();
    }

    /**
     * Test is Wrapping
     *
     * @return null
     */
    public function testIsWrapping()
    {
        $oView = oxNew('order');
        $this->assertTrue($oView->isWrapping());
    }

    /**
     * Testing init() method
     *
     * @return null
     */
    public function testInit()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('bl_perfCalcVatOnlyForBasketOrder', true);

        $oOrder = oxNew("order");
        $oOrder->init();

        //test reseting to false config var bl_perfCalcVatOnlyForBasketOrder
        $this->assertEquals(false, $oConfig->getConfigParam('bl_perfCalcVatOnlyForBasketOrder'));

        //test template var
        $this->assertEquals("page/checkout/order.tpl", $oOrder->getTemplateName());
    }


    /**
     * Testing init() method - check if init forces basket recalculation
     * by calling basket onUpdate() method
     *
     * @return null
     */
    public function testInitForcesBasketRecalculation()
    {
        $oConfig = $this->getConfig();
        $mySession = oxRegistry::getSession();

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('onUpdate'));
        $oBasket->expects($this->once())
            ->method('onUpdate');

        //basket name in session will be "basket"
        $oConfig->setConfigParam('blMallSharedBasket', 1);

        //setting basket to session
        $mySession->setBasket($oBasket);
        //$this->getSession()->setVariable( 'basket', $oBasket );

        $oOrder = oxNew("order");

        $oOrder->init();
    }

    /**
     * Testing render() - if basket is not set, visitor shoud be redirected to shop home url
     *
     * @return null
     */
    public function testRenderWhenNoBasketExist()
    {
        $oConfig = $this->getConfig();
        $mySession = oxRegistry::getSession();

        //basket name in session will be "basket"
        $oConfig->setConfigParam('blMallSharedBasket', 1);

        //no basket, no user
        $mySession->setBasket(null);
        //$this->getSession()->setVariable( 'basket', null );
        $this->getSession()->setVariable('usr', null);

        $oOrder = oxNew("order");

        try {
            $oOrder->render();
        } catch (Exception $e) {
            $this->assertEquals($oConfig->getShopHomeURL(), $e->getMessage());
        }
    }

    /**
     * Testing render() - if active user not set and basket not empty,
     * visitor shoud be redirected to basket step
     *
     * @return null
     */
    public function testRenderWhenNoActiveUserExistWithBasket()
    {
        $sRedirUrl = $this->getConfig()->getShopHomeURL() . 'cl=basket';
        $this->expectException('oxException');
        $this->expectExceptionMessage($sRedirUrl);

        oxTestModules::addFunction('oxUtils', 'redirect($url, $blAddRedirectParam = true, $iHeaderCode = 301)', '{throw new oxException($url);}');
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getProductsCount'));
        $oB->expects($this->once())->method('getProductsCount')->will($this->returnValue(1));

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasketReservations', 'getBasket'));
        $oS->expects($this->any())->method('getBasket')->will($this->returnValue($oB));

        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getSession', 'getUser'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));
        $oO->expects($this->any())->method('getUser')->will($this->returnValue(null));
        $oO->render();
    }

    /**
     * Testing render() - if active user not set and basket is empty,
     * visitor shoud be redirected to basket step
     *
     * @return null
     */
    public function testRenderWhenNoActiveUserExistNoBasket()
    {
        $sRedirUrl = $this->getConfig()->getShopHomeURL();
        $this->expectException('oxException');
        $this->expectExceptionMessage($sRedirUrl);

        oxTestModules::addFunction('oxUtils', 'redirect($url, $blAddRedirectParam = true, $iHeaderCode = 301)', '{throw new oxException($url);}');
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $oB = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getProductsCount'));
        $oB->expects($this->any())->method('getProductsCount')->will($this->returnValue(0));

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasketReservations', 'getBasket'));
        $oS->expects($this->any())->method('getBasket')->will($this->returnValue($oB));

        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getSession', 'getUser'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));
        $oO->expects($this->any())->method('getUser')->will($this->returnValue(null));
        $oO->render();
    }

    /**
     * Testing render() - if user and basket are set, but basket is empty,
     * visitor shoud be redirected to shop home url
     *
     * @return null
     */
    public function testRenderWhenBasketIsEmpty()
    {
        $oConfig = $this->getConfig();
        $mySession = oxRegistry::getSession();

        //basket name in session will be "basket"
        $oConfig->setConfigParam('blMallSharedBasket', 1);

        $oBasket = oxNew(\OxidEsales\Eshop\Application\Model\Basket::class);
        $mySession->setBasket($oBasket);
        //$this->getSession()->setVariable( 'basket', $oBasket );
        $this->getSession()->setVariable('usr', 'oxdefaultadmin');

        $oOrder = oxNew("order");

        try {
            $oOrder->render();
        } catch (Exception $e) {
            $this->assertEquals($oConfig->getShopHomeURL(), $e->getMessage());
        }
    }

    /**
     * Testing render() - if user and basket are set, basket not empty,
     * but payment is not set, visitor shoud be redirected to payment select page
     *
     * @return null
     */
    public function testRenderWhenPaymentIsEmpty()
    {
        $oConfig = $this->getConfig();
        $mySession = oxRegistry::getSession();

        //basket name in session will be "basket"
        $oConfig->setConfigParam('blMallSharedBasket', 1);

        $oBasket = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Basket::class)->getMock();
        $oBasket->method('getProductsCount')->willReturn(5);

        $mySession->setBasket($oBasket);
        //$this->getSession()->setVariable( 'basket', $oBasket );
        $this->getSession()->setVariable('usr', 'oxdefaultadmin');

        $oOrder = oxNew("order");

        try {
            $oOrder->render();
        } catch (Exception $e) {
            $this->assertEquals($oConfig->getShopHomeURL() . '&cl=payment', $e->getMessage());
        }
    }

    /**
     * Testing render() - view data values
     *
     * @return null
     */
    public function testRender()
    {
        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\PaymentHelper::class, Payment::class, true);

        $config = $this->getConfig();
        $session = oxRegistry::getSession();

        //basket name in session will be "basket"
        $config->setConfigParam('blMallSharedBasket', 1);
        $config->setConfigParam('iMinOrderPrice', false);

        $price = oxNew('oxPrice');
        $price->setPrice(100, 19);

        $basketArticles = array(1, 2, 3);

        $basket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getPrice', 'getProductsCount', 'getBasketArticles'));
        $basket->expects($this->any())->method('getPrice')->will($this->returnValue($price));
        $basket->expects($this->any())->method('getProductsCount')->will($this->returnValue(1));
        $basket->expects($this->any())->method('getBasketArticles')->will($this->returnValue($basketArticles));

        //setting order info to session
        $basket->setPayment('oxidcashondel');
        $this->getSession()->setVariable('sShipSet', 'oxidstandard');
        $session->setBasket($basket);
        $this->getSession()->setVariable('usr', 'oxdefaultadmin');
        $this->getSession()->setVariable('deladrid', 'null');
        $this->getSession()->setVariable('ordrem', 'testRemark');

        //setting some config data
        $config->setConfigParam('blConfirmAGB', '1');
        $config->setConfigParam('blConfirmCustInfo', '1');

        $utilsObjectMock = new UtilsObjectHelper();
        $order = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, ['getUtilsObjectInstance']);
        $order->expects($this->any())->method('getUtilsObjectInstance')->will($this->returnValue($utilsObjectMock));

        //checking return value
        $this->assertEquals('page/checkout/order.tpl', $order->render());

        //checking view data
        $this->assertEquals('oxidcashondel', $order->getPayment()->getId());
        $this->assertEquals($basketArticles, $order->getBasketArticles());
        $this->assertEquals('testRemark', $order->getOrderRemark());
        $this->assertEquals('oxidstandard', $order->getShipSet()->getId());
        $this->assertEquals(1, $order->isConfirmAGBActive());
        $this->assertEquals("execute", $order->getExecuteFnc());

        //checking if new order id was generated
        $this->assertEquals('testUID', $session->getVariable('sess_challenge'));
    }

    /**
     * Testing execute() -  without order rules confirmation "ord_agb"
     *
     * @return null
     */
    public function testExecuteWithoutAGB()
    {
        $oConfig = $this->getConfig();

        $oConfig->setConfigParam('blConfirmAGB', 1);
        $this->setRequestParameter('ord_agb', null);

        $oConfig->setConfigParam('blConfirmCustInfo', 1);
        $this->setRequestParameter('ord_custinfo', 1);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getEncodedDeliveryAddress'));
        $oUser->expects($this->any())->method('getEncodedDeliveryAddress')->will($this->returnValue('encodedAddress'));

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oS->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getSession'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));
        $oO->setUser($oUser);

        $this->assertNull($oO->execute());

        $this->assertEquals(1, $oO->isConfirmAGBError());
    }

    /**
     * Testing execute() - without needed information to create proper user.
     *
     * @return null
     */
    public function testExecuteWithoutCustInfo()
    {
        $oConfig = $this->getConfig();

        $oConfig->setConfigParam('blConfirmAGB', 1);
        $this->setRequestParameter('ord_agb', 1);

        $oConfig->setConfigParam('blConfirmCustInfo', 1);
        // test new tpl, when option (ord_custinfo) was removed
        $this->setRequestParameter('ord_custinfo', null);
        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oS->expects($this->any())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getSession'));
        $oOrder->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertEquals('user', $oOrder->execute());
        // test former tpl. If ord_custinfo is not confirmed
        $this->setRequestParameter('ord_custinfo', 0);

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oS->expects($this->any())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getSession'));
        $oOrder->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertSame('user', $oOrder->execute());
    }

    /**
     * Testing execute()
     *
     * @return null
     */
    public function testExecute()
    {
        $this->setupConfigForOrderExecute();

        //setting active user
        $this->getSession()->setVariable('usr', '_testUserId');

        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\OrderHelper::class, 'oxorder');

        $oPrice = $this->getPriceForOrderExecute();

        //setting basket info
        $oBasket = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Basket::class)->getMock();
        $oBasket->method('getProductsCount')->willReturn(1);
        $oBasket->method('getPrice')->willReturn($oPrice);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('onOrderExecute'));
        $oUser->expects($this->once())->method('onOrderExecute')->will($this->returnValue(null));

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oSession->setBasket($oBasket);

        //on order success must return next step vale
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('_getNextStep', 'getSession', 'getUser'));
        $oOrder->expects($this->any())->method('_getNextStep')->will($this->returnValue('nextStepValue'));
        $oOrder->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oOrder->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals('nextStepValue', $oOrder->execute());
    }

    /**
     * Testing execute() when order validating stock fails
     *
     * @return null
     */
    public function testExecuteWithWrongStockThrowsException()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{throw $aA[0];}');

        $this->setupConfigForOrderExecute();
        $this->getPriceForOrderExecute();

        $product = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array("checkForStock"));
        $product->expects($this->any())->method("checkForStock")->will($this->returnValue($product));

        $basketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array("getArticle", "getAmount"));
        $basketItem->expects($this->any())->method("getArticle")->will($this->returnValue($product));
        $basketItem->expects($this->any())->method("getAmount")->will($this->returnValue(999));

        //setting basket info
        $basket = $this->getBasketMock($basketItem);

        $session = $this->getSessionMock($basket);
        $session->expects($this->any())->method("checkSessionChallenge")->will($this->returnValue(true));

        $order = $this->getOrderMock($session);

        $this->expectException('oxOutOfStockException');
        $this->assertNull($order->execute());
    }

    /**
     * Testing execute() when order validating stock fails
     *
     * @return null
     */
    public function testExecuteWithWrongStockCallsMethodsInRightOrder()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{throw $aA[0];}');

        $this->setupConfigForOrderExecute();
        $this->getPriceForOrderExecute();

        $product = $this->getMock(Article::class, ['checkForStock']);
        $product
            ->method("checkForStock")
            ->with($this->equalTo(999))
            ->will($this->returnValue($product));

        $basketItem = $this->getMock(BasketItem::class, ['getArticle', 'getAmount']);
        $basketItem->method('getArticle')->will($this->returnValue($product));
        $basketItem->method('getAmount')->will($this->returnValue(999));

        $basket = $this->getBasketMock($basketItem);

        $session = $this->getSessionMock($basket);
        $session->method('checkSessionChallenge')->will($this->returnValue(true));

        $order = $this->getOrderMock($session);
        $order->expects($this->never())->method('_getNextStep');

        $this->expectException('oxOutOfStockException');
        $this->assertNull($order->execute());
    }

    /**
     * Testing execute() - on success user must be marked as shop customer
     * by calling oxuser::onOrderExecute()
     *
     * @return null
     */
    public function testExecuteOnSuccessMarksUser()
    {
        $this->setupConfigForOrderExecute();

        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\OrderHelper::class, 'oxorder');

        $oPrice = $this->getPriceForOrderExecute();

        $oBasket = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Basket::class)->getMock();
        $oBasket->method('getPrice')->willReturn($oPrice);
        $oBasket->method('getProductsCount')->willReturn(1);


        // check if onOrderExecute is called once
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('onOrderExecute'));
        $oUser->expects($this->once())->method('onOrderExecute')->with($this->equalTo($oBasket), $this->equalTo(1))->will($this->returnValue(null));
        $this->assertTrue($oUser->load('_testUserId'));
        //on order success must return next step vale
        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oS->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('_getNextStep', 'getSession'));
        $oOrder->expects($this->any())->method('_getNextStep')->will($this->returnValue('nextStepValue'));
        $oOrder->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oOrder->setUser($oUser);

        $oS->setBasket($oBasket);

        $this->assertEquals('nextStepValue', $oOrder->execute());
    }

    /**
     * Testing getNextStep()
     *
     * @return null
     */
    public function testGetNextStep()
    {
        $oOrder = $this->getProxyClass('order');

        // set no param
        $res = $oOrder->UNITgetNextStep(null);
        $this->assertEquals("thankyou", $res);

        // email error
        $res = $oOrder->UNITgetNextStep(0);
        $this->assertEquals("thankyou?mailerror=1", $res);

        // if success
        $res = $oOrder->UNITgetNextStep(1);
        $this->assertEquals("thankyou", $res);

        // no authentication
        $res = $oOrder->UNITgetNextStep(2);
        $this->assertEquals("payment?payerror=2", $res);

        // reload blocker activ
        $res = $oOrder->UNITgetNextStep(3);
        $this->assertEquals("thankyou", $res);

        // reload blocker activ
        $res = $oOrder->UNITgetNextStep(8);
        $this->assertEquals("order", $res);

        // other payment error
        $res = $oOrder->UNITgetNextStep(6);
        $this->assertEquals("payment?payerror=6", $res);

        // address changed
        $res = $oOrder->UNITgetNextStep(7);
        $this->assertEquals("order?iAddressError=1", $res);

        // error text
        $res = $oOrder->UNITgetNextStep("Test Error");
        $this->assertEquals("payment?payerror=-1&payerrortext=Test+Error", $res);
    }

    /**
     * Testing getters and setters
     *
     * @return null
     */
    public function testGetBasket()
    {
        $mySession = oxRegistry::getSession();

        $oBasket = oxNew(\OxidEsales\Eshop\Application\Model\Basket::class);
        $this->getConfig()->setConfigParam('blMallSharedBasket', 1);
        //$this->getSession()->setVariable( 'basket', $oBasket );
        $mySession->setBasket($oBasket);
        $oOrder = $this->getProxyClass('order');
        $this->assertEquals($oBasket, $oOrder->getBasket());
    }

    /**
     * Test get payment.
     *
     * @return null
     */
    public function testGetPayment()
    {
        $mySession = oxRegistry::getSession();
        oxTestModules::addFunction('oxpayment', 'isValidPayment', '{return true;}');

        //basket name in session will be "basket"
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blMallSharedBasket', 1);
        $oConfig->setConfigParam('iMinOrderPrice', false);

        $oBasket = oxNew(\OxidEsales\Eshop\Application\Model\Basket::class);
        $oBasket->setPayment('oxidcashondel');

        $mySession->setBasket($oBasket);

        $this->getSession()->setVariable('sShipSet', 'oxidstandard');
        $this->getSession()->setVariable('usr', 'oxdefaultadmin');
        $this->getSession()->setVariable('deladrid', 'null');
        $oOrder = $this->getProxyClass('order');
        $this->assertEquals('oxidcashondel', $oOrder->getPayment()->getId());
    }

    /**
     * Test if method for validating payment uses basket price
     * getted from \OxidEsales\Eshop\Application\Model\Basket::getPriceForPayment()
     *
     * @return null
     */
    public function testGetPayment_userBasketPriceForPayment()
    {
        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getPriceForPayment', 'getPaymentId'));
        $oBasket->expects($this->once())->method('getPriceForPayment')->will($this->returnValue(100));
        $oBasket->expects($this->once())->method('getPaymentId')->will($this->returnValue('oxidinvoice'));

        PaymentHelper::$dBasketPrice = null;
        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\PaymentHelper::class, 'oxPayment');

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getBasket'));
        $oOrder->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $oOrder->getPayment();

        $this->assertEquals(100, PaymentHelper::$dBasketPrice);
    }


    /**
     * Test get execute function.
     *
     * @return null
     */
    public function testGetExecuteFnc()
    {
        $oOrder = $this->getProxyClass('order');
        $this->assertEquals('execute', $oOrder->getExecuteFnc());
    }

    /**
     * Test get order remark.
     *
     * @return null
     */
    public function testGetOrderRemark()
    {
        $oOrder = $this->getProxyClass('order');
        $this->getSession()->setVariable('ordrem', 'test');
        $this->assertEquals('test', $oOrder->getOrderRemark());
    }

    /**
     * Test get basket article.
     *
     * @return null
     */
    public function testGetBasketArticles()
    {
        $aBasketArticles = array(1, 2, 3);

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getProductsCount', 'getBasketArticles'));
        $oBasket->expects($this->any())->method('getProductsCount')->will($this->returnValue(1));
        $oBasket->expects($this->any())->method('getBasketArticles')->will($this->returnValue($aBasketArticles));
        //basket name in session will be "basket"
        $this->getConfig()->setConfigParam('blMallSharedBasket', 1);
        //setting order info to session
        //$this->getSession()->setVariable( 'basket', $oBasket );
        $mySession = oxRegistry::getSession();
        $mySession->setBasket($oBasket);

        $oOrder = $this->getProxyClass("order");

        $this->assertEquals($aBasketArticles, $oOrder->getBasketArticles());
    }

    /**
     * Test get delivery address.
     *
     * @return null
     */
    public function testGetDelAddress()
    {
        $this->setRequestParameter('deladrid', '_testDelAddrId');

        $oDelAdress = oxNew('oxBase');
        $oDelAdress->init('oxaddress');
        $oDelAdress->setId('_testDelAddrId');
        $oDelAdress->oxaddress__oxuserid = new oxField('_testUserId', oxField::T_RAW);
        $oDelAdress->oxaddress__oxcountryid = new oxField('a7c40f631fc920687.20179984', oxField::T_RAW);
        $oDelAdress->save();

        $oOrder = oxNew('order');
        $oDeliveryAddress = $oOrder->getDelAddress();

        $this->assertEquals('_testDelAddrId', $oDeliveryAddress->getId());
        $this->assertEquals('_testUserId', $oDeliveryAddress->oxaddress__oxuserid->value);
        $this->assertEquals('Deutschland', $oDeliveryAddress->oxaddress__oxcountry->value);
    }

    /**
     * Test get shipping set.
     *
     * @return null
     */
    public function testGetShipSet()
    {
        //basket name in session will be "basket"
        $this->getConfig()->setConfigParam('blMallSharedBasket', 1);
        $oBasket = oxNew(\OxidEsales\Eshop\Application\Model\Basket::class);
        $oBasket->setPayment('oxidcashondel');

        $mySession = oxRegistry::getSession();
        $mySession->setBasket($oBasket);

        //$this->getSession()->setVariable( 'basket', $oBasket );
        $this->getSession()->setVariable('sShipSet', 'oxidstandard');
        $oOrder = $this->getProxyClass("order");

        $this->assertEquals('oxidstandard', $oOrder->getShipSet()->getId());
    }

    /**
     * Test is confirm AGB active.
     *
     * @return null
     */
    public function testIsConfirmAGBActive()
    {
        $oOrder = $this->getProxyClass("order");

        $this->getConfig()->setConfigParam('blConfirmAGB', true);
        $this->assertTrue($oOrder->isConfirmAGBActive());
    }

    /**
     * Test is confirm AGB error.
     *
     * @return null
     */
    public function testIsConfirmAGBError()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getEncodedDeliveryAddress'));
        $oUser->expects($this->any())->method('getEncodedDeliveryAddress')->will($this->returnValue('encodedAddress'));

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oS->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getSession'));
        $oOrder->expects($this->any())->method('getSession')->will($this->returnValue($oS));
        $oOrder->setUser($oUser);

        $oConfig = $this->getConfig();

        $oConfig->setConfigParam('blConfirmAGB', 1);
        $this->setRequestParameter('ord_agb', null);

        $oConfig->setConfigParam('blConfirmCustInfo', 1);
        $this->setRequestParameter('ord_custinfo', 1);
        $oOrder->execute();
        $this->assertEquals(1, $oOrder->isConfirmAGBError());
    }

    /**
     * Test is confirm AGB error.
     *
     * @return null
     */
    public function testIsConfirmAGBErrorWhenBasketHasIntangibleProducts()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getEncodedDeliveryAddress'));
        $oUser->expects($this->any())->method('getEncodedDeliveryAddress')->will($this->returnValue('encodedAddress'));

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('hasArticlesWithIntangibleAgreement'));
        $oBasket->expects($this->any())->method('hasArticlesWithIntangibleAgreement')->will($this->returnValue(true));

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getSession', 'getBasket'));
        $oOrder->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oOrder->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));
        $oOrder->setUser($oUser);

        $oConfig = $this->getConfig();

        $oConfig->setConfigParam('blConfirmAGB', 0);
        $oConfig->setConfigParam('blEnableIntangibleProdAgreement', 1);
        $this->setRequestParameter('oxdownloadableproductsagreement', null);

        $oOrder->execute();
        $this->assertEquals(1, $oOrder->isConfirmAGBError());
    }

    /**
     * Test is confirm AGB error.
     *
     * @return null
     */
    public function testIsConfirmAGBErrorWhenBasketHasDownloadableProducts()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getEncodedDeliveryAddress'));
        $oUser->expects($this->any())->method('getEncodedDeliveryAddress')->will($this->returnValue('encodedAddress'));

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('hasArticlesWithDownloadableAgreement'));
        $oBasket->expects($this->any())->method('hasArticlesWithDownloadableAgreement')->will($this->returnValue(true));

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getSession', 'getBasket'));
        $oOrder->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oOrder->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));
        $oOrder->setUser($oUser);

        $oConfig = $this->getConfig();

        $oConfig->setConfigParam('blConfirmAGB', 0);
        $oConfig->setConfigParam('blEnableIntangibleProdAgreement', 1);
        $this->setRequestParameter('oxdownloadableproductsagreement', null);

        $oOrder->execute();
        $this->assertEquals(1, $oOrder->isConfirmAGBError());
    }

    /**
     * Test show order button on top.
     *
     * @return null
     */
    public function testShowOrderButtonOnTop()
    {
        $oOrder = $this->getProxyClass("order");
        $this->getConfig()->setConfigParam('blShowOrderButtonOnTop', true);
        $this->assertTrue($oOrder->showOrderButtonOnTop());
    }

    public function testExecuteChecksSessionChallenge()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getEncodedDeliveryAddress'));
        $oUser->expects($this->any())->method('getEncodedDeliveryAddress')->will($this->returnValue('encodedAddress'));

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oS->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(false));
        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getConfig', 'getSession'));
        $oO->expects($this->never())->method('getConfig')->will($this->returnValue(false));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));
        $oO->setUser($oUser);

        $this->assertSame(null, $oO->execute());
        // reverse behavriour when checkSessionChallenge is true is tested in execute functionality tests
    }

    /**
     * Test oxViewConfig::getShowGiftWrapping() affection
     *
     * @return null
     */
    public function testIsWrappingIfWrappingIsOff()
    {
        $oCfg = $this->getMock("stdClass", array("getShowGiftWrapping"));
        $oCfg->expects($this->once())->method('getShowGiftWrapping')->will($this->returnValue(false));

        oxTestModules::addFunction("oxwrapping", "__construct", '{throw new Exception("wrapping should not be constructed");}');

        $oTg = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array("getViewConfig"));
        $oTg->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));

        $this->assertSame(false, $oTg->isWrapping());
    }


    public function testRenderDoesNotCleanReservationsIfOff()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasketReservations'));
        $oS->expects($this->never())->method('getBasketReservations');

        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getSession'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        try {
            $oO->render();
        } catch (Exception $e) {
            $this->assertEquals($this->getConfig()->getShopHomeURL(), $e->getMessage());
        }
    }

    public function testRenderDoesCleanReservationsIfOn()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', array('renewExpiration'));
        $oR->expects($this->once())->method('renewExpiration')->will($this->returnValue(null));

        $oS = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));

        $oO = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array('getSession'));
        $oO->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        try {
            $oO->render();
        } catch (Exception $e) {
            $this->assertEquals($this->getConfig()->getShopHomeURL() . 'cl=basket', $e->getMessage());
        }
    }

    /**
     * Testing Order::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oOrder = oxNew('Order');

        $this->assertEquals(1, count($oOrder->getBreadCrumb()));
    }

    /**
     * Testing address error getters
     *
     * @return null
     */
    public function testGetAddressError()
    {
        $this->setRequestParameter('iAddressError', 1);
        $oOrder = oxNew('Order');
        $this->assertEquals(1, $oOrder->getAddressError());
    }

    /**
     * Testing address encoding
     *
     * @return null
     */
    public function testGetDeliveryAddressMD5()
    {
        $oDelAddress = oxNew('oxAddress');
        $oDelAddress->init('oxaddress');
        $oDelAddress->setId('_testDelAddrId');
        $oDelAddress->oxaddress__oxcompany = new oxField("company");
        $oDelAddress->save();

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getEncodedDeliveryAddress'));
        $oUser->expects($this->any())->method('getEncodedDeliveryAddress')->will($this->returnValue('encodedAddress'));

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, array("getUser"));
        $oOrder->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals($oUser->getEncodedDeliveryAddress(), $oOrder->getDeliveryAddressMD5());

        $this->getSession()->setVariable('deladrid', '_testDelAddrId');

        $this->assertEquals($oUser->getEncodedDeliveryAddress() . $oDelAddress->getEncodedDeliveryAddress(), $oOrder->getDeliveryAddressMD5());
    }

    private function setupConfigForOrderExecute()
    {
        $config = $this->getConfig();

        //basket name in session will be "basket"
        $config->setConfigParam('blMallSharedBasket', 1);

        //order rules checking
        $config->setConfigParam('blConfirmAGB', 0);
        $config->setConfigParam('blConfirmCustInfo', 0);
    }

    private function getPriceForOrderExecute()
    {
        $price = oxNew('oxPrice');
        $price->setPrice(100, 19);
        return $price;
    }

    /**
     * @param $session
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getOrderMock($session)
    {
        $user = oxNew('oxUser');
        $user->load('_testUserId');

        $order = $this->getMock(\OxidEsales\Eshop\Application\Controller\OrderController::class, ['_getNextStep', "getSession", "getUser", "getPayment"]);
        $order->expects($this->any())->method('getSession')->will($this->returnValue($session));
        $order->expects($this->any())->method('getUser')->will($this->returnValue($user));
        $order->expects($this->any())->method('getPayment')->will($this->returnValue(true));
        return $order;
    }

    /**
     * @param $basketItem
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getBasketMock($basketItem)
    {
        $basket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ["getShippingId", "getPaymentId", "getProductsCount", "getContents"]);
        $basket->expects($this->any())->method("getProductsCount")->will($this->returnValue(1));
        $basket->expects($this->any())->method("getContents")->will($this->returnValue(['xxx' => $basketItem]));
        return $basket;
    }

    /**
     * @param $basket
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getSessionMock($basket)
    {
        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ["getBasket", 'checkSessionChallenge']);
        $session->expects($this->any())->method("getBasket")->will($this->returnValue($basket));
        return $session;
    }
}
