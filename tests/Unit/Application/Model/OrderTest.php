<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use Exception;
use oxArticleHelper;
use \oxdeliverylist;
use oxEmailHelper;
use \oxField;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\UserPayment;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\UtilsObject;
use oxOrder;
use \stdClass;
use \oxDb;
use \oxRegistry;
use \oxTestModules;
use OxidEsales\EshopCommunity\Application\Model\PaymentGateway;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxEmailHelper.php';

class modoxdeliverylist_oxorder extends oxdeliverylist
{

    public $aTestDeliveriesSetsRetValue = array('testShipSetId1', 'testShipSetId2');

    public function getDeliveryList($oBasket, $oUser = null, $sDelCountry = null, $sDelSet = null)
    {
        if ($this->_blCollectFittingDeliveriesSets) {
            return $this->aTestDeliveriesSetsRetValue;
        } else {
            return null;
        }
    }
}

class OrderTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setup()
    {
        parent::setUp();
        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', true);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRemClassModule('Unit\Application\Model\modoxdeliverylist_oxorder');
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxorderarticles');
        $this->cleanUpTable('oxdeliveryset');
        $this->cleanUpTable('oxpayments');
        $this->cleanUpTable('oxwrapping');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxorderarticles');
        $this->cleanUpTable('oxuserbaskets');
        $this->cleanUpTable('oxuserbaskets', 'oxuserid');

        $this->cleanUpTable('oxuserbasketitems');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxpayments');

        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxdelivery');
        $this->cleanUpTable('oxdiscount');
        $this->cleanUpTable('oxobject2discount');
        $this->cleanUpTable('oxdel2delset');
        $this->cleanUpTable('oxobject2delivery');
        $this->cleanUpTable('oxselectlist');
        $this->cleanUpTable('oxobject2selectlist');

        $this->cleanUpTable('oxorderarticles', 'oxorderid');
        $this->cleanUpTable('oxuserpayments', 'oxuserid');
        $this->cleanUpTable('oxvouchers');
        $this->cleanUpTable('oxvouchers', 'oxorderid');
        $this->cleanUpTable('oxvoucherseries');

        $oDb = oxDb::getDb();
        $oDb->execute("delete from oxuserbasketitems");
        $oDb->execute("delete from oxuserbaskets");

        oxArticleHelper::cleanup();

        parent::tearDown();
    }

    /**
     * Use case (#0001410):
     *
     * If you change the billing adress
     * -> Shop Admin -> Administer order -> order -> Tab adress
     * and then the shipping cost of an order
     * -> Shop Admin -> Administer order -> order -> Main
     * the billing adress will be set to old one.
     *
     * Changes will be rejected.
     */
    public function testForUseCase()
    {
        //  var_dump($this->getConfigParam( 'aLanguageParams' ));

        $soxId = '_testOrderId';

        // writing test order
        $oOrder = oxNew('oxorder');
        $oOrder->setId($soxId);
        $oOrder->oxorder__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $oOrder->oxorder__oxuserid = new oxField("oxdefaultadmin");
        $oOrder->oxorder__oxbillcompany = new oxField("Ihr Firmenname");
        $oOrder->oxorder__oxbillemail = new oxField(oxADMIN_LOGIN);
        $oOrder->oxorder__oxbillfname = new oxField("Hans");
        $oOrder->oxorder__oxbilllname = new oxField("Mustermann");
        $oOrder->oxorder__oxbillstreet = new oxField("Musterstr");
        $oOrder->oxorder__oxbillstreetnr = new oxField("10");
        $oOrder->oxorder__oxbillcity = new oxField("Musterstadt");
        $oOrder->oxorder__oxbillcountryid = new oxField("a7c40f6320aeb2ec2.72885259");
        $oOrder->oxorder__oxbillzip = new oxField("79098");
        $oOrder->oxorder__oxbillsal = new oxField("Herr");
        $oOrder->oxorder__oxpaymentid = new oxField("1f53d82f6391b86db09786fd75b69cb9");
        $oOrder->oxorder__oxpaymenttype = new oxField("oxidcashondel");
        $oOrder->oxorder__oxtotalnetsum = new oxField(75.55);
        $oOrder->oxorder__oxtotalbrutsum = new oxField(89.9);
        $oOrder->oxorder__oxtotalordersum = new oxField(117.4);
        $oOrder->oxorder__oxdelcost = new oxField(20);
        $oOrder->oxorder__oxdelval = new oxField(0);
        $oOrder->oxorder__oxpaycost = new oxField(7.5);
        $oOrder->oxorder__oxcurrency = new oxField("EUR");
        $oOrder->oxorder__oxcurrate = new oxField(1);
        $oOrder->oxorder__oxdeltype = new oxField("oxidstandard");
        $oOrder->save();

        // writing test order product
        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->oxorderarticles__oxorderid = new oxField($soxId);
        $oOrderArticle->oxorderarticles__oxamount = new oxField(1);
        $oOrderArticle->oxorderarticles__oxartid = new oxField("1126");
        $oOrderArticle->oxorderarticles__oxartnum = new oxField("1126");
        $oOrderArticle->oxorderarticles__oxtitle = new oxField("test title");
        $oOrderArticle->oxorderarticles__oxnetprice = new oxField(75.5462184874);
        $oOrderArticle->oxorderarticles__oxbrutprice = new oxField(89.9);
        $oOrderArticle->oxorderarticles__oxvatprice = new oxField(14.3537815126);
        $oOrderArticle->oxorderarticles__oxvat = new oxField(19);
        $oOrderArticle->oxorderarticles__oxprice = new oxField(89.9);
        $oOrderArticle->oxorderarticles__oxnprice = new oxField(75.5462184874);
        $oOrderArticle->oxorderarticles__oxstock = new oxField(6);
        $oOrderArticle->oxorderarticles__oxordershopid = new oxField($this->getConfig()->getBaseShopId());
        $oOrderArticle->save();

        // updating delivery costs
        $oOrder = oxNew('oxOrder');
        $oOrder->load($soxId);
        $oOrder->oxorder__oxdelcost = new oxField(25);
        $oOrder->oxorder__oxbillstreet = new oxField("Teststr");
        $oOrder->reloadDelivery(false);
        $oOrder->reloadDiscount(false);
        $oOrder->recalculateOrder();

        // testing
        $oOrder = oxNew("oxorder");
        $oOrder->load($soxId);
        $this->assertEquals(25, $oOrder->oxorder__oxdelcost->value);
        $this->assertEquals("Teststr", $oOrder->oxorder__oxbillstreet->value);

    }

    public function testValidateOrder()
    {
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("validateStock", "validateDelivery", "validatePayment", "validateDeliveryAddress", "validateBasket"));
        $oOrder->expects($this->once())->method('validateStock');
        $oOrder->expects($this->once())->method('validateDelivery');
        $oOrder->expects($this->once())->method('validatePayment');
        $oOrder->expects($this->once())->method('validateDeliveryAddress');
        $oOrder->expects($this->once())->method('validateBasket');
        $this->assertNull($oOrder->validateOrder(0, 0));

        // stock check failed
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("validateStock", "validateDelivery", "validatePayment", "validateDeliveryAddress", "validateBasket"));
        $oOrder->expects($this->once())->method('validateStock')->will($this->returnValue("validateStock"));
        $oOrder->expects($this->never())->method('validateDelivery');
        $oOrder->expects($this->never())->method('validatePayment');
        $oOrder->expects($this->never())->method('validateDeliveryAddress');
        $oOrder->expects($this->never())->method('validateBasket');
        $this->assertEquals("validateStock", $oOrder->validateOrder(0, 0));

        // delivery check failed
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("validateStock", "validateDelivery", "validatePayment", "validateDeliveryAddress", "validateBasket"));
        $oOrder->expects($this->once())->method('validateStock');
        $oOrder->expects($this->once())->method('validateDelivery')->will($this->returnValue("validateDelivery"));;
        $oOrder->expects($this->never())->method('validatePayment');
        $oOrder->expects($this->never())->method('validateDeliveryAddress');
        $oOrder->expects($this->never())->method('validateBasket');
        $this->assertEquals("validateDelivery", $oOrder->validateOrder(0, 0));

        // payment check failed
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("validateStock", "validateDelivery", "validatePayment", "validateDeliveryAddress", "validateBasket"));
        $oOrder->expects($this->once())->method('validateStock');
        $oOrder->expects($this->once())->method('validateDelivery');
        $oOrder->expects($this->once())->method('validatePayment')->will($this->returnValue("validatePayment"));
        $oOrder->expects($this->never())->method('validateDeliveryAddress');
        $oOrder->expects($this->never())->method('validateBasket');
        $this->assertEquals("validatePayment", $oOrder->validateOrder(0, 0));

        // payment check failed
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("validateStock", "validateDelivery", "validatePayment", "validateDeliveryAddress", "validateBasket"));
        $oOrder->expects($this->once())->method('validateStock');
        $oOrder->expects($this->once())->method('validateDelivery');
        $oOrder->expects($this->once())->method('validatePayment');
        $oOrder->expects($this->once())->method('validateDeliveryAddress')->will($this->returnValue("validateDeliveryAddress", "validateBasket"));
        $oOrder->expects($this->never())->method('validateBasket');
        $this->assertEquals("validateDeliveryAddress", $oOrder->validateOrder(0, 0));

        // min basket price check failed
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("validateStock", "validateDelivery", "validatePayment", "validateDeliveryAddress", "validateBasket"));
        $oOrder->expects($this->once())->method('validateStock');
        $oOrder->expects($this->once())->method('validateDelivery');
        $oOrder->expects($this->once())->method('validatePayment');
        $oOrder->expects($this->once())->method('validateDeliveryAddress');
        $oOrder->expects($this->once())->method('validateBasket')->will($this->returnValue("validateBasket"));
        $this->assertEquals("validateBasket", $oOrder->validateOrder(0, 0));
    }

    public function testValidateDeliveryAddress()
    {
        //$oOrder = oxNew('oxorder');

        $oDelAddress = oxNew('oxaddress');
        $oDelAddress->oxaddress__oxcompany = new oxField("company");
        $oDelAddress->oxaddress__oxfname = new oxField("fname");
        $oDelAddress->oxaddress__oxlname = new oxField("lname");
        $oDelAddress->oxaddress__oxstreet = new oxField("street");
        $oDelAddress->oxaddress__oxstreetnr = new oxField("streetnr");
        $oDelAddress->oxaddress__oxaddinfo = new oxField("addinfo");
        $oDelAddress->oxaddress__oxcity = new oxField("city");
        $oDelAddress->oxaddress__oxcountryid = new oxField("countryid");
        $oDelAddress->oxaddress__oxstateid = new oxField("statid");
        $oDelAddress->oxaddress__oxzip = new oxField("zip");
        $oDelAddress->oxaddress__oxfon = new oxField("fon");
        $oDelAddress->oxaddress__oxfax = new oxField("fax");
        $oDelAddress->oxaddress__oxsal = new oxField("sal");

        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxcompany = new oxField("company");
        $oUser->oxuser__oxusername = new oxField("username");
        $oUser->oxuser__oxfname = new oxField("fname");
        $oUser->oxuser__oxlname = new oxField("lname");
        $oUser->oxuser__oxstreet = new oxField("street");
        $oUser->oxuser__oxstreetnr = new oxField("streetnr");
        $oUser->oxuser__oxaddinfo = new oxField("addinfo");
        $oUser->oxuser__oxustid = new oxField("ustid");
        $oUser->oxuser__oxcity = new oxField("city");
        $oUser->oxuser__oxcountryid = new oxField("countryid");
        $oUser->oxuser__oxstateid = new oxField("statid");
        $oUser->oxuser__oxzip = new oxField("zip");
        $oUser->oxuser__oxfon = new oxField("fon");
        $oUser->oxuser__oxfax = new oxField("fax");
        $oUser->oxuser__oxsal = new oxField("sal");

        $sUserAddress = $oUser->getEncodedDeliveryAddress();
        $sDelAddress = $oDelAddress->getEncodedDeliveryAddress();
        $this->setRequestParameter('sDeliveryAddressMD5', $sUserAddress . $sDelAddress);

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("getDelAddressInfo"));
        $oOrder->expects($this->any())->method('getDelAddressInfo')->will($this->returnValue($oDelAddress));

        $this->assertSame(0, $oOrder->ValidateDeliveryAddress($oUser));
    }

    public function testValidateDelivery()
    {
        $oOrder = oxNew('oxorder');

        // non existing delivery set
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array("getShippingId"));
        $oBasket->expects($this->once())->method("getShippingId")->will($this->returnValue("xxx"));

        $this->assertEquals(oxOrder::ORDER_STATE_INVALIDDELIVERY, $oOrder->validateDelivery($oBasket));

        // existing delivery set
        $sDelSetId = oxDb::getDb()->getOne('select oxid from oxdeliveryset where oxactive = 1');
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array("getShippingId"));
        $oBasket->expects($this->once())->method("getShippingId")->will($this->returnValue($sDelSetId));

        $this->assertNull($oOrder->validateDelivery($oBasket));
    }

    public function testValidatePaymentWhenPaymentIsValid()
    {
        $paymentModel = $this->getMock(Payment::class, ['isValidPayment']);
        $paymentModel
            ->method('isValidPayment')
            ->willReturn(true);

        UtilsObject::setClassInstance(Payment::class, $paymentModel);

        $order = $this->getMock(Order::class, ['getPaymentType']);
        $order
            ->method('getPaymentType')
            ->willReturn(
                oxNew(UserPayment::class)
            );

        $paymentId = oxDb::getDb()->getOne('select oxid from oxpayments where oxactive = 1');

        $basket = $this->getMock(Basket::class, array("getPaymentId"));
        $basket
            ->method("getPaymentId")
            ->willReturn($paymentId);

        $this->assertNull($order->validatePayment($basket));
    }

    public function testValidatePaymentWithWrongPaymentId()
    {
        $paymentModel = $this->getMock(Payment::class, ['isValidPayment']);
        $paymentModel
            ->method('isValidPayment')
            ->willReturn(true);

        UtilsObject::setClassInstance(Payment::class, $paymentModel);

        $order = $this->getMock(Order::class, ['getPaymentType']);
        $order
            ->method('getPaymentType')
            ->willReturn(
                oxNew(UserPayment::class)
            );

        $basket = $this->getMock(Basket::class, array("getPaymentId"));
        $basket
            ->method("getPaymentId")
            ->willReturn('wrongPaymentId');

        $this->assertEquals(
            oxOrder::ORDER_STATE_INVALIDPAYMENT,
            $order->validatePayment($basket)
        );
    }

    public function testValidatePaymentWhenPaymentIsInvalid()
    {
        $paymentModel = $this->getMock(Payment::class, ['isValidPayment']);
        $paymentModel
            ->method('isValidPayment')
            ->willReturn(false);

        UtilsObject::setClassInstance(Payment::class, $paymentModel);

        $order = $this->getMock(Order::class, ['getPaymentType']);
        $order
            ->method('getPaymentType')
            ->willReturn(
                oxNew(UserPayment::class)
            );

        $paymentId = oxDb::getDb()->getOne('select oxid from oxpayments where oxactive = 1');

        $basket = $this->getMock(Basket::class, array("getPaymentId"));
        $basket
            ->method("getPaymentId")
            ->willReturn($paymentId);

        $this->assertEquals(
            oxOrder::ORDER_STATE_INVALIDPAYMENT,
            $order->validatePayment($basket)
        );
    }

    /**
     * Test case for oxOrder::validateBasket()
     *
     * @return null
     */
    public function testValidateBasket()
    {
        $oOrder = oxNew('oxorder');

        // < min price
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array("isBelowMinOrderPrice"));
        $oBasket->expects($this->once())->method("isBelowMinOrderPrice")->will($this->returnValue(true));

        $this->assertEquals(oxOrder::ORDER_STATE_BELOWMINPRICE, $oOrder->validateBasket($oBasket));

        // > min price
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array("isBelowMinOrderPrice"));
        $oBasket->expects($this->once())->method("isBelowMinOrderPrice")->will($this->returnValue(false));

        $this->assertNull($oOrder->validateBasket($oBasket));
    }

    public function testGetOrderCurrency()
    {
        $oOrder = oxNew('oxOrder');
        $oCurr = $oOrder->getOrderCurrency();
        $this->assertEquals("EUR", $oCurr->name);

        // second currency
        $oOrder = oxNew('oxOrder');
        $oOrder->oxorder__oxcurrency = new oxField('GBP');
        $oCurr = $oOrder->getOrderCurrency();
        $this->assertEquals("GBP", $oCurr->name);
    }

    public function testCancelOrderCancelStateChangeDidNotSucceed()
    {
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("save", "getOrderArticles"));
        $oOrder->expects($this->once())->method('save')->will($this->returnValue(false));
        $oOrder->expects($this->never())->method('getOrderArticles');

        $oOrder->cancelOrder();
    }

    public function testCancelOrder()
    {
        $oOrderArticle1 = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderArticle::class, array("cancelOrderArticle"));
        $oOrderArticle1->expects($this->once())->method('cancelOrderArticle');

        $oOrderArticle2 = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderArticle::class, array("cancelOrderArticle"));
        $oOrderArticle2->expects($this->once())->method('cancelOrderArticle');

        $aOrderArticles[] = $oOrderArticle1;
        $aOrderArticles[] = $oOrderArticle2;

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("save", "getOrderArticles"));
        $oOrder->expects($this->once())->method('save')->will($this->returnValue(true));
        $oOrder->expects($this->once())->method('getOrderArticles')->will($this->returnValue($aOrderArticles));

        $oOrder->cancelOrder();
    }

    public function testDeleteOrderWhenOrderIsCanceled()
    {
        $this->getConfig()->setConfigParam("blUseStock", true);

        $sShopId = $this->getConfig()->getShopId();

        // test products for stock check
        $oProd = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oProd->setId("_testProdId");
        $oProd->oxarticles__oxactive = new oxField(1);
        $oProd->oxarticles__oxtitle = new oxField("testprod");
        $oProd->oxarticles__oxstock = new oxField(9);
        $oProd->save();

        // test order
        $oOrder = oxNew('oxOrder');
        $oOrder->setId("_testOrderId");
        $oOrder->oxorder__oxshopid = new oxField($sShopId);

        // test order products
        $oOrderProd = oxNew('oxOrderArticle');
        $oOrderProd->setId("_testOrderProdId");
        $oOrderProd->oxorderarticles__oxorderid = new oxField($oOrder->getId());
        $oOrderProd->oxorderarticles__oxartid = new oxField($oProd->getId());
        $oOrderProd->oxorderarticles__oxamount = new oxField(6);
        $oOrderProd->oxorderarticles__oxstorno = new oxField(0);
        $oOrderProd->save();

        $oOrder->save();

        // canceling order
        $oOrder->cancelOrder();

        $this->assertEquals(1, count($oOrder->getOrderArticles()));
        $this->assertEquals(0, count($oOrder->getOrderArticles(true)));

        // checking order products
        $oOrderProd = oxNew('oxOrderArticle');
        $oOrderProd->load("_testOrderProdId");
        $this->assertEquals(1, $oOrderProd->oxorderarticles__oxstorno->value);

        // checking products
        $oProd = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oProd->load("_testProdId");
        $this->assertEquals(15, $oProd->oxarticles__oxstock->value);

        $oDb = oxDb::getDb();

        // testing if order record in db
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxorder where oxid = '" . $oOrder->getId() . "'"));

        // testing if order product record in db
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxorderarticles where oxorderid = '" . $oOrder->getId() . "'"));

        // deleting order
        $oOrder->delete();

        // testing if order was deleted from db
        $this->assertFalse($oDb->getOne("select 1 from oxorder where oxid = '" . $oOrder->getId() . "'"));

        // testing if order products were removed from db
        $this->assertFalse($oDb->getOne("select 1 from oxorderarticles where oxorderid = '" . $oOrder->getId() . "'"));
    }

    public function testUpdateNoticeList()
    {
        $this->getConfig()->setConfigParam("blVariantParentBuyable", 1);
        $oDB = oxDb::getDb();

        // creating notice list items for some user
        $oUser = oxNew('oxuser');
        $oUser->setId('_testUserId');
        $oUser->save();

        $oUserBasket = $oUser->getBasket('noticelist');
        $oUserBasket->addItemToBasket('1126', 1, null);
        $oUserBasket->addItemToBasket('1127', 1, null);

        // testing if items are in place
        $this->assertEquals(2, (int) $oDB->getOne('select count(*) from oxuserbasketitems where oxbasketid = "' . $oUserBasket->getId() . '"'));
        $this->assertEquals($oUserBasket->getId(), $oDB->getOne('select oxid from oxuserbaskets where oxid = "' . $oUserBasket->getId() . '"'));

        // simulating basket items list
        $aBasketItems[0] = oxNew('oxBasketItem');
        $aBasketItems[0]->init('1126', 10);

        $aBasketItems[1] = oxNew('oxBasketItem');
        $aBasketItems[1]->init('1127', 10);

        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_testOrderId');
        $oOrder->UNITupdateNoticeList($aBasketItems, $oUser);

        // testing if items were removed from db
        $this->assertFalse($oDB->getOne('select 1 from oxuserbasketitems where oxbasketid = "' . $oUserBasket->getId() . '"'));
    }

    public function testSetOrderArticleList()
    {
        $aOrderArticleList = time();

        $oOrder = oxNew('oxOrder');
        $oOrder->setOrderArticleList($aOrderArticleList);
        $this->assertEquals($aOrderArticleList, $oOrder->getOrderArticles());
    }

    /**
     * Testing order language getter
     */
    public function testGetOrderLanguage()
    {
        // for empty order - session language is set
        $oOrder = oxNew('oxorder');
        $this->assertEquals(oxRegistry::getLang()->getBaseLanguage(), $oOrder->getOrderLanguage());

        // testing how language is validated
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxlang = new oxField(999);
        $this->assertEquals(0, $oOrder->getOrderLanguage());
    }

    /**
     * Bug: when order was made in ENG language, and shop admin adds new
     * product to order - order language is resetted to DE
     */
    public function testRecalculateOrderWhenSessionLanguageDiffersFromOrderLanguage()
    {
        oxTestModules::addFunction('oxarticle', 'isBuyable', '{ return true; }');

        $oOrder = oxNew('oxbase');
        $oOrder->init('oxorder');
        $oOrder->setId('_testOrderId');
        $oOrder->oxorder__oxshopid = new oxField($this->getConfig()->getShopid());
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->oxorder__oxorderdate = new oxField('2008-11-04 17:44:39');
        $oOrder->oxorder__oxordernr = new oxField(time());
        $oOrder->oxorder__oxbillcompany = new oxField('Ihr Firmenname');
        $oOrder->oxorder__oxbillemail = new oxField(oxADMIN_LOGIN);
        $oOrder->oxorder__oxbillfname = new oxField('Hans');
        $oOrder->oxorder__oxbilllname = new oxField('Mustermann');
        $oOrder->oxorder__oxbillstreet = new oxField('Musterstr.');
        $oOrder->oxorder__oxbillstreetnr = new oxField('10');
        $oOrder->oxorder__oxbillcity = new oxField('Musterstadt');
        $oOrder->oxorder__oxbillcountryid = new oxField('a7c40f631fc920687.20179984');
        $oOrder->oxorder__oxbillzip = new oxField('79098');
        $oOrder->oxorder__oxbillsal = new oxField('Mr');
        $oOrder->oxorder__oxpaymentid = new oxField('965594c328f54cc4a8f60c3595f92478');
        $oOrder->oxorder__oxpaymenttype = new oxField('oxidcashondel');
        $oOrder->oxorder__oxtotalnetsum = new oxField('42.86');
        $oOrder->oxorder__oxtotalbrutsum = new oxField('51');
        $oOrder->oxorder__oxdelcost = new oxField('3.9');
        $oOrder->oxorder__oxpaycost = new oxField('7.5');
        $oOrder->oxorder__oxcurrency = new oxField('EUR');
        $oOrder->oxorder__oxcurrate = new oxField('1');
        $oOrder->oxorder__oxtransstatus = new oxField('OK');
        $oOrder->oxorder__oxlang = new oxField('1');
        $oOrder->oxorder__oxdeltype = new oxField('oxidstandard');
        $oOrder->save();

        $oOrderArticle = oxNew('oxbase');
        $oOrderArticle->init('oxorderarticles');
        $oOrderArticle->setId('_testOrderArticleId');
        $oOrderArticle->oxorderarticles__oxorderid = new oxField($oOrder->getId());
        $oOrderArticle->oxorderarticles__oxamount = new oxField('1');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxtitle = new oxField('Beer homebrew kit CHEERS!');
        $oOrderArticle->save();

        // canceled order article
        $oOrderArticle = oxNew('oxbase');
        $oOrderArticle->init('oxorderarticles');
        $oOrderArticle->setId('_testOrderArticleId2');
        $oOrderArticle->oxorderarticles__oxorderid = new oxField($oOrder->getId());
        $oOrderArticle->oxorderarticles__oxamount = new oxField('1');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1126');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('1126');
        $oOrderArticle->oxorderarticles__oxtitle = new oxField('Beer homebrew kit CHEERS!');
        $oOrderArticle->oxorderarticles__oxstorno = new oxField(1);
        $oOrderArticle->save();

        // 0 amount article
        $oOrderArticle = oxNew('oxbase');
        $oOrderArticle->init('oxorderarticles');
        $oOrderArticle->setId('_testOrderArticleId3');
        $oOrderArticle->oxorderarticles__oxorderid = new oxField($oOrder->getId());
        $oOrderArticle->oxorderarticles__oxamount = new oxField(2);
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1127');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('1127');
        $oOrderArticle->oxorderarticles__oxtitle = new oxField('Beer homebrew kit CHEERS!');
        $oOrderArticle->oxorderarticles__oxstorno = new oxField(1);
        $oOrderArticle->save();

        // now loading and recalculating
        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxamount = new oxField(5);

        // now loading and recalculating
        $oOrderArticle2 = oxNew('oxOrderArticle');
        $oOrderArticle2->oxorderarticles__oxartid = new oxField('1127');
        $oOrderArticle2->oxorderarticles__oxartnum = new oxField('1127');
        $oOrderArticle2->oxorderarticles__oxamount = new oxField(0);


        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrderId');
        $oOrder->recalculateOrder(array('1651' => $oOrderArticle, '1127' => $oOrderArticle2));

        // reloading and checking order and order articles language
        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrderId');

        $oOrderArticles = $oOrder->getOrderArticles();
        $oOrderArticle = $oOrderArticles->current();

        $this->assertEquals('1', $oOrder->oxorder__oxlang->value);
        $this->assertEquals('Beer homebrew kit CHEERS!', $oOrderArticle->oxorderarticles__oxtitle->value);

    }

    private function _insertTestOrder($sId = '_testOrderId')
    {
        $myConfig = $this->getConfig();

        //set order
        $this->_oOrder = oxNew("oxOrder");
        $this->_oOrder->setId($sId);
        $this->_oOrder->oxorder__oxshopid = new oxField($myConfig->getShopId(), oxField::T_RAW);
        $this->_oOrder->oxorder__oxuserid = new oxField("_testUserId", oxField::T_RAW);
        //$this->_oOrder->oxorder__oxbillcountryid = new oxField('10', oxField::T_RAW);
        $this->_oOrder->oxorder__oxbillcountryid = new oxField("a7c40f6320aeb2ec2.72885259");
        $this->_oOrder->oxorder__oxdelcountryid = new oxField("a7c40f631fc920687.20179984", oxField::T_RAW);
        $this->_oOrder->oxorder__oxdeltype = new oxField('_testDeliverySetId', oxField::T_RAW);
        $this->_oOrder->oxorder__oxpaymentid = new oxField('_testPaymentId', oxField::T_RAW);
        $this->_oOrder->oxorder__oxpaymenttype = new oxField('_testPaymentId', oxField::T_RAW);
        $this->_oOrder->oxorder__oxcardid = new oxField('_testWrappingId', oxField::T_RAW);
        $this->_oOrder->save();
    }

    private function _insertTestOrderArticle($sOrdArtId = '_testOrderArticleId', $sArtId = '_testArticleId')
    {
        $oOrderArticle = oxNew("oxOrderArticle");
        $oOrderArticle->setId($sOrdArtId);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxartid = new oxField($sArtId, oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxamount = new oxField(5, oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxbprice = new oxField(119, oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxbrutprice = new oxField(5 * 119, oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxvat = new oxField(19, oxField::T_RAW);
        $oOrderArticle->save();

        return $oOrderArticle;
    }

    private function _insertTestArticle()
    {
        $myConfig = $this->getConfig();

        // insert test article
        $sInsert = "insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`,`OXSTOCKFLAG`,`OXSTOCK`,`OXPRICE`)
                values ('_testArticleId','" . $myConfig->getShopId() . "','testArticleTitle','2','20','119')";
        $sInsert2 = "insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`,`OXSTOCKFLAG`,`OXSTOCK`,`OXPRICE`)
                values ('_testArticleId2','" . $myConfig->getShopId() . "','testArticleTitle','2','20','119')";

        $this->addToDatabase($sInsert, 'oxarticles');
        $this->addToDatabase($sInsert2, 'oxarticles');
    }

    public function testAddOrderArticlesToBasket()
    {
        $this->_insertTestOrder();
        $this->_insertTestArticle();
        $oOrderArticle = $this->_insertTestOrderArticle();
        $oOrderArticle->load($oOrderArticle->getId());
        $oOrderArticle->oxorderarticles__oxwrapid = new oxField("xxx", oxField::T_RAW);

        $oOrderArticles['_testOrderArticleId'] = $oOrderArticle;

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");
        $oUser->oxuser__oxcountryid = new oxField('a7c40f631fc920687.20179984', oxField::T_RAW);

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('getOrderUser'));
        $oOrder->expects($this->once())->method('getOrderUser')->will($this->returnValue($oUser));
        $oOrder->load("_testOrderId");
        $oOrder->oxorder__oxpaymenttype = new oxField('oxidcashondel', oxField::T_RAW);
        $oOrder->oxorder__oxdeltype = new oxField('1b842e73470578914.54719298', oxField::T_RAW); // 3.9

        $oBasket = $oOrder->UNITgetOrderBasket(false, false);
        $oOrder->UNITaddOrderArticlesToBasket($oBasket, $oOrderArticles);
        $oBasket->calculateBasket(true);

        $this->assertEquals("_testUserId", $oBasket->getBasketUser()->getId());
        $this->assertEquals("1", $oBasket->getProductsCount());

        $this->assertEquals("595", $oBasket->getProductsPrice()->getBruttoSum());
        $this->assertEquals("602.5", $oBasket->getPrice()->getBruttoPrice()); // 595 + payment 7.5
        $this->assertEquals("oxidcashondel", $oBasket->getPaymentId());
        $this->assertEquals("1b842e73470578914.54719298", $oBasket->getShippingId());

        $oContents = $oBasket->getContents();
        $this->assertEquals(1, count($oContents));
        $this->assertEquals('xxx', $oContents['_testOrderArticleId']->getWrappingId());
    }

    public function testAddOrderArticlesToBasketUsesVouchersDiscount()
    {
        $this->_insertTestOrder();
        $this->_insertTestArticle();
        $oOrderArticle = $this->_insertTestOrderArticle();
        $oOrderArticles['_testOrderArticleId'] = $oOrderArticle;

        $oUser = oxNew('oxuser', 'core');
        $oUser->setId("_testUserId");
        $oUser->oxuser__oxcountryid = new oxField('a7c40f631fc920687.20179984', oxField::T_RAW);

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->load("_testOrderId");

        $oVoucherSerie = oxNew("oxVoucherSerie");
        $oVoucherSerie->setId('_testVoucherSerieId');
        $oVoucherSerie->oxvoucherseries__oxdiscount = new oxField('50', oxField::T_RAW);
        $oVoucherSerie->oxvoucherseries__oxdiscounttype = new oxField('absolute', oxField::T_RAW);
        $oVoucherSerie->save();

        $oVoucher = oxNew("oxVoucher");
        $oVoucher->setId('_testVoucherId');
        $oVoucher->oxvouchers__oxorderid = new oxField('_testOrderId', oxField::T_RAW);
        $oVoucher->oxvouchers__oxvouchernr = new oxField('_testVoucherNr', oxField::T_RAW);
        $oVoucher->oxvouchers__oxvoucherserieid = new oxField('_testVoucherSerieId', oxField::T_RAW);
        $oVoucher->save();

        $oBasket = $oOrder->UNITgetOrderBasket(false, false);
        $oOrder->UNITaddOrderArticlesToBasket($oBasket, $oOrderArticles);
        $oBasket->calculateBasket(true);

        $this->assertEquals("595", $oBasket->getProductsPrice()->getBruttoSum());
        $this->assertEquals("551.9", $oBasket->getPrice()->getBruttoPrice()); // 595 - 50 + 6.9 (delivery cost for Rest of Europe)
    }

    public function testSetSeparateNumbering()
    {
        $oOrder = $this->getProxyClass("oxOrder");

        $oOrder->setSeparateNumbering(true);
        $this->assertEquals(true, $oOrder->getNonPublicVar('_blSeparateNumbering'));

        $oOrder->setSeparateNumbering(false);
        $this->assertEquals(false, $oOrder->getNonPublicVar('_blSeparateNumbering'));
    }

    public function testGetShippingSetList()
    {
        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\modoxdeliverylist_oxorder::class, 'oxdeliverylist');

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId("_testOrderId");
        $oOrder->oxorder__oxuserid = new oxField('_testUserId', oxField::T_RAW);

        $aRet = $oOrder->getShippingSetList();
        $this->assertEquals(array('testShipSetId1', 'testShipSetId2'), $aRet);
    }

    //#M525 orderdate must be the same as it was
    public function testRecalculateOrderDoNotChangeSomeData()
    {
        $this->_insertTestOrder();
        $this->_insertTestArticle();
        $oOrderArticle = $this->_insertTestOrderArticle('_testOrderArticleId', '_testArticleId');
        $oOrderArticles['_testOrderArticleId'] = $oOrderArticle;
        $oOrderArticle2 = $this->_insertTestOrderArticle('_testOrderArticleId2', '_testArticleId2');
        $oOrderArticles['_testOrderArticleId2'] = $oOrderArticle2;

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");
        $oUser->save();

        $oOrder = oxNew('oxOrder');
        $oOrder->load("_testOrderId");
        $oOrder->oxorder__oxfolder = new oxField('Bearbeitet', oxField::T_RAW);
        $oOrder->oxorder__oxip = new oxField('111.111.111.111', oxField::T_RAW);
        $oOrder->oxorder__oxremark = new oxField('test remark', oxField::T_RAW);
        $sOrderDate = $oOrder->oxorder__oxorderdate->value;
        $sOrderIp = $oOrder->oxorder__oxip->value;
        $sOrderFolder = $oOrder->oxorder__oxfolder->value;
        $sOrderRemark = $oOrder->oxorder__oxremark->value;

        $oOrder->recalculateOrder(); // $oOrderArticles );

        $this->assertEquals(date('Y-m-d h', $sOrderDate), date('Y-m-d h', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->formatDBDate($oOrder->oxorder__oxorderdate->value)));
        $this->assertEquals($sOrderFolder, $oOrder->oxorder__oxfolder->value);
        $this->assertEquals($sOrderIp, $oOrder->oxorder__oxip->value);
        $this->assertEquals($sOrderRemark, $oOrder->oxorder__oxremark->value);
    }

    public function testRecalculateOrderChangingProductsAmount()
    {
        $this->_insertTestOrder();
        $this->_insertTestArticle();
        $oOrderArticle = $this->_insertTestOrderArticle('_testOrderArticleId', '_testArticleId');
        $oOrderArticle2 = $this->_insertTestOrderArticle('_testOrderArticleId2', '_testArticleId2');

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");
        $oUser->save();

        $oOrder = oxNew('oxOrder');
        $oOrder->load("_testOrderId");

        $oOrder->recalculateOrder();
        $oOrder->recalculateOrder();

        $this->assertEquals("1000", $oOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals("1190", $oOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals("1196.9", $oOrder->oxorder__oxtotalordersum->value); // + 6.9 delivery costs for rest of europe

        $oOrderArticle2->oxorderarticles__oxamount = new oxField(10, oxField::T_RAW);
        $oOrderArticle2->save();

        $oUpdatedOrder = oxNew('oxorder');
        $oUpdatedOrder->load('_testOrderId');
        $oUpdatedOrder->recalculateOrder();

        $this->assertEquals("_testUserId", $oUpdatedOrder->oxorder__oxuserid->value);
        $this->assertEquals("1500", $oUpdatedOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals("1785", $oUpdatedOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals("1791.9", $oUpdatedOrder->oxorder__oxtotalordersum->value); // + 6.9 delivery costs for rest of europe

        // if article is deleted
        $oOrderArticle2->oxorderarticles__oxamount = new oxField(0, oxField::T_RAW);
        $oOrderArticle2->save();

        $oUpdatedOrder = oxNew('oxorder');
        $oUpdatedOrder->load('_testOrderId');
        $oUpdatedOrder->recalculateOrder();

        $this->assertEquals("_testUserId", $oUpdatedOrder->oxorder__oxuserid->value);
        $this->assertEquals("500", $oUpdatedOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals("595", $oUpdatedOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals("601.9", $oUpdatedOrder->oxorder__oxtotalordersum->value); // + 6.9 delivery costs for rest of europe
    }

    public function testRecalculateOrderCancellingProducts()
    {
        $this->_insertTestOrder();
        $this->_insertTestArticle();
        $oOrderArticle = $this->_insertTestOrderArticle('_testOrderArticleId', '_testArticleId');
        $oOrderArticles['_testOrderArticleId'] = $oOrderArticle;
        $oOrderArticle2 = $this->_insertTestOrderArticle('_testOrderArticleId2', '_testArticleId2');
        $oOrderArticles['_testOrderArticleId2'] = $oOrderArticle2;

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");
        $oUser->save();

        $oOrder = oxNew('oxorder');
        $oOrder->load("_testOrderId");
        $oOrder->recalculateOrder();

        $this->assertEquals("1000", $oOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals("1190", $oOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals("1196.9", $oOrder->oxorder__oxtotalordersum->value);

        $sInsert = "update oxorderarticles set oxstorno='1' where oxartid='_testArticleId'";
        oxDb::getDb()->Execute($sInsert);

        $oUpdatedOrder = oxNew('oxorder');
        $oUpdatedOrder->load('_testOrderId');
        $oUpdatedOrder->recalculateOrder();

        $this->assertEquals("_testUserId", $oUpdatedOrder->oxorder__oxuserid->value);
        $this->assertEquals("500", $oUpdatedOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals("595", $oUpdatedOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals("601.9", $oUpdatedOrder->oxorder__oxtotalordersum->value);

        $sInsert = "update oxorderarticles set oxstorno='1' where oxartid='_testArticleId2'";
        oxDb::getDb()->Execute($sInsert);

        $oUpdatedOrder = oxNew('oxorder');
        $oUpdatedOrder->load('_testOrderId');
        $oUpdatedOrder->recalculateOrder();

        $this->assertEquals("_testUserId", $oUpdatedOrder->oxorder__oxuserid->value);
        $this->assertEquals("0", $oUpdatedOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals("0", $oUpdatedOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals("6.9", $oUpdatedOrder->oxorder__oxtotalordersum->value);
    }

    public function testRecalculateOrderCancellingAndDeletingProducts()
    {
        $this->_insertTestOrder();
        $this->_insertTestArticle();
        $oOrderArticle = $this->_insertTestOrderArticle('_testOrderArticleId', '_testArticleId');
        $oOrderArticles['_testOrderArticleId'] = $oOrderArticle;
        $oOrderArticle2 = $this->_insertTestOrderArticle('_testOrderArticleId2', '_testArticleId2');
        $oOrderArticles['_testOrderArticleId2'] = $oOrderArticle2;

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");
        $oUser->save();

        $oOrder = oxNew('oxorder');
        $oOrder->load("_testOrderId");
        $oOrder->recalculateOrder(); // $oOrderArticles );

        $this->assertEquals("1000", $oOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals("1190", $oOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals("1196.9", $oOrder->oxorder__oxtotalordersum->value);

        $sInsert = "update oxorderarticles set oxstorno='1' where oxartid='_testArticleId' or oxartid='_testArticleId2'";
        oxDb::getDb()->Execute($sInsert);

        $oUpdatedOrder = oxNew('oxorder');
        $oUpdatedOrder->load('_testOrderId');
        $oUpdatedOrder->recalculateOrder(); // array() );

        $oOrderArticle2->oxorderarticles__oxamount = new oxField(0, oxField::T_RAW);
        //$oOrder->recalculateOrder( array($oOrderArticle2) );
        $oUpdatedOrder = oxNew('oxorder');
        $oUpdatedOrder->load('_testOrderId');
        $oUpdatedOrder->recalculateOrder(array($oOrderArticle2));
        $this->assertEquals("_testUserId", $oUpdatedOrder->oxorder__oxuserid->value);
        $this->assertEquals("0", $oUpdatedOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals("0", $oUpdatedOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals("6.9", $oUpdatedOrder->oxorder__oxtotalordersum->value);
    }

    public function testRecalculateAddingArticles()
    {
        $oDB = oxDb::getDb();
        $myConfig = $this->getConfig();

        $this->_insertTestOrder();
        $this->_insertTestArticle();
        $oOrderArticle = $this->_insertTestOrderArticle();
        $oOrderArticles['_testOrderArticleId'] = $oOrderArticle;

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");
        $oUser->save();

        $oOrder = oxNew('oxOrder');
        $oOrder->load("_testOrderId");
        $oOrder->recalculateOrder(); // $oOrderArticles );

        $this->assertEquals("500", $oOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals("595", $oOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals("601.9", $oOrder->oxorder__oxtotalordersum->value);

        // insert test article 2
        $sInsert = "insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`,`OXSTOCKFLAG`,`OXSTOCK`,`OXPRICE`)
                    values ('_testArticleId3','" . $myConfig->getShopId() . "','testArticleTitle2','2','20','238')";

        $this->addToDatabase($sInsert, 'oxarticles');

        $oOrderArticle2 = oxNew('oxorderarticle');
        $oOrderArticle2->oxorderarticles__oxartid = new oxField('_testArticleId3', oxField::T_RAW);
        $oOrderArticle2->oxorderarticles__oxpersparam = new oxField(serialize(array(1, 2, 3)), oxField::T_RAW);
        $oOrderArticle2->oxorderarticles__oxamount = new oxField(1, oxField::T_RAW);

        $oOrder->recalculateOrder(array($oOrderArticle2));

        $oUpdatedOrder = oxNew('oxorder');
        $oUpdatedOrder->load('_testOrderId');

        //counting order articles
        $sSql = "select count(oxid) from oxorderarticles where oxorderid = '_testOrderId'";
        $this->assertEquals(2, $oDB->getOne($sSql));

        $this->assertEquals("_testUserId", $oUpdatedOrder->oxorder__oxuserid->value);
        $this->assertEquals("700", $oUpdatedOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals("833", $oUpdatedOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals("839.9", $oUpdatedOrder->oxorder__oxtotalordersum->value);

        //checking if persistent params were saved
        $sSql = "select oxpersparam from oxorderarticles where oxartid = '_testArticleId3'";
        $this->assertEquals(serialize(array(1, 2, 3)), $oDB->getOne($sSql));
    }

    public function testRecalculateOrderUpdatesArticleStockInfo()
    {
        $this->_insertTestOrder();
        $this->_insertTestArticle();
        $oOrderArticle = $this->_insertTestOrderArticle();

        // update articles stock
        $oOrderArticle->setNewAmount($oOrderArticle->oxorderarticles__oxamount->value + 5);
        $oOrderArticle->save();

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");
        $oUser->save();

        $oOrder = oxNew('oxOrder');
        $oOrder->load("_testOrderId");

        $oOrder->recalculateOrder();

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class, 'core');
        $oArticle->load('_testArticleId');

        //article stock was 20, so 15 items should be now
        $this->assertEquals("15", $oArticle->oxarticles__oxstock->value);
    }

    public function testRecalculateOrderNotUpdatesOrderOnError()
    {
        $oDB = oxDb::getDb();

        $this->_insertTestOrder();
        $this->_insertTestArticle();

        $oOrderArticle = $this->_insertTestOrderArticle();
        $oOrderArticle->setNewAmount(27);
        $this->assertEquals("25", $oOrderArticle->oxorderarticles__oxamount->value);

        $oUser = oxNew('oxuser', 'core');
        $oUser->setId("_testUserId");
        $oUser->save();

        $oOrder = oxNew('oxOrder', 'core');
        $oOrder->load("_testOrderId");
        $oOrder->oxorder__oxtotalnetsum = new oxField(500, oxField::T_RAW);
        $oOrder->oxorder__oxtotalbrutsum = new oxField(595, oxField::T_RAW);
        $oOrder->oxorder__oxtotalordersum = new oxField(595, oxField::T_RAW);

        // order article stock is greater than article stock (20), so no info shoud be updated
        // articles stock: 20 (current state) + 5 (restores back)- 27 (oxNew('value'))= -2 (no bier)
        $oOrder->recalculateOrder();

        $oOrderArticles = $oOrder->getOrderArticles();
        $oOrderArticle = $oOrderArticles->offsetGet("_testOrderArticleId");

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->load('_testArticleId');

        //article stock was 20, so stock amount should be = 0
        $this->assertEquals("0", $oArticle->oxarticles__oxstock->value);
        $this->assertEquals("25", $oOrderArticle->oxorderarticles__oxamount->value);

        $this->assertEquals(100 * 25, $oOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals(119 * 25, $oOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals(119 * 25 + 6.9, $oOrder->oxorder__oxtotalordersum->value);

        //check if order articles were not deleted
        $sSql = "select count(oxid) from oxorderarticles where oxorderid = '_testOrderId'";
        $this->assertEquals(1, $oDB->getOne($sSql));
    }

    //#M429: Total amonts are not recalculated when Shipping is changed for order in the admin
    public function testRecalculateOrderChangingShippingSetAndPayment()
    {
        oxTestModules::addFunction('oxBasket', 'isAdmin', '{ return true; }');

        $oOrder = oxNew('oxbase');
        $oOrder->init('oxorder');
        $oOrder->setId('_testOrderId2');
        $oOrder->oxorder__oxshopid = new oxField($this->getConfig()->getShopid());
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->oxorder__oxorderdate = new oxField('2008-11-04 17:44:39');
        $oOrder->oxorder__oxordernr = new oxField(time());
        $oOrder->oxorder__oxbillcompany = new oxField('Ihr Firmenname');
        $oOrder->oxorder__oxbillemail = new oxField(oxADMIN_LOGIN);
        $oOrder->oxorder__oxbillfname = new oxField('Hans');
        $oOrder->oxorder__oxbilllname = new oxField('Mustermann');
        $oOrder->oxorder__oxbillstreet = new oxField('Musterstr.');
        $oOrder->oxorder__oxbillstreetnr = new oxField('10');
        $oOrder->oxorder__oxbillcity = new oxField('Musterstadt');
        $oOrder->oxorder__oxbillcountryid = new oxField('a7c40f631fc920687.20179984');
        $oOrder->oxorder__oxbillzip = new oxField('79098');
        $oOrder->oxorder__oxbillsal = new oxField('Mr');
        $oOrder->oxorder__oxpaymentid = new oxField('965594c328f54cc4a8f60c3595f92478');
        $oOrder->oxorder__oxpaymenttype = new oxField('oxidcashondel');
        $oOrder->oxorder__oxtotalnetsum = new oxField('42.86');
        $oOrder->oxorder__oxtotalbrutsum = new oxField('51');
        $oOrder->oxorder__oxdelcost = new oxField('3.9');
        $oOrder->oxorder__oxpaycost = new oxField('7.5');
        $oOrder->oxorder__oxcurrency = new oxField('EUR');
        $oOrder->oxorder__oxcurrate = new oxField('1');
        $oOrder->oxorder__oxtransstatus = new oxField('OK');
        $oOrder->oxorder__oxlang = new oxField('1');
        $oOrder->oxorder__oxdeltype = new oxField('oxidstandard');
        $oOrder->save();

        $oOrderArticle = oxNew('oxbase');
        $oOrderArticle->init('oxorderarticles');
        $oOrderArticle->setId('_testOrderArticleId2');
        $oOrderArticle->oxorderarticles__oxorderid = new oxField($oOrder->getId());
        $oOrderArticle->oxorderarticles__oxamount = new oxField('1');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxtitle = new oxField('Beer homebrew kit CHEERS!');
        $oOrderArticle->save();

        $this->assertEquals('3.9', $oOrder->oxorder__oxdelcost->value);
        $this->assertEquals('7.5', $oOrder->oxorder__oxpaycost->value);

        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrderId2');
        $oOrder->setDelivery('1b842e732a23255b1.91207750');
        $oOrder->oxorder__oxpaymenttype->setValue("oxempty");
        $oOrder->recalculateOrder();

        $this->assertEquals('1b842e732a23255b1.91207750', $oOrder->oxorder__oxdeltype->value);
        $this->assertEquals('9.9', $oOrder->oxorder__oxdelcost->value);

        $oOrder->setDelivery('oxidstandard');
        $oOrder->oxorder__oxpaymenttype->setValue("oxidinvoice");
        $oOrder->recalculateOrder();

        $this->assertEquals('0.0', $oOrder->oxorder__oxpaycost->value);
    }

    //#M429: Total amounts are not recalculated when Shipping is changed for order in the admin
    public function testRecalculateOrderChangingShippingSetAndDelCosts()
    {
        oxTestModules::addFunction('oxBasket', 'isAdmin', '{ return true; }');

        $oOrder = oxNew('oxbase');
        $oOrder->init('oxorder');
        $oOrder->setId('_testOrderId2');
        $oOrder->oxorder__oxshopid = new oxField($this->getConfig()->getShopid());
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->oxorder__oxorderdate = new oxField('2008-11-04 17:44:39');
        $oOrder->oxorder__oxordernr = new oxField(time());
        $oOrder->oxorder__oxbillcompany = new oxField('Ihr Firmenname');
        $oOrder->oxorder__oxbillemail = new oxField(oxADMIN_LOGIN);
        $oOrder->oxorder__oxbillfname = new oxField('Hans');
        $oOrder->oxorder__oxbilllname = new oxField('Mustermann');
        $oOrder->oxorder__oxbillstreet = new oxField('Musterstr.');
        $oOrder->oxorder__oxbillstreetnr = new oxField('10');
        $oOrder->oxorder__oxbillcity = new oxField('Musterstadt');
        $oOrder->oxorder__oxbillcountryid = new oxField('a7c40f631fc920687.20179984');
        $oOrder->oxorder__oxbillzip = new oxField('79098');
        $oOrder->oxorder__oxbillsal = new oxField('Mr');
        $oOrder->oxorder__oxpaymentid = new oxField('965594c328f54cc4a8f60c3595f92478');
        $oOrder->oxorder__oxpaymenttype = new oxField('oxidcashondel');
        $oOrder->oxorder__oxtotalnetsum = new oxField('42.86');
        $oOrder->oxorder__oxtotalbrutsum = new oxField('51');
        $oOrder->oxorder__oxdelcost = new oxField('3.9');
        $oOrder->oxorder__oxpaycost = new oxField('7.5');
        $oOrder->oxorder__oxcurrency = new oxField('EUR');
        $oOrder->oxorder__oxcurrate = new oxField('1');
        $oOrder->oxorder__oxtransstatus = new oxField('OK');
        $oOrder->oxorder__oxlang = new oxField('1');
        $oOrder->oxorder__oxdeltype = new oxField('oxidstandard');
        $oOrder->save();

        $oOrderArticle = oxNew('oxbase');
        $oOrderArticle->init('oxorderarticles');
        $oOrderArticle->setId('_testOrderArticleId2');
        $oOrderArticle->oxorderarticles__oxorderid = new oxField($oOrder->getId());
        $oOrderArticle->oxorderarticles__oxamount = new oxField('1');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxtitle = new oxField('Beer homebrew kit CHEERS!');
        $oOrderArticle->save();

        $this->assertEquals('3.9', $oOrder->oxorder__oxdelcost->value);

        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrderId2');
        $oOrder->setDelivery('1b842e732a23255b1.91207750');
        $oOrder->oxorder__oxpaymenttype->setValue("oxempty");
        $oOrder->recalculateOrder();

        $this->assertEquals('9.9', $oOrder->oxorder__oxdelcost->value);
    }

    //#M601: manual change of delivery costs gets lost after saving some other order data
    public function testRecalculateOrderChangingDelCostsAndDiscount()
    {
        $oOrder = oxNew('oxbase');
        $oOrder->init('oxorder');
        $oOrder->setId('_testOrderId2');
        $oOrder->oxorder__oxshopid = new oxField($this->getConfig()->getShopid());
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->oxorder__oxorderdate = new oxField('2008-11-04 17:44:39');
        $oOrder->oxorder__oxordernr = new oxField(time());
        $oOrder->oxorder__oxbillcompany = new oxField('Ihr Firmenname');
        $oOrder->oxorder__oxbillemail = new oxField(oxADMIN_LOGIN);
        $oOrder->oxorder__oxbillfname = new oxField('Hans');
        $oOrder->oxorder__oxbilllname = new oxField('Mustermann');
        $oOrder->oxorder__oxbillstreet = new oxField('Musterstr.');
        $oOrder->oxorder__oxbillstreetnr = new oxField('10');
        $oOrder->oxorder__oxbillcity = new oxField('Musterstadt');
        $oOrder->oxorder__oxbillcountryid = new oxField('a7c40f631fc920687.20179984');
        $oOrder->oxorder__oxbillzip = new oxField('79098');
        $oOrder->oxorder__oxbillsal = new oxField('Mr');
        $oOrder->oxorder__oxpaymentid = new oxField('965594c328f54cc4a8f60c3595f92478');
        $oOrder->oxorder__oxpaymenttype = new oxField('oxidcashondel');
        $oOrder->oxorder__oxtotalnetsum = new oxField('42.86');
        $oOrder->oxorder__oxtotalbrutsum = new oxField('51');
        $oOrder->oxorder__oxdelcost = new oxField('3.9');
        $oOrder->oxorder__oxpaycost = new oxField('7.5');
        $oOrder->oxorder__oxcurrency = new oxField('EUR');
        $oOrder->oxorder__oxcurrate = new oxField('1');
        $oOrder->oxorder__oxtransstatus = new oxField('OK');
        $oOrder->oxorder__oxlang = new oxField('1');
        $oOrder->oxorder__oxdeltype = new oxField('oxidstandard');
        $oOrder->save();

        $oOrderArticle = oxNew('oxbase');
        $oOrderArticle->init('oxorderarticles');
        $oOrderArticle->setId('_testOrderArticleId2');
        $oOrderArticle->oxorderarticles__oxorderid = new oxField($oOrder->getId());
        $oOrderArticle->oxorderarticles__oxamount = new oxField('1');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('test');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('test');
        $oOrderArticle->oxorderarticles__oxtitle = new oxField('Beer homebrew kit CHEERS!');
        $oOrderArticle->oxorderarticles__oxbrutprice = new oxField(29);
        $oOrderArticle->oxorderarticles__oxbprice = new oxField(29);
        $oOrderArticle->save();

        $this->assertEquals('3.9', $oOrder->oxorder__oxdelcost->value);

        //change delivery
        $oOrder->oxorder__oxdelcost->setValue(0);
        $oOrder->save();

        $oOrder = oxNew('oxorder');
        $oOrder->setAdminMode(true);
        $oOrder->load('_testOrderId2');

        // keeps old delivery cost
        $oOrder->reloadDelivery(false);
        $oOrder->recalculateOrder();

        $this->assertEquals(0, $oOrder->oxorder__oxdelcost->value);
        $this->assertEquals(36.5, $oOrder->oxorder__oxtotalordersum->value);

        //change discount
        $oOrder->oxorder__oxdiscount->setValue(2);

        // keeps old discount
        $oOrder->reloadDiscount(false);
        $oOrder->recalculateOrder();

        $oOrder = oxNew('oxorder');
        $oOrder->setAdminMode(true);
        $oOrder->load('_testOrderId2');
        $this->assertEquals(0, $oOrder->oxorder__oxdelcost->value);
        $this->assertEquals(2, $oOrder->oxorder__oxdiscount->value);
        $this->assertEquals(34.5, $oOrder->oxorder__oxtotalordersum->value);

        $oOrder->reloadDelivery(false);
        $oOrder->reloadDiscount(false);
        $oOrder->recalculateOrder();

        $oOrder = oxNew('oxorder');
        $oOrder->setAdminMode(true);
        $oOrder->load('_testOrderId2');
        $this->assertEquals(0, $oOrder->oxorder__oxdelcost->value);
        $this->assertEquals(2, $oOrder->oxorder__oxdiscount->value);
        $this->assertEquals(34.5, $oOrder->oxorder__oxtotalordersum->value);
    }


    //#M434: Shipping is not resolved when user's country changed in Order info from Admin area
    public function testRecalculateOrderChangingUserCountry()
    {
        $oOrder = oxNew('oxbase');
        $oOrder->init('oxorder');
        $oOrder->setId('_testOrderId2');
        $oOrder->oxorder__oxshopid = new oxField($this->getConfig()->getShopid());
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->oxorder__oxorderdate = new oxField('2008-11-04 17:44:39');
        $oOrder->oxorder__oxordernr = new oxField(time());
        $oOrder->oxorder__oxbillcompany = new oxField('Ihr Firmenname');
        $oOrder->oxorder__oxbillemail = new oxField(oxADMIN_LOGIN);
        $oOrder->oxorder__oxbillfname = new oxField('Hans');
        $oOrder->oxorder__oxbilllname = new oxField('Mustermann');
        $oOrder->oxorder__oxbillstreet = new oxField('Musterstr.');
        $oOrder->oxorder__oxbillstreetnr = new oxField('10');
        $oOrder->oxorder__oxbillcity = new oxField('Musterstadt');
        $oOrder->oxorder__oxbillcountryid = new oxField('a7c40f631fc920687.20179984');
        $oOrder->oxorder__oxbillzip = new oxField('79098');
        $oOrder->oxorder__oxbillsal = new oxField('Mr');
        $oOrder->oxorder__oxpaymentid = new oxField('965594c328f54cc4a8f60c3595f92478');
        $oOrder->oxorder__oxpaymenttype = new oxField('oxidcashondel');
        $oOrder->oxorder__oxtotalnetsum = new oxField('42.86');
        $oOrder->oxorder__oxtotalbrutsum = new oxField('51');
        $oOrder->oxorder__oxdelcost = new oxField('3.9');
        $oOrder->oxorder__oxpaycost = new oxField('7.5');
        $oOrder->oxorder__oxcurrency = new oxField('EUR');
        $oOrder->oxorder__oxcurrate = new oxField('1');
        $oOrder->oxorder__oxtransstatus = new oxField('OK');
        $oOrder->oxorder__oxlang = new oxField('1');
        $oOrder->oxorder__oxdeltype = new oxField('oxidstandard');
        $oOrder->save();

        $oOrderArticle = oxNew('oxbase');
        $oOrderArticle->init('oxorderarticles');
        $oOrderArticle->setId('_testOrderArticleId2');
        $oOrderArticle->oxorderarticles__oxorderid = new oxField($oOrder->getId());
        $oOrderArticle->oxorderarticles__oxamount = new oxField('1');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxtitle = new oxField('Beer homebrew kit CHEERS!');
        $oOrderArticle->save();

        $oOrderArticle = oxNew('oxbase');
        $oOrderArticle->init('oxorderarticles');
        $oOrderArticle->setId('_testOrderArticleId3');
        $oOrderArticle->oxorderarticles__oxorderid = new oxField($oOrder->getId());
        $oOrderArticle->oxorderarticles__oxamount = new oxField('1');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('1651');
        $oOrderArticle->oxorderarticles__oxtitle = new oxField('Beer homebrew kit CHEERS!');
        $oOrderArticle->oxorderarticles__oxisbundle = new oxField(1);
        $oOrderArticle->save();

        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrderId2');

        $this->assertEquals('oxidstandard', $oOrder->oxorder__oxdeltype->value);

        $oOrder->oxorder__oxbillcountryid = new oxField('a7c40f6321c6f6109.43859248');
        $oOrder->save();

        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrderId2');
        $this->assertEquals('a7c40f6321c6f6109.43859248', $oOrder->oxorder__oxbillcountryid->value);
        $aRet = $oOrder->getShippingSetList();
        $aResult = array_keys($aRet);
        $aExpectResult = array('oxidstandard', '1b842e732a23255b1.91207750', '1b842e732a23255b1.91207751');
        sort($aExpectResult);
        sort($aResult);
        $this->assertEquals($aExpectResult, $aResult);

        $oOrder->oxorder__oxbillcountryid = new oxField('aaaaa');
        $oOrder->save();
        $aRet = $oOrder->getShippingSetList();
        $this->assertEquals(0, count($aRet));
    }

    public function testAssign()
    {
        $this->_insertTestOrder();

        $oOrder = oxNew('oxorder');
        $oOrder->load("_testOrderId");

        $this->assertEquals("_testOrderId", $oOrder->getId());
        $this->assertEquals("_testUserId", $oOrder->oxorder__oxuserid->value);
    }

    /*
     * Test if assing sets bill and delivery country title when title is empty
     */
    public function testAssignSetsBillCountryAndDeliveryCountryTitle()
    {
        $this->_insertTestOrder();

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_getCountryTitle'));

        $oOrder->expects($this->any())
            ->method('_getCountryTitle')
            ->will($this->returnValue('testCountryTitle'));

        $oOrder->load("_testOrderId");
        $this->assertEquals("testCountryTitle", $oOrder->oxorder__oxbillcountry->value);
        $this->assertEquals("testCountryTitle", $oOrder->oxorder__oxdelcountry->value);
    }

    public function testAssignResults()
    {
        $this->_insertTestOrder();

        $oOrder = oxNew('oxorder');
        $oOrder->assign(array('oxtotalordersum' => 999, 'oxstorno' => 0, 'oxorderdate' => '2008-01-01 01:01:01', 'oxsenddate' => '2009-02-02 02:02:02', 'oxbillcountryid' => 'a7c40f631fc920687.20179984', 'oxdelcountryid' => 'a7c40f631fc920687.20179984'));

        $sOrderDate = '01.01.2008 01:01:01';
        $sSendDate = '02.02.2009 02:02:02';
        if (oxRegistry::getLang()->getBaseLanguage() == 1) {
            $sOrderDate = '2008-01-01 01:01:01';
            $sSendDate = '2009-02-02 02:02:02';
        }

        $this->assertEquals($sOrderDate, $oOrder->oxorder__oxorderdate->value);
        $this->assertEquals($sSendDate, $oOrder->oxorder__oxsenddate->value);
        $this->assertEquals('Deutschland', $oOrder->oxorder__oxbillcountry->value);
        $this->assertEquals('Deutschland', $oOrder->oxorder__oxdelcountry->value);
        $this->assertEquals(999, $oOrder->getTotalOrderSum());
    }

    // #FS1967
    public function testAssignResultsCanceledOrder()
    {
        $this->_insertTestOrder();

        $oOrder = oxNew('oxorder');
        $oOrder->assign(array('oxtotalordersum' => 999, 'oxstorno' => 1, 'oxorderdate' => '2008-01-01 01:01:01', 'oxsenddate' => '2009-02-02 02:02:02', 'oxbillcountryid' => 'a7c40f631fc920687.20179984', 'oxdelcountryid' => 'a7c40f631fc920687.20179984'));

        $sOrderDate = '01.01.2008 01:01:01';
        $sSendDate = '02.02.2009 02:02:02';
        if (oxRegistry::getLang()->getBaseLanguage() == 1) {
            $sOrderDate = '2008-01-01 01:01:01';
            $sSendDate = '2009-02-02 02:02:02';
        }

        $this->assertEquals($sOrderDate, $oOrder->oxorder__oxorderdate->value);
        $this->assertEquals($sSendDate, $oOrder->oxorder__oxsenddate->value);
        $this->assertEquals('Deutschland', $oOrder->oxorder__oxbillcountry->value);
        $this->assertEquals('Deutschland', $oOrder->oxorder__oxdelcountry->value);
        $this->assertNull($oOrder->totalorder);
    }

    public function testLoadLoadsDeliverySet()
    {
        $this->_insertTestOrder();

        //insert test delivery set
        $oDelSet = oxNew('oxDeliverySet');
        $oDelSet->setId('_testDeliverySetId');
        $oDelSet->oxdeliveryset__oxtitle = new oxField('testDeliverySetTitle', oxField::T_RAW);
        $oDelSet->save();

        $oOrder = oxNew('oxorder');
        $oOrder->load("_testOrderId");

        $this->assertEquals("_testDeliverySetId", $oOrder->getDelSet()->getId());
        $this->assertEquals("testDeliverySetTitle", $oOrder->getDelSet()->oxdeliveryset__oxtitle->value);
    }

    public function testLoadLoadsPaymentType()
    {
        $this->_insertTestOrder();

        //insert test delivery set
        $oPayment = oxNew('oxUserPayment');
        $oPayment->setId('_testPaymentId');
        $oPayment->save();

        $oOrder = oxNew('oxorder');
        $oOrder->load("_testOrderId");

        $this->assertEquals("_testPaymentId", $oOrder->getPaymentType()->getId());
    }

    public function testLoadLoadsGiftCard()
    {
        $this->_insertTestOrder();

        //insert test delivery set
        $oWrapping = oxNew('oxWrapping');
        $oWrapping->setId('_testWrappingId');
        $oWrapping->save();

        $oOrder = oxNew('oxorder');
        $oOrder->load("_testOrderId");

        $this->assertEquals("_testWrappingId", $oOrder->getGiftCard()->getId());
    }

    public function testGetCountryTitle()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $sCountry = $oOrder->UNITgetCountryTitle("a7c40f631fc920687.20179984");
        $this->assertEquals("Deutschland", $sCountry);
    }

    public function testGetCountryTitleInOtherLang()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        oxRegistry::getLang()->setBaseLanguage(1);
        $sCountry = $oOrder->UNITgetCountryTitle("a7c40f631fc920687.20179984");
        $this->assertEquals("Germany", $sCountry);
    }

    public function testGetCountryTitleWithoutId()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $sCountry = $oOrder->UNITgetCountryTitle(null);
        $this->assertEquals(null, $sCountry);
    }

    public function testGetOrderArticles()
    {
        $myConfig = $this->getConfig();
        $oDB = oxDb::getDb();

        // insert order article
        $sInsert = "insert into oxorderarticles (`OXID`, `OXORDERID`, `OXARTID`, `OXAMOUNT`)
                    values ('_testOrderArticleId', '_testOrderId', '_testArticleId', '10')";

        $oDB->Execute($sInsert);

        $sInsert = "insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`,`OXSTOCKFLAG`,`OXSTOCK`,`OXPRICE`)
                        values ('_testArticleId','" . $myConfig->getShopId() . "','testArticleTitle','2','20','119')";

        $this->addToDatabase($sInsert, 'oxarticles');

        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_testOrderId');

        $oArticles = $oOrder->getOrderArticles();

        reset($oArticles);

        $this->assertEquals(1, $oArticles->count());
        $this->assertEquals(10, $oArticles["_testOrderArticleId"]->oxorderarticles__oxamount->value);
        $this->assertEquals("_testArticleId", $oArticles["_testOrderArticleId"]->oxorderarticles__oxartid->value);
    }

    public function testGetOrderDeliveryPrice()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxdelcost = new oxField(119, oxField::T_RAW);
        $oOrder->oxorder__oxdelvat = new oxField(19, oxField::T_RAW);

        $this->assertEquals(100, $oOrder->getOrderDeliveryPrice()->getNettoPrice());
        $this->assertEquals(19, $oOrder->getOrderDeliveryPrice()->getVATValue());
    }

    public function testGetOrderDeliveryPriceAlreadySet()
    {
        $oOrder = $this->getProxyClass("oxOrder");

        $oDelPrice = oxNew('oxprice');
        $oDelPrice->setPrice(119, 19);

        $oOrder->setNonPublicVar('_oDelPrice', $oDelPrice);

        $this->assertEquals(100, $oOrder->getOrderDeliveryPrice()->getNettoPrice());
        $this->assertEquals(19, $oOrder->getOrderDeliveryPrice()->getVATValue());
    }

    public function testGetOrderWrappingPrice()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxwrapcost = new oxField(119, oxField::T_RAW);
        $oOrder->oxorder__oxwrapvat = new oxField(19, oxField::T_RAW);

        $this->assertEquals(100, $oOrder->getOrderWrappingPrice()->getNettoPrice());
        $this->assertEquals(19, $oOrder->getOrderWrappingPrice()->getVATValue());
    }

    public function testGetOrderWrappingPriceAlreadySet()
    {
        $oOrder = $this->getProxyClass("oxOrder");

        $oWrappingPrice = oxNew('oxprice');
        $oWrappingPrice->setPrice(119, 19);

        $oOrder->setNonPublicVar('_oWrappingPrice', $oWrappingPrice);

        $this->assertEquals(100, $oOrder->getOrderWrappingPrice()->getNettoPrice());
        $this->assertEquals(19, $oOrder->getOrderWrappingPrice()->getVATValue());
    }

    public function testGetOrderPaymentPrice()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxpaycost = new oxField(119, oxField::T_RAW);
        $oOrder->oxorder__oxpayvat = new oxField(19, oxField::T_RAW);

        $this->assertEquals(100, $oOrder->getOrderPaymentPrice()->getNettoPrice());
        $this->assertEquals(19, $oOrder->getOrderPaymentPrice()->getVATValue());
    }

    public function testGetOrderPaymentPriceAlreadySet()
    {
        $oOrder = $this->getProxyClass("oxOrder");

        $oPaymentPrice = oxNew('oxprice');
        $oPaymentPrice->setPrice(119, 19);

        $oOrder->setNonPublicVar('_oPaymentPrice', $oPaymentPrice);

        $this->assertEquals(100, $oOrder->getOrderPaymentPrice()->getNettoPrice());
        $this->assertEquals(19, $oOrder->getOrderPaymentPrice()->getVATValue());
    }

    public function testGetOrderNetSum()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->oxorder__oxtotalnetsum = new oxField(200, oxField::T_RAW);

        $oPrice = oxNew('oxprice');
        $oPrice->setPrice(10, 0);

        $oOrder->setNonPublicVar('_oDelPrice', $oPrice);
        $oOrder->setNonPublicVar('_oWrappingPrice', $oPrice);
        $oOrder->setNonPublicVar('_oPaymentPrice', $oPrice);

        $this->assertEquals(230, $oOrder->getOrderNetSum());
    }

    public function testFinalizeOrderReturnsErrorCodeWhenOrderAlreadyExist()
    {
        $order = $this->getMock(Order::class, array('_checkOrderExist'));
        $order->expects($this->any())
            ->method('_checkOrderExist')
            ->will($this->returnValue('EXISTINGORDERID'));

        $basket = oxNew(Basket::class);
        $user = oxNew(User::class);

        $this->assertEquals(
            3,
            $order->finalizeOrder($basket, $user)
        );
    }

    /**
     * Testing if finalize order calls all required methods.
     * Sets order id, order user, assigns all info from basket, loads payment data.
     * Then executes payment. On success - saves order, removes article from wishlist,
     * updates voucher data. Finally sends order confirmation email to customer.
     */
    public function testFinalizeOrderCallsAllRequiredMethods()
    {
        $basket = oxNew(Basket::class);
        $user = oxNew(User::class);

        $methods = [
            'setId',
            '_loadFromBasket',
            '_setPayment',
            '_setFolder',
            'save',
            '_executePayment',
            '_updateWishlist',
            '_updateNoticeList',
            '_markVouchers',
            '_sendOrderByEmail',
            '_updateOrderDate',
        ];

        $testMethods = array_unique($methods);
        $testMethods[] = '_setUser';
        $testMethods[] = 'validateOrder';
        $testMethods[] = '_setOrderStatus';
        $order = $this->getMock(Order::class, $testMethods);

        foreach ($methods AS $key => $method) {
            $order->expects($this->once())
                ->method($method)
                ->will($this->returnValue(true));
        }

        $order->expects($this->atLeastOnce())->method('_setUser');
        $order->expects($this->atLeastOnce())->method('_setOrderStatus');
        $order->expects($this->once())->method('validateOrder');
        $order->expects($this->once())->method('_updateOrderDate');

        $order->finalizeOrder($basket, $user);
    }

    /**
     * Testing if finalize order calls all required methods.
     * Sets order id, order user, assigns all info from basket, loads payment data.
     * Then executes payment. On success - saves order, removes article from wishlist,
     * updates voucher data. Finally sends order confirmation email to customer.
     */
    public function testFinalizeOrderFromRecalculateOrder()
    {
        $basket = oxNew(Basket::class);
        $user = oxNew(User::class);

        $methods = [
            '_setUser',
            '_loadFromBasket',
            '_setPayment',
            '_setOrderStatus',
            'save',
            '_updateWishlist',
            '_updateNoticeList',
        ];
        $testMethods = array_unique($methods);
        $testMethods[] = '_updateOrderDate';
        $order = $this->getMock(Order::class, $testMethods);


        foreach ($methods AS $key => $method) {
            $order
                ->method($method)
                ->will($this->returnValue(true));
        }

        $order->expects($this->never())->method('_updateOrderDate');

        $order->finalizeOrder($basket, $user, true);
    }

    /**
     * Testing if finalizeOrder() on success returns sending order mail to user status
     */
    public function testFinalizeOrderReturnsMailingStatusOnSuccess()
    {
        $basket = oxNew(Basket::class);
        $user = oxNew(User::class);

        $methods = [
            'setId',
            '_setUser',
            '_loadFromBasket',
            '_setPayment',
            '_setFolder',
            'save',
            '_setOrderStatus',
            '_executePayment',
            '_setOrderStatus',
            '_updateWishlist',
            '_updateNoticeList',
            '_markVouchers',
            '_sendOrderByEmail',
            'validateOrder',
        ];

        $order = $this->getMock(Order::class, array_unique($methods));


        $order->expects($this->once())->method('setId')->will($this->returnValue(true));
        $order->expects($this->once())->method('_setUser')->will($this->returnValue(true));
        $order->expects($this->once())->method('_loadFromBasket')->will($this->returnValue(true));
        $order->expects($this->once())->method('_setPayment')->will($this->returnValue(true));
        $order->expects($this->once())->method('save')->will($this->returnValue(true));
        $order->expects($this->once())->method('_executePayment')->will($this->returnValue(true));
        $order->expects($this->atLeastOnce())->method('_setOrderStatus')->will($this->returnValue(true));
        $order->expects($this->once())->method('_updateWishlist')->will($this->returnValue(true));
        $order->expects($this->once())->method('_markVouchers')->will($this->returnValue(true));
        $order->expects($this->once())->method('_sendOrderByEmail')->will($this->returnValue(1));
        $order->expects($this->once())->method('validateOrder');

        $this->assertEquals(
            1,
            $order->finalizeOrder($basket, $user)
        );
    }

    /**
     * Testing if finalize order returns error code if order payment failed
     */
    public function testFinalizeOrderReturnsErrorCodeOnPaymentFailure()
    {
        $basket = oxNew(Basket::class);
        $user = oxNew(User::class);

        $methods = [
            'setId',
            '_setUser',
            '_loadFromBasket',
            '_setPayment',
            '_setFolder',
            'save',
            '_executePayment',
            'validateOrder',
        ];

        $order = $this->getMock(Order::class, $methods);

        $order->expects($this->once())->method('setId')->will($this->returnValue(true));
        $order->expects($this->once())->method('_setUser')->will($this->returnValue(true));
        $order->expects($this->once())->method('_loadFromBasket')->will($this->returnValue(true));
        $order->expects($this->once())->method('_setPayment')->will($this->returnValue(true));
        $order->expects($this->once())->method('save')->will($this->returnValue(true));
        $order->expects($this->once())->method('_executePayment')->will($this->returnValue(2));
        $order->expects($this->once())->method('validateOrder');

        $this->assertEquals(
            2,
            $order->finalizeOrder($basket, $user)
        );
    }


    public function testSetOrderStatus()
    {
        $this->_insertTestOrder();

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->load("_testOrderId");

        $sSql = "select oxorderdate from oxorder where oxid='_testOrderId'";
        $sOldDate = oxDb::getDb()->getOne($sSql);

        $oOrder->UNITsetOrderStatus("OK");

        $sSql = "select oxtransstatus from oxorder where oxid='_testOrderId'";
        $sStatus = oxDb::getDb()->getOne($sSql);

        $this->assertEquals("OK", $sStatus);

        //checking if order object also has this status (M:1300)
        $this->assertEquals("OK", $oOrder->oxorder__oxtransstatus->value);
    }

    public function testUpdateOrderDate()
    {
        $this->_insertTestOrder();

        oxAddClassModule('modOxUtilsDate', 'oxUtilsDate');
        \OxidEsales\Eshop\Core\Registry::getUtilsDate()->UNITSetTime(100);

        $sQ = "select oxorderdate from oxorder where oxid='_testOrderId' ";
        $sDate = oxDb::getDb()->getOne($sQ);

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->load("_testOrderId");
        $oOrder->UNITupdateOrderDate();

        $sQ = "select oxorderdate from oxorder where oxid='_testOrderId' ";
        $sDateNew = oxDb::getDb()->getOne($sQ);

        $this->assertNotEquals($sDate, $sDateNew);
        $this->assertEquals(date('Y-m-d h:i:s', 100), $sDateNew);
    }


    public function testLoadFromBasket()
    {
        $this->getConfig()->setConfigParam("blStoreIPs", true);

        $myConfig = $this->getConfig();

        // simulating basket
        $oPrice = oxNew('oxPrice');
        //$oPriceList = oxNew( 'oxPriceList' );
        $oPrice->setPrice(119, 19);
        //$oPriceList->addToPriceList( $oPrice );
        $oDiscount = new stdClass;
        $oDiscount->dDiscount = 2;
        $oDiscount2 = new stdClass;
        $oDiscount2->dDiscount = 3;
        $dDiscountesNettoPrice = 95;
        $aProductVats = array('10' => '50', '5' => '25');

        $this->getSession()->setVariable('ordrem', 'testValue');

        $aMethods = array('getBruttoSum',
                          'getPrice',
                          'getCosts',
                          'getVoucherDiscount',
                          'getDiscounts',
                          'getProductVats',
                          'getNettoSum',
                          'getContents',
                          'getShippingId'
        );

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, $aMethods);

        $oBasket->expects($this->any())->method('getBruttoSum')->will($this->returnValue("119"));
        $oBasket->expects($this->any())->method('getPrice')->will($this->returnValue($oPrice));
        $oBasket->expects($this->any())->method('getCosts')->will($this->returnValue($oPrice));
        $oBasket->expects($this->any())->method('getVoucherDiscount')->will($this->returnValue($oPrice));
        $oBasket->expects($this->any())->method('getDiscounts')->will($this->returnValue(array($oDiscount, $oDiscount2)));
        $oBasket->expects($this->any())->method('getProductVats')->will($this->returnValue($aProductVats));
        $oBasket->expects($this->any())->method('getNettoSum')->will($this->returnValue($dDiscountesNettoPrice));
        $oBasket->expects($this->any())->method('getContents')->will($this->returnValue(array()));
        $oBasket->expects($this->any())->method('getShippingId')->will($this->returnValue('_testShippingId'));

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->UNITloadFromBasket($oBasket);

        $this->assertEquals("95", $oOrder->oxorder__oxtotalnetsum->value);
        $this->assertEquals("119", $oOrder->oxorder__oxtotalbrutsum->value);
        $this->assertEquals("119", $oOrder->oxorder__oxtotalordersum->value);
        $this->assertEquals("10", $oOrder->oxorder__oxartvat1->value);
        $this->assertEquals("50", $oOrder->oxorder__oxartvatprice1->value);
        $this->assertEquals("5", $oOrder->oxorder__oxartvat2->value);
        $this->assertEquals("25", $oOrder->oxorder__oxartvatprice2->value);
        $this->assertEquals("119", $oOrder->oxorder__oxpaycost->value);
        $this->assertEquals("19", $oOrder->oxorder__oxpayvat->value);
        $this->assertEquals("119", $oOrder->oxorder__oxdelcost->value);
        $this->assertEquals("19", $oOrder->oxorder__oxdelvat->value);
        $this->assertEquals("_testShippingId", $oOrder->oxorder__oxdeltype->value);
        $this->assertEquals("testValue", $oOrder->oxorder__oxremark->value);
        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getRemoteAddress(), $oOrder->oxorder__oxip->value);

        $oCur = $myConfig->getActShopCurrencyObject();
        $this->assertEquals($oCur->name, $oOrder->oxorder__oxcurrency->value);
        $this->assertEquals($oCur->rate, $oOrder->oxorder__oxcurrate->value);

        $this->assertEquals("119", $oOrder->oxorder__oxvoucherdiscount->value);
        $this->assertEquals(oxRegistry::getLang()->getBaseLanguage(), $oOrder->oxorder__oxlang->value);
        $this->assertEquals("5", $oOrder->oxorder__oxdiscount->value);
        $this->assertEquals("ERROR", $oOrder->oxorder__oxtransstatus->value);
        $this->assertEquals(119, $oOrder->oxorder__oxwrapcost->value);
    }

    public function testLoadFromBasketSetsOrderArticles()
    {
        // simulating basket
        $oPrice = oxNew('oxPrice');
        $oPriceList = oxNew('oxPriceList');
        $oPrice->setPrice(119, 19);
        $oPriceList->addToPriceList($oPrice);

        $oBasketItem = $this->getProxyClass("oxBasketItem");
        $oBasketItem->setNonPublicVar('_sProductId', '1126');
        $oBasketItem->setNonPublicVar('_oPrice', $oPrice);
        $oBasketItem->setNonPublicVar('_oUnitPrice', $oPrice);
        $aBasketItems[] = $oBasketItem;
        $aBasketItems[] = $oBasketItem;


        $aMethods = array('getProductsPrice',
                          'getPrice',
                          'getCosts',
                          'getVoucherDiscount',
                          'getTotalDiscount',
                          'getContents',
                          'getShippingId'
        );

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, $aMethods);

        $oBasket->expects($this->any())->method('getProductsPrice')->will($this->returnValue($oPriceList));
        $oBasket->expects($this->any())->method('getPrice')->will($this->returnValue($oPrice));
        $oBasket->expects($this->any())->method('getCosts')->will($this->returnValue($oPrice));
        $oBasket->expects($this->any())->method('getVoucherDiscount')->will($this->returnValue($oPrice));
        $oBasket->expects($this->any())->method('getTotalDiscount')->will($this->returnValue($oPrice));
        $oBasket->expects($this->any())->method('getShippingId')->will($this->returnValue('_testShippingId'));
        $oBasket->expects($this->any())->method('getContents')->will($this->returnValue($aBasketItems));

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->UNITloadFromBasket($oBasket);

        $this->assertEquals(2, count($oOrder->getNonPublicVar('_oArticles')));
    }

    public function testSetUser()
    {
        //load user
        $oUser = oxNew('oxuser');
        $oUser->load("oxdefaultadmin");

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->UNITsetUser($oUser);

        $this->assertEquals('oxdefaultadmin', $oOrder->oxorder__oxuserid->value);
        $this->assertEquals('John', $oOrder->oxorder__oxbillfname->value);
        $this->assertEquals('Doe', $oOrder->oxorder__oxbilllname->value);
        $this->assertEquals(null, $oOrder->oxorder__oxdelfname->value);
    }

    public function testSetUserLoadsDeliveryAddress()
    {
        $oUser = oxNew('oxuser');
        $oUser->load("oxdefaultadmin");

        $oDelAddress = oxNew('oxBase');
        $oDelAddress->init('oxaddress');

        $oDelAddress->oxaddress__oxfname = new oxField('testDelFName', oxField::T_RAW);
        $oDelAddress->oxaddress__oxlname = new oxField('testDelLName', oxField::T_RAW);
        $oDelAddress->oxaddress__oxcity = new oxField('testDelCity', oxField::T_RAW);
        $oDelAddress->oxaddress__oxcompany = new oxField('', oxField::T_RAW);
        $oDelAddress->oxaddress__oxstreet = new oxField('', oxField::T_RAW);
        $oDelAddress->oxaddress__oxstreetnr = new oxField('', oxField::T_RAW);
        $oDelAddress->oxaddress__oxaddinfo = new oxField('', oxField::T_RAW);
        $oDelAddress->oxaddress__oxcountryid = new oxField('', oxField::T_RAW);
        $oDelAddress->oxaddress__oxstateid = new oxField('', oxField::T_RAW);
        $oDelAddress->oxaddress__oxzip = new oxField('', oxField::T_RAW);
        $oDelAddress->oxaddress__oxfon = new oxField('', oxField::T_RAW);
        $oDelAddress->oxaddress__oxfax = new oxField('', oxField::T_RAW);
        $oDelAddress->oxaddress__oxsal = new oxField('', oxField::T_RAW);

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('getDelAddressInfo'));
        $oOrder->expects($this->once())->method('getDelAddressInfo')->will($this->returnValue($oDelAddress));
        $oOrder->UNITsetUser($oUser);

        $this->assertEquals('testDelFName', $oOrder->oxorder__oxdelfname->value);
        $this->assertEquals('testDelLName', $oOrder->oxorder__oxdellname->value);
        $this->assertEquals('testDelCity', $oOrder->oxorder__oxdelcity->value);
    }

    public function testSetWrapping()
    {
        $myConfig = oxNew('oxConfig');

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(119, 19);

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getCosts', 'getCardId', 'getCardMessage'));
        $oBasket->expects($this->any())->method('getCosts')->will($this->returnValue($oPrice));
        $oBasket->expects($this->any())->method('getCardId')->will($this->returnValue('testCardId'));
        $oBasket->expects($this->any())->method('getCardMessage')->will($this->returnValue('testCardMsg'));

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setConfig($myConfig);

        $oOrder->UNITsetWrapping($oBasket);

        $this->assertEquals(119, $oOrder->oxorder__oxwrapcost->value);
        $this->assertEquals(19, $oOrder->oxorder__oxwrapvat->value);
        $this->assertEquals('testCardId', $oOrder->oxorder__oxcardid->value);
        $this->assertEquals('testCardMsg', $oOrder->oxorder__oxcardtext->value);
    }

    public function testSetOrderArticles()
    {
        // simulating basket
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(115, 15);

        $oBasketItem = $this->getProxyClass("oxBasketItem");
        $oBasketItem->setNonPublicVar('_sProductId', '1126');
        $oBasketItem->setNonPublicVar('_oPrice', $oPrice);
        $oBasketItem->setNonPublicVar('_oUnitPrice', $oPrice);
        $oBasketItem->setNonPublicVar('_dAmount', 3);
        $oBasketItem->setNonPublicVar('_sWrappingId', 'testWrapId');
        $oBasketItem->setNonPublicVar('_sShopId', 'testShopId');
        $aBasketItems[] = $oBasketItem;

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId');

        $oOrder->UNITsetOrderArticles($aBasketItems);
        $oArticles = $oOrder->getNonPublicVar('_oArticles');
        //$this->assertEquals( 1, count($oArticles) );
        $this->assertEquals(1, $oArticles->count());

        $oArticles->rewind();
        $oOrderArticle = $oArticles->current();
        $this->assertEquals('_testOrderId', $oOrderArticle->oxorderarticles__oxorderid->value);
        $this->assertEquals('1126', $oOrderArticle->oxorderarticles__oxartid->value);
        $this->assertEquals(3, $oOrderArticle->oxorderarticles__oxamount->value);

        $this->assertEquals('1126', $oOrderArticle->oxorderarticles__oxartnum->value);
        $this->assertEquals('Bar-Set ABSINTH', $oOrderArticle->oxorderarticles__oxtitle->value);

        $this->assertEquals('100', $oOrderArticle->oxorderarticles__oxnetprice->value);
        $this->assertEquals('115', $oOrderArticle->oxorderarticles__oxbrutprice->value);
        $this->assertEquals('15', $oOrderArticle->oxorderarticles__oxvatprice->value);
        $this->assertEquals('15', $oOrderArticle->oxorderarticles__oxvat->value);
        // #M773 Do not use article lazy loading on order save
        $this->assertTrue(isset($oOrderArticle->oxorderarticles__oxthumb->value));

        $this->assertEquals('testWrapId', $oOrderArticle->oxorderarticles__oxwrapid->value);
        $this->assertEquals('testShopId', $oOrderArticle->oxorderarticles__oxordershopid->value);
    }

    public function testSetOrderArticlesCopiesArticleFieldsToOrderArticle()
    {
        // simulating basket
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(119, 19);

        $oBasketItem = $this->getProxyClass("oxBasketItem");
        $oBasketItem->setNonPublicVar('_sProductId', '1351');
        $oBasketItem->setNonPublicVar('_oPrice', $oPrice);
        $oBasketItem->setNonPublicVar('_oUnitPrice', $oPrice);
        $aBasketItems[] = $oBasketItem;

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId');
        $oOrder->UNITsetOrderArticles($aBasketItems);

        $oArticles = $oOrder->getNonPublicVar('_oArticles');
        $oArticles->rewind();
        $oOrderArticle = $oArticles->current();

        //check if article info was copied to oxorderarticle
        $expected = ($this->getConfig()->getEdition() === 'EE') ? '50' : '14';
        $this->assertEquals($expected, $oOrderArticle->oxorderarticles__oxstock->value);
    }

    public function testSetOrderArticlesWithChoosenSelectList()
    {
        $aChosenSelectlist[0] = new stdClass();
        $aChosenSelectlist[0]->name = 'selectName';
        $aChosenSelectlist[0]->value = 'selectValue';

        $aChosenSelectlist[1] = new stdClass();
        $aChosenSelectlist[1]->name = 'selectName';
        $aChosenSelectlist[1]->value = 'selectValue';

        // simulating basket
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(119, 19);

        $oBasketItem = $this->getProxyClass("oxBasketItem");
        $oBasketItem->setNonPublicVar('_sProductId', '1126');
        $oBasketItem->setNonPublicVar('_oPrice', $oPrice);
        $oBasketItem->setNonPublicVar('_oUnitPrice', $oPrice);
        $oBasketItem->setNonPublicVar('_aChosenSelectlist', $aChosenSelectlist);
        $aBasketItems[] = $oBasketItem;

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId');
        $oOrder->UNITsetOrderArticles($aBasketItems);

        $oArticles = $oOrder->getNonPublicVar('_oArticles');
        $oArticles->rewind();
        $oOrderArticle = $oArticles->current();

        $this->assertEquals('selectName : selectValue, selectName : selectValue', $oOrderArticle->oxorderarticles__oxselvariant->value);
        $this->assertEquals('Bar-Set ABSINTH', $oOrderArticle->oxorderarticles__oxtitle->value);
    }

    public function testSetOrderArticlesWithTwoChoosenSelectList()
    {
        $aChosenSelectlist[0] = new stdClass();
        $aChosenSelectlist[0]->name = 'selectName';
        $aChosenSelectlist[0]->value = 'selectValue';

        $aChosenSelectlist2[0] = new stdClass();
        $aChosenSelectlist2[0]->name = 'selectName2';
        $aChosenSelectlist2[0]->value = 'selectValue2';

        // simulating basket
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(119, 19);

        $oBasketItem = $this->getProxyClass("oxBasketItem");
        $oBasketItem->setNonPublicVar('_sProductId', '1126');
        $oBasketItem->setNonPublicVar('_oPrice', $oPrice);
        $oBasketItem->setNonPublicVar('_oUnitPrice', $oPrice);
        $oBasketItem->setNonPublicVar('_aChosenSelectlist', $aChosenSelectlist);
        $aBasketItems[] = $oBasketItem;

        $oBasketItem2 = $this->getProxyClass("oxBasketItem");
        $oBasketItem2->setNonPublicVar('_sProductId', '1126');
        $oBasketItem2->setNonPublicVar('_oPrice', $oPrice);
        $oBasketItem2->setNonPublicVar('_oUnitPrice', $oPrice);
        $oBasketItem2->setNonPublicVar('_aChosenSelectlist', $aChosenSelectlist2);
        $aBasketItems[] = $oBasketItem2;


        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId');
        $oOrder->UNITsetOrderArticles($aBasketItems);

        $oArticles = $oOrder->getNonPublicVar('_oArticles');

        $i = 0;
        foreach ($oArticles as $oArticle) {
            $oOrderArticles[$i++] = $oArticle;
        }

        $this->assertEquals('selectName : selectValue', $oOrderArticles[0]->oxorderarticles__oxselvariant->value);
        $this->assertEquals('selectName2 : selectValue2', $oOrderArticles[1]->oxorderarticles__oxselvariant->value);
        $this->assertEquals('Bar-Set ABSINTH', $oOrderArticles[0]->oxorderarticles__oxtitle->value);
        $this->assertEquals('Bar-Set ABSINTH', $oOrderArticles[1]->oxorderarticles__oxtitle->value);
    }

    public function testSetOrderArticlesWithTwoChoosenSelectListAndAVariant()
    {
        $aChosenSelectlist[0] = new stdClass();
        $aChosenSelectlist[0]->name = 'selectName';
        $aChosenSelectlist[0]->value = 'selectValue';

        $aChosenSelectlist2[0] = new stdClass();
        $aChosenSelectlist2[0]->name = 'selectName2';
        $aChosenSelectlist2[0]->value = 'selectValue2';

        $sVarSelect = 'red | S';

        // simulating basket
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(119, 19);

        $oBasketItem = $this->getProxyClass("oxBasketItem");
        $oBasketItem->setNonPublicVar('_sProductId', '1126');
        $oBasketItem->setNonPublicVar('_oPrice', $oPrice);
        $oBasketItem->setNonPublicVar('_oUnitPrice', $oPrice);
        $oBasketItem->setNonPublicVar('_aChosenSelectlist', $aChosenSelectlist);
        $aBasketItems[] = $oBasketItem;

        $oBasketItem2 = $this->getProxyClass("oxBasketItem");
        $oBasketItem2->setNonPublicVar('_sProductId', '1126');
        $oBasketItem2->setNonPublicVar('_oPrice', $oPrice);
        $oBasketItem2->setNonPublicVar('_oUnitPrice', $oPrice);
        $oBasketItem2->setNonPublicVar('_aChosenSelectlist', $aChosenSelectlist2);
        $oBasketItem2->setNonPublicVar('_sVarSelect', $sVarSelect);
        $aBasketItems[] = $oBasketItem2;


        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId');
        $oOrder->UNITsetOrderArticles($aBasketItems);

        $oArticles = $oOrder->getNonPublicVar('_oArticles');

        $i = 0;
        foreach ($oArticles as $oArticle) {
            $oOrderArticles[$i++] = $oArticle;
        }

        $this->assertEquals('selectName : selectValue', $oOrderArticles[0]->oxorderarticles__oxselvariant->value);
        $this->assertEquals('selectName2 : selectValue2 || red | S', $oOrderArticles[1]->oxorderarticles__oxselvariant->value);
        $this->assertEquals('Bar-Set ABSINTH', $oOrderArticles[0]->oxorderarticles__oxtitle->value);
        $this->assertEquals('Bar-Set ABSINTH', $oOrderArticles[1]->oxorderarticles__oxtitle->value);
    }

    public function testExecutePayment()
    {
        $oGateway = $this->getMock(\OxidEsales\Eshop\Application\Model\PaymentGateway::class, array('executePayment'));
        $oGateway->expects($this->any())->method('executePayment')->will($this->returnValue(true));

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_getGateway'));
        $oOrder->expects($this->once())->method('_getGateway')->will($this->returnValue($oGateway));

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(119, 19);

        $oBasket = $this->getProxyClass('oxBasket');
        $oBasket->setNonPublicVar('_oPrice', $oPrice);

        $oPayment = oxNew('oxpayment');

        $this->assertEquals(true, $oOrder->UNITexecutePayment($oBasket, $oPayment));
    }

    public function testExecutePaymentReturnsDefaultErrorCodeOnFailedPayment()
    {
        $oGateway = $this->getMock(\OxidEsales\Eshop\Application\Model\PaymentGateway::class, array('executePayment', 'getLastErrorNo'));
        $oGateway->expects($this->any())->method('executePayment')->will($this->returnValue(false));
        $oGateway->expects($this->any())->method('getLastErrorNo')->will($this->returnValue(false));

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_getGateway'));
        $oOrder->expects($this->once())->method('_getGateway')->will($this->returnValue($oGateway));

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(119, 19);

        $oBasket = $this->getProxyClass('oxBasket');
        $oBasket->setNonPublicVar('_oPrice', $oPrice);

        $oPayment = oxNew('oxpayment');

        $this->assertEquals(2, $oOrder->UNITexecutePayment($oBasket, $oPayment));
    }

    public function testExecutePaymentReturnsGatewayErrorNoOnFailedPayment()
    {
        $oGateway = $this->getMock(\OxidEsales\Eshop\Application\Model\PaymentGateway::class, array('executePayment', 'getLastErrorNo'));
        $oGateway->expects($this->any())->method('executePayment')->will($this->returnValue(false));
        $oGateway->expects($this->any())->method('getLastErrorNo')->will($this->returnValue(3));

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_getGateway'));
        $oOrder->expects($this->once())->method('_getGateway')->will($this->returnValue($oGateway));

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(119, 19);

        $oBasket = $this->getProxyClass('oxBasket');
        $oBasket->setNonPublicVar('_oPrice', $oPrice);

        $oPayment = oxNew('oxpayment');

        $this->assertEquals(3, $oOrder->UNITexecutePayment($oBasket, $oPayment));
    }

    public function testExecutePaymentReturnsGatewayErrorMessageOnFailedPayment()
    {
        $oGateway = $this->getMock(\OxidEsales\Eshop\Application\Model\PaymentGateway::class, array('executePayment', 'getLastError'));
        $oGateway->expects($this->any())->method('executePayment')->will($this->returnValue(false));
        $oGateway->expects($this->any())->method('getLastError')->will($this->returnValue('testErrorMsg'));

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_getGateway'));
        $oOrder->expects($this->once())->method('_getGateway')->will($this->returnValue($oGateway));

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(119, 19);

        $oBasket = $this->getProxyClass('oxBasket');
        $oBasket->setNonPublicVar('_oPrice', $oPrice);

        $oPayment = oxNew('oxpayment');

        $this->assertEquals('testErrorMsg', $oOrder->UNITexecutePayment($oBasket, $oPayment));
    }

    public function testGetGatewayPayment()
    {
        $this->_insertTestOrder();

        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('iPayment_blActive', false);

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setConfig($oConfig);
        $oOrder->oxorder__oxpaymenttype = new oxField('_testPaymentId', oxField::T_RAW);

        $oPayment = oxNew('oxPayment');
        $oPayment->oxpayments__oxactive = new oxField('1', oxField::T_RAW);
        $oPayment->setId('_testPaymentId');
        $oPayment->save();

        $oGateway = $oOrder->UNITgetGateway();
        $this->assertInstanceOf('OxidEsales\EshopCommunity\Application\Model\PaymentGateway', $oGateway);
    }

    public function testSetPayment()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->oxorder__oxuserid = new oxField("_testUserId", oxField::T_RAW);

        $oUserpayment = $oOrder->UNITsetPayment('oxidcashondel');

        $this->assertEquals("_testUserId", $oUserpayment->oxuserpayments__oxuserid->value);
        $this->assertEquals("oxidcashondel", $oUserpayment->oxuserpayments__oxpaymentsid->value);
        $this->assertEquals("", $oUserpayment->oxuserpayments__oxvalue->value);
        $this->assertEquals("Nachnahme", $oUserpayment->oxpayments__oxdesc->value);
        $this->assertEquals(0, count($oUserpayment->aDynValues));
    }

    public function testSetFolder()
    {
        $myConfig = $this->getConfig();
        $oOrder = $this->getProxyClass("oxOrder");

        $oOrder->UNITsetFolder();

        $this->assertEquals(key($myConfig->getShopConfVar('aOrderfolder')), $oOrder->oxorder__oxfolder->value);
    }

    public function testSetPaymentSavesUserPaymentInDb()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->oxorder__oxuserid = new oxField("_testUserId", oxField::T_RAW);

        $oOrder->UNITsetPayment('oxidcashondel');

        $myDb = oxDb::getDb();
        $sSql = "select oxuserid from oxuserpayments where oxuserid = '_testUserId' and oxpaymentsid = 'oxidcashondel' ";

        $this->assertEquals('_testUserId', $myDb->getOne($sSql));
    }

    public function testSetPaymentWithWrongPaymentId()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->oxorder__oxuserid = new oxField("_testUserId", oxField::T_RAW);

        $this->assertNull($oOrder->UNITsetPayment('noSuchPayment'));

        $myDb = oxDb::getDb();
        $sSql = "select oxuserid from oxuserpayments where oxuserid = '_testUserId' and oxpaymentsid = 'noSuchPayment' ";

        $this->assertEquals(null, $myDb->getOne($sSql));
    }


    public function testSetPaymentFromCC()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->oxorder__oxuserid = new oxField("_testUserId", oxField::T_RAW);

        $oUserpayment = $oOrder->UNITsetPayment('oxidcreditcard');

        $this->assertEquals("_testUserId", $oUserpayment->oxuserpayments__oxuserid->value);
        $this->assertEquals("oxidcreditcard", $oUserpayment->oxuserpayments__oxpaymentsid->value);
        $this->assertEquals("kktype__@@kknumber__@@kkmonth__@@kkyear__@@kkname__@@kkpruef__@@", $oUserpayment->oxuserpayments__oxvalue->value);
        $this->assertEquals("Kreditkarte", $oUserpayment->oxpayments__oxdesc->value);
        $this->assertEquals(6, count($oUserpayment->aDynValues));
    }

    public function testSetPaymentWithDynValues()
    {
        $aDynVal = array("kktype" => "visa", "kknumber" => "12345", "kkmonth" => "11", "kkyear" => "2008", "kkname" => "testName", "kkpruef" => "56789");
        $this->getSession()->setVariable('dynvalue', $aDynVal);

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->oxorder__oxuserid = new oxField();

        $oUserpayment = $oOrder->UNITsetPayment('oxidcreditcard');

        $sValue = "kktype__visa@@kknumber__12345@@kkmonth__11@@kkyear__2008@@kkname__testName@@kkpruef__56789@@";
        $this->assertEquals($sValue, $oUserpayment->oxuserpayments__oxvalue->value);
        $this->assertEquals(6, count($oUserpayment->aDynValues));
    }

    // #756M
    public function testPreserveOrderPaymentDynValues()
    {
        $aDynVal = array("lsbankname" => "SekundesBakas", "lsblz" => "11122233", "lsktonr" => "AA11222200003333444455", "lsktoinhaber" => "aaaaabbbbb");
        $sValue = "lsbankname__SekundesBakas@@lsblz__11122233@@lsktonr__AA11222200003333444455@@lsktoinhaber__aaaaabbbbb@@";

        $this->getSession()->setVariable('dynvalue', $aDynVal);

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->oxorder__oxuserid = new oxField();

        $oUserpayment = $oOrder->UNITsetPayment('oxiddebitnote');

        $this->assertEquals($sValue, $oUserpayment->oxuserpayments__oxvalue->value);
        $this->assertEquals(4, count($oUserpayment->aDynValues));

        $this->getSession()->deleteVariable('dynvalue');

        $oUserpayment = $oOrder->UNITsetPayment('oxiddebitnote');
        $this->assertEquals($sValue, $oUserpayment->oxuserpayments__oxvalue->value);
        $this->assertEquals(4, count($oUserpayment->aDynValues));
    }

    // FS#1661
    public function testUpdateWishlistUpdateAmount()
    {

        $oBasketItem = $this->getProxyClass("oxBasketItem");
        $oBasketItem->setNonPublicVar('_sProductId', '2000');
        $oBasketItem->setNonPublicVar('_dAmount', 1);
        $oBasketItem->setWishId('oxdefaultadmin');
        $aBasketItems[] = $oBasketItem;

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId');

        $oDB = oxDb::getDb();
        $sSql = "insert into oxuserbaskets (OXID, OXUSERID, OXTITLE) VALUES ('_testUserBasketId','oxdefaultadmin','wishlist')";
        $oDB->execute($sSql);
        $sSql = "insert into oxuserbasketitems (OXID, OXBASKETID, OXARTID, OXAMOUNT) VALUES ('_testUserBasketItemId', '_testUserBasketId', '2000', '3')";
        $oDB->execute($sSql);

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");

        $oOrder->UNITupdateWishlist($aBasketItems, $oUser);

        $sSql = "select oxamount from oxuserbasketitems where oxartid = '2000' and oxbasketid = '_testUserBasketId'";
        $iRes = $oDB->getOne($sSql);
        $this->assertEquals(2, $iRes);
    }

    public function testUpdateWishlistUpdateNegativeAmount()
    {

        $oBasketItem = $this->getProxyClass("oxBasketItem");
        $oBasketItem->setNonPublicVar('_sProductId', '2000');
        $oBasketItem->setNonPublicVar('_dAmount', 5);
        $oBasketItem->setWishId('oxdefaultadmin');
        $aBasketItems[] = $oBasketItem;

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId');

        $oDB = oxDb::getDb();
        $sSql = "insert into oxuserbaskets (OXID, OXUSERID, OXTITLE) VALUES ('_testUserBasketId','oxdefaultadmin','wishlist')";
        $oDB->execute($sSql);
        $sSql = "insert into oxuserbasketitems (OXID, OXBASKETID, OXARTID, OXAMOUNT) VALUES ('_testUserBasketItemId', '_testUserBasketId', '2000', '3')";
        $oDB->execute($sSql);

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");

        $oOrder->UNITupdateWishlist($aBasketItems, $oUser);

        $sSql = "select count(*) from oxuserbasketitems where oxartid = '2000' and oxbasketid = '_testUserBasketId'";
        $iRes = $oDB->getOne($sSql);
        $this->assertEquals(0, $iRes);
    }

    // FS#1661
    public function testUpdateWishlistRemoveFromWishList()
    {
        $oBasketItem = $this->getProxyClass("oxBasketItem");
        $oBasketItem->setNonPublicVar('_sProductId', '2000');
        $oBasketItem->setNonPublicVar('_dAmount', 1);
        $oBasketItem->setWishId('oxdefaultadmin');
        $aBasketItems[] = $oBasketItem;

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId');

        $oDB = oxDb::getDb();
        $sSql = "insert into oxuserbaskets (OXID, OXUSERID, OXTITLE) VALUES ('_testUserBasketId','oxdefaultadmin','wishlist')";
        $oDB->execute($sSql);
        $sSql = "insert into oxuserbasketitems (OXID, OXBASKETID, OXARTID, OXAMOUNT) VALUES ('_testUserBasketItemId', '_testUserBasketId', '2000', '1')";
        $oDB->execute($sSql);

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");

        $oOrder->UNITupdateWishlist($aBasketItems, $oUser);

        $sSql = "select count(*) from oxuserbasketitems where oxartid = '2000' and oxbasketid = '_testUserBasketId'";
        $iRes = $oDB->getOne($sSql);
        $this->assertEquals(0, $iRes);
    }

    // FS#1661
    public function testUpdateWishlistRemoveFromWishListVariant()
    {
        $sArtId = ($this->getConfig()->getEdition() === 'EE') ? '2363' : '2077';

        $oBasketItem = $this->getProxyClass("oxBasketItem");
        $oBasketItem->setNonPublicVar('_sProductId', "$sArtId-01");
        $oBasketItem->setNonPublicVar('_dAmount', 1);
        $oBasketItem->setWishId('oxdefaultadmin');
        $oBasketItem->setWishArticleId($sArtId);
        $aBasketItems[] = $oBasketItem;

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId');

        $oDB = oxDb::getDb();
        $sSql = "insert into oxuserbaskets (OXID, OXUSERID, OXTITLE) VALUES ('_testUserBasketId','oxdefaultadmin','wishlist')";
        $oDB->execute($sSql);
        $sSql = "insert into oxuserbasketitems (OXID, OXBASKETID, OXARTID, OXAMOUNT) VALUES ('_testUserBasketItemId', '_testUserBasketId', '$sArtId', '1')";
        $oDB->execute($sSql);

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");

        $oOrder->UNITupdateWishlist($aBasketItems, $oUser);

        $sSql = "select count(*) from oxuserbasketitems where oxartid = '$sArtId' and oxbasketid = '_testUserBasketId'";
        $iRes = $oDB->getOne($sSql);
        $this->assertEquals(0, $iRes);
    }

    public function testUpdateWishlistWithSpecifiedWishId()
    {
        $oBasketItem = $this->getProxyClass("oxBasketItem");
        $oBasketItem->setNonPublicVar('_sProductId', '1126');
        $oBasketItem->setNonPublicVar('_dAmount', 1);
        $oBasketItem->setWishId('_testUserId');
        $aBasketItems[] = $oBasketItem;

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId');

        $oDB = oxDb::getDb();
        $sSql = "insert into oxuserbaskets (OXID, OXUSERID, OXTITLE) VALUES ('_testUserBasketId','_testUserId','wishlist')";
        $oDB->execute($sSql);
        $sSql = "insert into oxuserbasketitems (OXID, OXBASKETID, OXARTID, OXAMOUNT) VALUES ('_testUserBasketItemId', '_testUserBasketId', '1126', '3')";
        $oDB->execute($sSql);

        $oUser = oxNew('oxuser');
        $oUser->setId("_testUserId");

        $oOrder->UNITupdateWishlist($aBasketItems, $oUser);

        $sSql = "select oxamount from oxuserbasketitems where oxartid = '1126' and oxbasketid = '_testUserBasketId'";
        $iRes = $oDB->getOne($sSql);
        $this->assertEquals(2, $iRes);
    }

    public function testMarkVouchers()
    {
        $oVSerie = oxNew('oxvoucherserie');
        $oVSerie->setId('_testVoucherSerieId');
        $oVSerie->save();
        $oVoucher = oxNew('oxvoucher');
        $oVoucher->setId('_testVoucherId');
        $oVoucher->oxvouchers__oxvoucherserieid = new oxField('_testVoucherSerieId', oxField::T_RAW);
        $oVoucher->save();
        $aVouchers[$oVoucher->getId()] = $oVoucher;

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getVouchers'));
        $oBasket->expects($this->any())->method('getVouchers')->will($this->returnValue($aVouchers));

        $oUser = oxNew('oxUser');
        $oUser->setId('_testUserId');

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId');
        $oOrder->UNITmarkVouchers($oBasket, $oUser);

        $oDB = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $sSQL = "select * from oxvouchers where oxid = '_testVoucherId'";
        $aRes = $oDB->getRow($sSQL);

        //testing loading used vouchers to order object
        $aOrderVouchers = $oOrder->getNonPublicVar('_aVoucherList');
        $this->assertEquals('_testVoucherId', $aOrderVouchers['_testVoucherId']->getId());

        //testing marking vouchers as used
        $this->assertEquals('_testOrderId', $aRes['OXORDERID']);
        $this->assertEquals('_testUserId', $aRes['OXUSERID']);
        $this->assertEquals(date("Y-m-d"), $aRes['OXDATEUSED']);
    }

    public function testSaveOrder()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId2');
        $oOrder->oxorder__oxuserid = new oxField('_testUserId', oxField::T_RAW);

        $oOrder->save();

        $oDB = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $sSql = "select * from oxorder where oxid = '_testOrderId2'";
        $aRes = $oDB->getRow($sSql);
        $this->assertEquals('_testOrderId2', $aRes['OXID']);
        $this->assertEquals('_testUserId', $aRes['OXUSERID']);
    }

    public function testSaveOrderSavesOrderArticles()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->setId('_testOrderArticleId');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1126', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId2', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxamount = new oxField('3', oxField::T_RAW);
        $oOrderArticle->save();

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("getOrderArticles"));
        $oOrder->expects($this->any())->method('getOrderArticles')->will($this->returnValue(array($oOrderArticle)));
        $oOrder->setId('_testOrderId2');
        $oOrder->save();

        $oDB = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $sSql = "select * from oxorderarticles where oxid = '_testOrderArticleId'";
        $aRes = $oDB->getRow($sSql);

        $this->assertEquals('_testOrderArticleId', $aRes['OXID']);
        $this->assertEquals('_testOrderId2', $aRes['OXORDERID']);
        $this->assertEquals(1126, $aRes['OXARTID']);
        $this->assertEquals(3, $aRes['OXAMOUNT']);
    }

    public function testGetDelAddressInfo()
    {
        $this->setRequestParameter('deladrid', '_testDelAddrId');

        $oDelAdress = oxNew('oxBase');
        $oDelAdress->init('oxaddress');
        $oDelAdress->setId('_testDelAddrId');
        $oDelAdress->oxaddress__oxuserid = new oxField('_testUserId', oxField::T_RAW);
        $oDelAdress->oxaddress__oxcountryid = new oxField('a7c40f631fc920687.20179984', oxField::T_RAW);
        $oDelAdress->save();

        $oOrder = oxNew('oxOrder');
        $oDeliveryAddress = $oOrder->getDelAddressInfo();

        $this->assertEquals('_testDelAddrId', $oDeliveryAddress->getId());
        $this->assertEquals('_testUserId', $oDeliveryAddress->oxaddress__oxuserid->value);
        $this->assertEquals('Deutschland', $oDeliveryAddress->oxaddress__oxcountry->value);
    }


    public function testGetDelAddressInfoWithoutDeliveryAddressId()
    {
        $this->setRequestParameter('deladrid', null);

        $oOrder = oxNew('oxOrder');

        $this->assertNull($oOrder->getDelAddressInfo());
    }

    public function testValidateStock()
    {
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxstock = new oxField('2', oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(0, oxField::T_RAW);
        $oArticle->save();

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array('getArticle', 'getAmount'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($oArticle));
        $oBasketItem->expects($this->any())->method('getAmount')->will($this->returnValue(1));
        $aBasketItems[] = $oBasketItem;

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getContents'));
        $oBasket->expects($this->any())->method('getContents')->will($this->returnValue($aBasketItems));

        $oOrder = oxNew('oxOrder');

        try {
            $oOrder->validateStock($oBasket);
        } catch (Exception $e) {
            $this->fail('No exeption shoud be thrown');
        }
    }

    public function testValidateStockThrowsExeptionWhenOutOfStock()
    {
        //$oArticle = oxNew( 'oxArticle' );
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('checkForStock'));
        $oArticle->expects($this->once())->method('checkForStock')->will($this->returnValue(5));
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxstock = new oxField('2', oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(0, oxField::T_RAW);
        $oArticle->save();

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array('getArticle', 'getAmount'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($oArticle));
        $oBasketItem->expects($this->any())->method('getAmount')->will($this->returnValue(3));
        $aBasketItems[] = $oBasketItem;

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getContents'));
        $oBasket->expects($this->any())->method('getContents')->will($this->returnValue($aBasketItems));

        $oOrder = oxNew('oxOrder');

        try {
            $oOrder->validateStock($oBasket);
        } catch (Exception $exception) {
            $this->assertTrue(is_a($exception, \OxidEsales\Eshop\Core\Exception\OutOfStockException::class));
            $this->assertEquals(5, $exception->getRemainingAmount());

            return;
        }

        $this->fail('OutOfStockException exeption shoud be thrown');
    }

    //#1115: Usability Problem during checkout with products without stock
    public function testValidateStockThrowsExeptionWhenOffline()
    {
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field('1', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->oxarticles__oxstockflag = new \OxidEsales\Eshop\Core\Field(2, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->save();

        $oBasketItem = $this->getProxyClass("oxbasketitem");
        $oBasketItem->init('_testArticleId', 1);
        $oBasketItem->setNonPublicVar("_oArticle", null);
        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->setNonPublicVar("_aBasketContents", array($oBasketItem));
        $oArticle->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field(0, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->oxarticles__oxstockflag = new \OxidEsales\Eshop\Core\Field(2, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->save();

        $oOrder = oxNew('oxOrder');

        $this->setExpectedException(\OxidEsales\Eshop\Core\Exception\NoArticleException::class);
        $oOrder->validateStock($oBasket);
    }

    // #1318: exception is thrown if product (not orderable if out of stock) goes out of stock during order process
    public function testValidateStockThrowsExeptionWhenNotBuyable()
    {
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field('1', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->oxarticles__oxstockflag = new \OxidEsales\Eshop\Core\Field(3, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->save();

        $oBasketItem = $this->getProxyClass("oxbasketitem");
        $oBasketItem->init('_testArticleId', 1);
        $oBasketItem->setNonPublicVar("_oArticle", null);
        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->setNonPublicVar("_aBasketContents", array($oBasketItem));
        $oArticle->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field(0, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->save();

        $oOrder = oxNew('oxOrder');
        $this->setExpectedException(\OxidEsales\Eshop\Core\Exception\ArticleInputException::class);
        $oOrder->validateStock($oBasket);
    }

    public function testInsert()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $oDb->Execute("truncate table `oxcounters`");

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setId('_testOrderId2');
        $oOrder->oxorder__oxuserid = new oxField('_testUserId', oxField::T_RAW);
        $oOrder->oxorder__oxtotalnetsum = new oxField('100', oxField::T_RAW);

        $sTestDate = date('Y-m-d H:i:s');
        $this->assertTrue($oOrder->UNITinsert());

        $oDB = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $sSql = "select * from oxorder where oxid = '_testOrderId2'";
        $aRes = $oDB->getRow($sSql);


        $this->assertEquals('_testOrderId2', $aRes['OXID']);
        $this->assertEquals('_testUserId', $aRes['OXUSERID']);
        $this->assertEquals('100', $aRes['OXTOTALNETSUM']);

        $myConfig = $oOrder->getConfig();

        $this->assertTrue($aRes['OXORDERDATE'] >= $sTestDate);
        $this->assertEquals($myConfig->getShopId(), $aRes['OXSHOPID']);

        $this->assertEquals(0, $aRes['OXORDERNR']);
    }

    public function testUpdate()
    {
        $this->_insertTestOrder();
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->load("_testOrderId");
        $oOrder->oxorder__oxsenddate = new \OxidEsales\Eshop\Core\Field("2007/07/07 00:00:00", \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->UNITupdate();

        $sSendDate = '2007-07-07 00:00:00';
        $this->assertEquals($sSendDate, $oOrder->oxorder__oxsenddate->value);
    }

    public function testDelete()
    {
        $oDB = oxDb::getDb();
        $this->_insertTestOrder();

        $oOrderArticle = oxNew(\OxidEsales\Eshop\Application\Model\OrderArticle::class);
        $oOrderArticle->setId('_testOrderArticleId');
        $oOrderArticle->oxorderarticles__oxorderid = new \OxidEsales\Eshop\Core\Field('_testOrderId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxamount = new \OxidEsales\Eshop\Core\Field('2', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->save();

        $oOrder = oxNew('oxOrder');
        $oOrder->load('_testOrderId');

        $this->assertEquals(1, $oOrder->getOrderArticles()->count());
        $this->assertTrue($oOrder->delete());

        $sSql = "select count(*) from oxorder where oxid = '_testOrderId'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals(0, $sStatus);

        $sSql = "select count(*) from oxorderarticles where oxorderid = '_testOrderId'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals(0, $sStatus);

    }

    public function testDeleteRemovesUserPaymentInfo()
    {
        $oDB = oxDb::getDb();
        $this->_insertTestOrder();

        $oOrderArticle = oxNew(\OxidEsales\Eshop\Application\Model\OrderArticle::class);
        $oOrderArticle->setId('_testOrderArticleId');
        $oOrderArticle->oxorderarticles__oxorderid = new \OxidEsales\Eshop\Core\Field('_testOrderId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxamount = new \OxidEsales\Eshop\Core\Field('2', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->save();

        $oPayment = oxNew('oxPayment');
        $oPayment->setId('_testPaymentId');
        $oPayment->save();

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->load('_testOrderId');
        $oOrder->oxorder__oxpaymentid = new \OxidEsales\Eshop\Core\Field('_testPaymentId', \OxidEsales\Eshop\Core\Field::T_RAW);

        $this->assertTrue($oOrder->delete());

        $sSql = "select count(*) from oxuserpayments where oxid='_testPaymentId'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals(0, $sStatus);
    }

    public function testDeleteRestoresArticleStockInfoForNonCanceledOrderArticles()
    {
        $oDB = oxDb::getDb();
        $this->_insertTestOrder();

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxactive = new \OxidEsales\Eshop\Core\Field('1', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field('5', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->save();

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->setId('_testArticleId2');
        $oArticle->oxarticles__oxactive = new \OxidEsales\Eshop\Core\Field('1', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field('5', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->save();

        $oOrderArticle = oxNew(\OxidEsales\Eshop\Application\Model\OrderArticle::class);
        $oOrderArticle->setId('_testOrderArticleId');
        $oOrderArticle->oxorderarticles__oxartid = new \OxidEsales\Eshop\Core\Field('_testArticleId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new \OxidEsales\Eshop\Core\Field('_testOrderId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxamount = new \OxidEsales\Eshop\Core\Field('2', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxstorno = new \OxidEsales\Eshop\Core\Field('0', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->save();

        $oOrderArticle->setId('_testOrderArticleId2');
        $oOrderArticle->oxorderarticles__oxartid = new \OxidEsales\Eshop\Core\Field('_testArticleId2', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new \OxidEsales\Eshop\Core\Field('_testOrderId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxamount = new \OxidEsales\Eshop\Core\Field('2', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxstorno = new \OxidEsales\Eshop\Core\Field('1', \OxidEsales\Eshop\Core\Field::T_RAW); //canceled
        $oOrderArticle->save();

        $this->getConfig()->setConfigParam('blUseStock', true);

        $oOrder = oxNew('oxOrder');
        $oOrder->load('_testOrderId');

        $this->assertEquals(2, $oOrder->getOrderArticles()->count());
        $this->assertTrue($oOrder->delete());

        $sSql = "select oxstock from oxarticles where oxid = '_testArticleId'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals(7, $sStatus);

        $sSql = "select oxstock from oxarticles where oxid = '_testArticleId2'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals(5, $sStatus);
    }

    public function testDeleteDoesNotRestoresArticleWhenStockUsageIsOff()
    {
        $oDB = oxDb::getDb();
        $this->_insertTestOrder();

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxactive = new \OxidEsales\Eshop\Core\Field('1', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field('5', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->save();

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->setId('_testArticleId2');
        $oArticle->oxarticles__oxactive = new \OxidEsales\Eshop\Core\Field('1', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->oxarticles__oxstock = new \OxidEsales\Eshop\Core\Field('5', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticle->save();

        $oOrderArticle = oxNew(\OxidEsales\Eshop\Application\Model\OrderArticle::class);
        $oOrderArticle->setId('_testOrderArticleId');
        $oOrderArticle->oxorderarticles__oxartid = new \OxidEsales\Eshop\Core\Field('_testArticleId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new \OxidEsales\Eshop\Core\Field('_testOrderId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxamount = new \OxidEsales\Eshop\Core\Field('2', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxstorno = new \OxidEsales\Eshop\Core\Field('0', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->save();

        $oOrderArticle->setId('_testOrderArticleId2');
        $oOrderArticle->oxorderarticles__oxartid = new \OxidEsales\Eshop\Core\Field('_testArticleId2', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new \OxidEsales\Eshop\Core\Field('_testOrderId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxamount = new \OxidEsales\Eshop\Core\Field('2', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxstorno = new \OxidEsales\Eshop\Core\Field('1', \OxidEsales\Eshop\Core\Field::T_RAW); //canceled
        $oOrderArticle->save();

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->getConfig()->setConfigParam('blUseStock', false);
        $oOrder->load('_testOrderId');

        $this->assertEquals(2, $oOrder->getOrderArticles()->count());
        $this->assertTrue($oOrder->delete());

        $sSql = "select oxstock from oxarticles where oxid = '_testArticleId'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals(5, $sStatus);

        $sSql = "select oxstock from oxarticles where oxid = '_testArticleId2'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals(5, $sStatus);
    }


    public function testDeleteWithSelectedOrderId()
    {
        $oDB = oxDb::getDb();
        $this->_insertTestOrder();

        $oOrderArticle = oxNew(\OxidEsales\Eshop\Application\Model\OrderArticle::class);
        $oOrderArticle->setId('_testOrderArticleId');
        $oOrderArticle->oxorderarticles__oxorderid = new \OxidEsales\Eshop\Core\Field('_testOrderId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->oxorderarticles__oxamount = new \OxidEsales\Eshop\Core\Field('2', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrderArticle->save();

        $oOrder = oxNew('oxOrder');

        $this->assertTrue($oOrder->delete('_testOrderId'));

        $sSql = "select count(*) from oxorder where oxid = '_testOrderId'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals(0, $sStatus);

        $sSql = "select count(*) from oxorderarticles where oxorderid = '_testOrderId'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals(0, $sStatus);
    }

    public function testDeleteNotExistingOrder()
    {
        $oOrder = oxNew('oxOrder');
        $this->assertFalse($oOrder->delete('_noSuchOrderId'));
    }

    public function testGetInvoiceNum()
    {
        $this->_insertTestOrder();

        $oOrder = oxNew('oxOrder');
        $oOrder->load('_testOrderId');
        $oOrder->oxorder__oxinvoicenr = new \OxidEsales\Eshop\Core\Field(5, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->save();

        $iNum = $oOrder->getInvoiceNum();
        $this->assertEquals(6, $iNum);
    }

    public function testGetNextBillNum()
    {
        $this->_insertTestOrder('_testOrderId');
        $this->_insertTestOrder('_testOrderId1');

        $oOrder = oxNew('oxOrder');
        $oOrder->load('_testOrderId');
        $oOrder->oxorder__oxbillnr = new \OxidEsales\Eshop\Core\Field(999, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->save();

        $oOrder = oxNew('oxOrder');
        $oOrder->load('_testOrderId1');
        $oOrder->oxorder__oxbillnr = new \OxidEsales\Eshop\Core\Field(1000, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->save();

        $iNum = $oOrder->getNextBillNum();
        $this->assertEquals(1001, $iNum);
    }

    public function testVoucherNrList()
    {
        $this->_insertTestOrder();

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->oxvouchers__oxorderid = new \OxidEsales\Eshop\Core\Field('_testOrderId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oVoucher->oxvouchers__oxvouchernr = new \OxidEsales\Eshop\Core\Field('_testVoucherNr', \OxidEsales\Eshop\Core\Field::T_RAW);
        $oVoucher->save();

        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_testOrderId');

        $aRes = $oOrder->getVoucherNrList();

        $this->assertEquals(1, count($aRes));

        $sVoucherNr = reset($aRes);
        $this->assertEquals('_testVoucherNr', $sVoucherNr);
    }

    public function testGetOrderSum()
    {
        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_testOrderId1');
        $oOrder->oxorder__oxtotalordersum = new \OxidEsales\Eshop\Core\Field(100, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->oxorder__oxcurrate = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->save();

        $oOrder->setId('_testOrderId2');
        $oOrder->oxorder__oxtotalordersum = new \OxidEsales\Eshop\Core\Field(150, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->oxorder__oxcurrate = new \OxidEsales\Eshop\Core\Field(0.5, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->save();

        $dRes = $oOrder->getOrderSum();
        $this->assertEquals(100 + 150 / 0.5, $dRes);
    }

    public function testGetOrderSumUsesNotCanceledOrders()
    {
        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_testOrderId1');
        $oOrder->oxorder__oxtotalordersum = new \OxidEsales\Eshop\Core\Field(100, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->oxorder__oxcurrate = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->oxorder__oxstorno = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW); //canceled
        $oOrder->save();

        $oOrder->setId('_testOrderId2');
        $oOrder->oxorder__oxtotalordersum = new \OxidEsales\Eshop\Core\Field(150, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->oxorder__oxcurrate = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->oxorder__oxstorno = new \OxidEsales\Eshop\Core\Field(0, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->save();

        $dRes = $oOrder->getOrderSum();
        $this->assertEquals(150, $dRes);
    }

    public function testGetOrderSumForDifferentShops()
    {
        $myConfig = $this->getConfig();

        $oDB = oxDb::getDb();
        $sSql = "insert into oxorder (oxid, oxshopid, oxtotalordersum) values('_testOrderId1', '123', '100') ";
        $oDB->execute($sSql);

        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_testOrderId2');
        $oOrder->oxorder__oxshopid = new \OxidEsales\Eshop\Core\Field($myConfig->getShopId(), \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->oxorder__oxtotalordersum = new \OxidEsales\Eshop\Core\Field(150, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->oxorder__oxcurrate = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->save();

        $dRes = $oOrder->getOrderSum();
        $this->assertEquals(150, $dRes);
    }

    public function testGetOrderSumOnlyCurrentDay()
    {
        $myConfig = $this->getConfig();

        $oDB = oxDb::getDb();

        $sCurrentDate = date("Y-m-d");
        $sShopId = $myConfig->getShopId();
        $sSql = "insert into oxorder (oxid, oxshopid, oxtotalordersum, oxorderdate, oxcurrate) values('_testOrderId1', '$sShopId', '100', '$sCurrentDate', '1') ";
        $oDB->execute($sSql);

        $sSql = "insert into oxorder (oxid, oxshopid, oxtotalordersum, oxorderdate, oxcurrate) values('_testOrderId2', '$sShopId', '150', '2005-01-15', '1') ";
        $oDB->execute($sSql);

        $oOrder = oxNew('oxOrder');

        $dRes = $oOrder->getOrderSum(true);
        $this->assertEquals(100, $dRes);
    }

    public function testGetOrderCnt()
    {
        $oOrder = oxNew('oxOrder');

        $oOrder->setId('_testOrderId1');
        $oOrder->save();

        $oOrder->setId('_testOrderId2');
        $oOrder->save();

        $iRes = $oOrder->getOrderCnt();
        $this->assertEquals(2, $iRes);
    }

    public function testGetOrderCntUsesNotCanceledOrders()
    {
        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_testOrderId1');
        $oOrder->oxorder__oxstorno = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW); //canceled
        $oOrder->save();

        $oOrder->setId('_testOrderId2');
        $oOrder->oxorder__oxstorno = new \OxidEsales\Eshop\Core\Field(0, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oOrder->save();

        $iRes = $oOrder->getOrderCnt();
        $this->assertEquals(1, $iRes);
    }

    public function testGetOrderCntForDifferentShops()
    {
        $oDB = oxDb::getDb();
        $sSql = "insert into oxorder (oxid, oxshopid) values('_testOrderId1', '123') ";
        $oDB->execute($sSql);

        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_testOrderId2');
        $oOrder->save();

        $iRes = $oOrder->getOrderCnt();
        $this->assertEquals(1, $iRes);
    }

    public function testGetOrderCntOnlyCurrentDay()
    {
        $myConfig = $this->getConfig();

        $oDB = oxDb::getDb();

        $sCurrentDate = date("Y-m-d");
        $sShopId = $myConfig->getShopId();
        $sSql = "insert into oxorder (oxid, oxshopid, oxorderdate) values('_testOrderId1', '$sShopId', '$sCurrentDate') ";
        $oDB->execute($sSql);

        $sSql = "insert into oxorder (oxid, oxshopid, oxorderdate) values('_testOrderId2', '$sShopId', '2005-01-15') ";
        $oDB->execute($sSql);

        $oOrder = oxNew('oxOrder');

        $iRes = $oOrder->getOrderCnt(true);
        $this->assertEquals(1, $iRes);
    }

    public function testCheckOrderExist()
    {
        $this->_insertTestOrder();

        $oOrder = $this->getProxyClass("oxOrder");

        $this->assertTrue($oOrder->UNITcheckOrderExist('_testOrderId'));
    }

    public function testCheckOrderExistWithNotExistingOrder()
    {
        $oOrder = $this->getProxyClass("oxOrder");

        $this->assertFalse($oOrder->UNITcheckOrderExist('_noExistingOrderId'));
    }

    public function testCheckOrderExistWithoutParams()
    {
        $oOrder = $this->getProxyClass("oxOrder");

        $this->assertFalse($oOrder->UNITcheckOrderExist());
    }

    public function testSendOrderByEmail()
    {
        oxEmailHelper::$blRetValue = true;
        oxAddClassModule('oxEmailHelper', 'oxemail');

        $oUser = oxNew('oxUser');
        $oUser->setId('_testUserId');

        $oBasket = oxNew('oxBasket');
        $oBasket->setOrderId('_testOrderId');

        $oPayment = oxNew('oxPayment');
        $oPayment->setId('_testPaymentId');

        $oOrder = $this->getProxyClass("oxOrder");

        $iRes = $oOrder->UNITsendOrderByEmail($oUser, $oBasket, $oPayment);

        $this->assertEquals(1, $iRes);

        //check if mail sending functions were called
        $this->assertTrue(oxEmailHelper::$blSendToUserWasCalled);
        $this->assertTrue(oxEmailHelper::$blSendToOwnerWasCalled);

        //checking if email functions were called with correct param
        $this->assertEquals(oxEmailHelper::$oOwnerOrder, $oOrder);
        $this->assertEquals(oxEmailHelper::$oUserOrder, $oOrder);

        //checking if oUser, oBasket, oPayment were attached to oOrder
        $this->assertEquals($oUser, $oOrder->getNonPublicVar('_oUser'));
        $this->assertEquals($oBasket, $oOrder->getNonPublicVar('_oBasket'));
        $this->assertEquals($oPayment, $oOrder->getNonPublicVar('_oPayment'));

    }

    public function testSendOrderByEmailWhenMailingFails()
    {
        oxEmailHelper::$blRetValue = false;
        oxAddClassModule('oxEmailHelper', 'oxemail');

        $oOrder = $this->getProxyClass("oxOrder");

        $iRes = $oOrder->UNITsendOrderByEmail(null, null, null);

        $this->assertEquals(0, $iRes);
    }

    public function testGetOrderUserCached()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setNonPublicVar('_oUser', '123');

        $this->assertEquals('123', $oOrder->getOrderUser());
    }

    public function testGetBasket()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setNonPublicVar('_oBasket', '123');

        $this->assertEquals('123', $oOrder->getBasket());
    }

    public function testGetPayment()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setNonPublicVar('_oPayment', '123');

        $this->assertEquals('123', $oOrder->getPayment());
    }

    public function testGetVoucherList()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setNonPublicVar('_aVoucherList', '123');

        $this->assertEquals('123', $oOrder->getVoucherList());
    }

    public function testGetDelSet()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setNonPublicVar('_oDelSet', '123');

        $this->assertEquals('123', $oOrder->getDelSet());
    }

    public function testGetPaymentType()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setNonPublicVar('_oPaymentType', '123');

        $this->assertEquals('123', $oOrder->getPaymentType());
    }

    public function testGetPaymentTypeWhenItDoesNotExistMustReturnNull()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxpaymentid = new \OxidEsales\Eshop\Core\Field('xxx');
        $this->assertFalse($oOrder->getPaymentType());
    }

    public function testGetPaymentTypeDynValue()
    {
        $sDyn = 'lsbankname__visa@@lsblz__12345@@lsktonr__56789@@lsktoinhaber__testName@@';
        $aDynVal = array("lsbankname" => "visa", "lsblz" => "12345", "lsktonr" => "56789", "lsktoinhaber" => "testName");
        $this->getSession()->setVariable('dynvalue', $aDynVal);

        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->oxorder__oxuserid = new \OxidEsales\Eshop\Core\Field();

        $oOrder->UNITsetPayment('oxiddebitnote');

        $aDynVal = oxRegistry::getUtils()->assignValuesFromText($sDyn);
        $this->assertEquals($aDynVal, $oOrder->getPaymentType()->aDynValues);
    }

    public function testGetLastUserPaymentType()
    {
        $myConfig = $this->getConfig();

        $oDB = oxDb::getDb();

        $sCurrentDate = date("Y-m-d");
        $sShopId = $myConfig->getShopId();
        $sSql = "insert into oxorder (oxid, oxshopid, oxpaymenttype, oxorderdate, oxuserid) values('_testOrderId1', '$sShopId', 'test1', '$sCurrentDate', 'test') ";
        $oDB->execute($sSql);

        $sSql = "insert into oxorder (oxid, oxshopid, oxpaymenttype, oxorderdate, oxuserid) values('_testOrderId2', '$sShopId', 'test2', '2005-01-15', 'test') ";
        $oDB->execute($sSql);

        $oOrder = oxNew('oxOrder');

        $iRes = $oOrder->getLastUserPaymentType('test');
        $this->assertEquals('test1', $iRes);
    }

    public function testGetGiftCard()
    {
        $oOrder = $this->getProxyClass("oxOrder");
        $oOrder->setNonPublicVar('_oGiftCard', '123');

        $this->assertEquals('123', $oOrder->getGiftCard());
    }

    public function testGetTotalOrderSum()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxtotalordersum = new \OxidEsales\Eshop\Core\Field(123);

        $this->assertEquals('123.00', $oOrder->getTotalOrderSum());
    }

    public function testGetBillCountry()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxbillcountryid = new \OxidEsales\Eshop\Core\Field("a7c40f631fc920687.20179984");

        // test magic getter
        $this->assertEquals('Deutschland', $oOrder->oxorder__oxbillcountry->value);

        // test getter
        $this->assertEquals('Deutschland', $oOrder->getBillCountry()->value);
    }

    public function testGetDelCountry()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxdelcountryid = new \OxidEsales\Eshop\Core\Field("a7c40f6321c6f6109.43859248");

        // test magic getter
        $this->assertEquals('Schweiz', $oOrder->oxorder__oxdelcountry->value);

        // test getter
        $this->assertEquals('Schweiz', $oOrder->getDelCountry()->value);
    }

    /**
     * Test case for #0002255: Item Discounts add multiple times when editing Orders
     *
     * @return null
     */
    public function testForBugEntry2255()
    {
        $sShopId = $this->getConfig()->getBaseShopId();

        // bundle type discount
        $oDiscount = oxNew('oxDiscount');
        $oDiscount->setAdminMode(false);
        $oDiscount->setId("_testDiscountId");
        $oDiscount->oxdiscount__oxshopid = new \OxidEsales\Eshop\Core\Field($sShopId);
        $oDiscount->oxdiscount__oxactive = new \OxidEsales\Eshop\Core\Field(1);
        $oDiscount->oxdiscount__oxtitle = new \OxidEsales\Eshop\Core\Field("Test discount");
        $oDiscount->oxdiscount__oxamount = new \OxidEsales\Eshop\Core\Field(1);
        $oDiscount->oxdiscount__oxamountto = new \OxidEsales\Eshop\Core\Field(9999);
        $oDiscount->oxdiscount__oxaddsumtype = new \OxidEsales\Eshop\Core\Field('itm');
        $oDiscount->oxdiscount__oxaddsum = new \OxidEsales\Eshop\Core\Field(0);

        $itmArtId = ($this->getConfig()->getEdition() === 'EE') ? '1487' : '1126';
        $oDiscount->oxdiscount__oxitmartid = new \OxidEsales\Eshop\Core\Field($itmArtId);

        $oDiscount->oxdiscount__oxitmamount = new \OxidEsales\Eshop\Core\Field(1);
        $oDiscount->oxdiscount__oxitmmultiple = new \OxidEsales\Eshop\Core\Field(0);
        $oDiscount->save();

        $oOrder = oxNew('oxOrder');
        $oOrder->setAdminMode(false);
        $oOrder->setId("_testOrderId");
        $oOrder->oxorder__oxshopid = new \OxidEsales\Eshop\Core\Field($sShopId);
        $oOrder->oxorder__oxuserid = new \OxidEsales\Eshop\Core\Field("oxdefaultadmin");
        $oOrder->oxorder__oxorderdate = new \OxidEsales\Eshop\Core\Field("2011-01-17 14:04:49");
        $oOrder->oxorder__oxordernr = new \OxidEsales\Eshop\Core\Field(); ///
        $oOrder->oxorder__oxbillcompany = new \OxidEsales\Eshop\Core\Field("Your Company Name");
        $oOrder->oxorder__oxbillemail = new \OxidEsales\Eshop\Core\Field("admin@oxid-esales.com");
        $oOrder->oxorder__oxbillfname = new \OxidEsales\Eshop\Core\Field("John");
        $oOrder->oxorder__oxbilllname = new \OxidEsales\Eshop\Core\Field("Doe");
        $oOrder->oxorder__oxbillstreet = new \OxidEsales\Eshop\Core\Field("Maple Street");
        $oOrder->oxorder__oxbillstreetnr = new \OxidEsales\Eshop\Core\Field(10);
        $oOrder->oxorder__oxbillustidstatus = new \OxidEsales\Eshop\Core\Field(1);
        $oOrder->oxorder__oxbillcity = new \OxidEsales\Eshop\Core\Field("Any City");
        $oOrder->oxorder__oxbillcountryid = new \OxidEsales\Eshop\Core\Field("a7c40f631fc920687.20179984");
        $oOrder->oxorder__oxbillstateid = new \OxidEsales\Eshop\Core\Field("BW");
        $oOrder->oxorder__oxbillzip = new \OxidEsales\Eshop\Core\Field("9041");
        $oOrder->oxorder__oxbillfon = new \OxidEsales\Eshop\Core\Field("217-8918712");
        $oOrder->oxorder__oxbillfax = new \OxidEsales\Eshop\Core\Field("217-8918713");
        $oOrder->oxorder__oxbillsal = new \OxidEsales\Eshop\Core\Field("MR");
        $oOrder->oxorder__oxpaymentid = new \OxidEsales\Eshop\Core\Field("k2ef91eeaa104dd9fa65de08a71cfc83");
        $oOrder->oxorder__oxpaymenttype = new \OxidEsales\Eshop\Core\Field("oxidcashondel");
        $oOrder->oxorder__oxtotalnetsum = new \OxidEsales\Eshop\Core\Field("46.81");
        $oOrder->oxorder__oxtotalbrutsum = new \OxidEsales\Eshop\Core\Field("55.7");
        $oOrder->oxorder__oxtotalordersum = new \OxidEsales\Eshop\Core\Field("67.1");
        $oOrder->oxorder__oxartvat1 = new \OxidEsales\Eshop\Core\Field(19);
        $oOrder->oxorder__oxartvatprice1 = new \OxidEsales\Eshop\Core\Field(8.89);
        $oOrder->oxorder__oxartvat2 = new \OxidEsales\Eshop\Core\Field(0);
        $oOrder->oxorder__oxartvatprice2 = new \OxidEsales\Eshop\Core\Field(0);
        $oOrder->oxorder__oxdelcost = new \OxidEsales\Eshop\Core\Field(3.9);
        $oOrder->oxorder__oxdelvat = new \OxidEsales\Eshop\Core\Field(19);
        $oOrder->oxorder__oxpaycost = new \OxidEsales\Eshop\Core\Field(7.5);
        $oOrder->oxorder__oxpayvat = new \OxidEsales\Eshop\Core\Field(19);
        $oOrder->oxorder__oxcurrency = new \OxidEsales\Eshop\Core\Field("EUR");
        $oOrder->oxorder__oxcurrate = new \OxidEsales\Eshop\Core\Field(1);
        $oOrder->oxorder__oxtransstatus = new \OxidEsales\Eshop\Core\Field("OK");
        $oOrder->oxorder__oxdeltype = new \OxidEsales\Eshop\Core\Field("oxidstandard");
        $oOrder->save();

        $oOrderArticle = oxNew(\OxidEsales\Eshop\Application\Model\OrderArticle::class);
        $oOrderArticle->setAdminMode(false);
        $oOrderArticle->setId("_testOrderArticleId");
        $oOrderArticle->oxorderarticles__oxorderid = new \OxidEsales\Eshop\Core\Field("_testOrderId");
        $oOrderArticle->oxorderarticles__oxamount = new \OxidEsales\Eshop\Core\Field(2);
        $oOrderArticle->oxorderarticles__oxartid = new \OxidEsales\Eshop\Core\Field("2275-01");
        $oOrderArticle->oxorderarticles__oxartnum = new \OxidEsales\Eshop\Core\Field("2275-01");
        $oOrderArticle->oxorderarticles__oxtitle = new \OxidEsales\Eshop\Core\Field("BBQ Grill TONNE");
        $oOrderArticle->oxorderarticles__oxnetprice = new \OxidEsales\Eshop\Core\Field(46.806722689076);
        $oOrderArticle->oxorderarticles__oxbrutprice = new \OxidEsales\Eshop\Core\Field(55.7);
        $oOrderArticle->oxorderarticles__oxvatprice = new \OxidEsales\Eshop\Core\Field(8.8932773109244);
        $oOrderArticle->oxorderarticles__oxvat = new \OxidEsales\Eshop\Core\Field(19);
        $oOrderArticle->oxorderarticles__oxprice = new \OxidEsales\Eshop\Core\Field(27.85);
        $oOrderArticle->oxorderarticles__oxnprice = new \OxidEsales\Eshop\Core\Field(27.85);
        $oOrderArticle->oxorderarticles__oxordershopid = new \OxidEsales\Eshop\Core\Field(23.403361344538);
        $oOrderArticle->save();

        $this->setRequestParameter('oxid', "_testOrderId");
        $this->setRequestParameter('aOrderArticles', array("_testOrderArticleId"));

        $oView = oxNew('order_article');
        $oView->updateOrder();
        $oView->updateOrder();
        $oView->updateOrder();

        // checking how many order articles after update
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(*) from oxorderarticles where oxorderid = '_testOrderId'"));
        $this->assertEquals(3, oxDb::getDb()->getOne("select sum(oxamount) from oxorderarticles where oxorderid = '_testOrderId'"));
    }

    /**
     * Testing formatted total net sum getter
     *
     * @return null
     */
    public function testGetFormattedTotalNetSum()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxcurrency = new \OxidEsales\Eshop\Core\Field("EUR");
        $oOrder->oxorder__oxtotalnetsum = new \OxidEsales\Eshop\Core\Field(100);

        return $this->assertEquals("100,00", $oOrder->getFormattedTotalNetSum());
    }

    /**
     * Testing formatted total brut sum getter
     *
     * @return null
     */
    public function testGetFormattedTotalBrutSum()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxcurrency = new \OxidEsales\Eshop\Core\Field("EUR");
        $oOrder->oxorder__oxtotalbrutsum = new \OxidEsales\Eshop\Core\Field(100);

        return $this->assertEquals("100,00", $oOrder->getFormattedTotalBrutSum());
    }

    /**
     * Testing formatted Delivery cost sum getter
     *
     * @return null
     */
    public function testGetFormattedeliveryCost()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxcurrency = new \OxidEsales\Eshop\Core\Field("EUR");
        $oOrder->oxorder__oxdelcost = new \OxidEsales\Eshop\Core\Field(100);

        return $this->assertEquals("100,00", $oOrder->getFormattedeliveryCost());
    }

    /**
     * Testing formatted pay cost sum getter
     *
     * @return null
     */
    public function testGetFormattedPayCost()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxcurrency = new \OxidEsales\Eshop\Core\Field("EUR");
        $oOrder->oxorder__oxpaycost = new \OxidEsales\Eshop\Core\Field(100);

        return $this->assertEquals("100,00", $oOrder->getFormattedPayCost());
    }

    /**
     * Testing formatted wrap cost sum getter
     *
     * @return null
     */
    public function testGetFormattedWrapCost()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxcurrency = new \OxidEsales\Eshop\Core\Field("EUR");
        $oOrder->oxorder__oxwrapcost = new \OxidEsales\Eshop\Core\Field(100);

        return $this->assertEquals("100,00", $oOrder->getFormattedWrapCost());
    }

    /**
     * Testing formatted gift card cost getter
     *
     * @return null
     */
    public function testGetFormattedGiftCardCost()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxcurrency = new \OxidEsales\Eshop\Core\Field("EUR");
        $oOrder->oxorder__oxgiftcardcost = new \OxidEsales\Eshop\Core\Field(120);

        return $this->assertEquals("120,00", $oOrder->getFormattedGiftCardCost());
    }

    /**
     * Testing formatted total vouchers getter
     *
     * @return null
     */
    public function testGetFormattedTotalVouchers()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxcurrency = new \OxidEsales\Eshop\Core\Field("EUR");
        $oOrder->oxorder__oxvoucherdiscount = new \OxidEsales\Eshop\Core\Field(100);

        return $this->assertEquals("100,00", $oOrder->getFormattedTotalVouchers());
    }

    /**
     * Testing formatted Discount getter
     *
     * @return null
     */
    public function testGetFormattedDiscount()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxcurrency = new \OxidEsales\Eshop\Core\Field("EUR");
        $oOrder->oxorder__oxdiscount = new \OxidEsales\Eshop\Core\Field(100);

        return $this->assertEquals("100,00", $oOrder->getFormattedDiscount());
    }

    /**
     * Testing formatted total sum from last order getter
     *
     * @return null
     */
    public function testGetFormattedTotalOrderSum()
    {
        $oOrder = oxNew('oxorder');
        $oOrder->oxorder__oxcurrency = new \OxidEsales\Eshop\Core\Field("EUR");
        $oOrder->oxorder__oxtotalordersum = new \OxidEsales\Eshop\Core\Field(100);

        return $this->assertEquals("100,00", $oOrder->getFormattedTotalOrderSum());
    }

    /**
     * Testing oxORder::getShipmentTrackingUrl()
     *
     * @return null
     */
    public function testGetShipmentTrackingUrl()
    {
        $sExpected = "http://www.dpd.de/cgi-bin/delistrack?typ=1&amp;lang=de&amp;pknr=123";

        $oOrder = oxNew('oxOrder');
        $oOrder->oxorder__oxtrackcode = new \OxidEsales\Eshop\Core\Field(123);
        $this->assertEquals($sExpected, $oOrder->getShipmentTrackingUrl());
    }

    /**
     * Testing oxORder::getShipmentTrackingUrl()
     *
     * @return null
     */
    public function testGetShipmentTrackingUrlCodeNotAdded()
    {
        $oOrder = oxNew('oxOrder');
        $oOrder->oxorder__oxtrackcode = new \OxidEsales\Eshop\Core\Field(false);
        $this->assertNull($oOrder->getShipmentTrackingUrl());
    }

    /**
     * Testing oxORder::getShipmentTrackingUrl()
     *
     * @return null
     */
    public function testGetShipmentTrackingUrlNotSet()
    {
        $this->setConfigParam('sParcelService', false);

        $oOrder = oxNew('oxOrder');
        $oOrder->oxorder__oxtrackcode = new \OxidEsales\Eshop\Core\Field(123);
        $this->assertNull($oOrder->getShipmentTrackingUrl());
    }

    /**
     * Testing oxORder::getShipmentTrackingUrl()
     *
     * @return null
     */
    public function testGetShipmentTrackingUrlWrongPlaceHolder()
    {
        $this->setConfigParam('sParcelService', "http://www.dpd.de/cgi-bin/delistrack?typ=1&amp;lang=de&amp;pknr=ID");

        $oOrder = oxNew('oxOrder');
        $oOrder->oxorder__oxtrackcode = new \OxidEsales\Eshop\Core\Field(123);
        $this->assertEquals('http://www.dpd.de/cgi-bin/delistrack?typ=1&amp;lang=de&amp;pknr=ID', $oOrder->getShipmentTrackingUrl());
    }

    /**
     * Testing oxOrder::_convertVat( $sVat )
     *
     * @return null
     */
    public function testconvertVat()
    {
        $oOrder = oxNew('oxOrder');
        $this->assertEquals(7.6, $oOrder->UNITconvertVat("7,6"));
        $this->assertEquals(7.6, $oOrder->UNITconvertVat("7.6"));
        $this->assertEquals(76.01, $oOrder->UNITconvertVat("76,01"));
        $this->assertEquals(76.01, $oOrder->UNITconvertVat("7.6,01"));
        $this->assertEquals(76.01, $oOrder->UNITconvertVat("76.01"));
        $this->assertEquals(76.01, $oOrder->UNITconvertVat("7,6.01"));
    }

    /**
     * Testing oxOrder DB table changes for field OXIP adding ipv6 support
     *
     * @return null
     */
    public function testOrderIpAddress()
    {
        $sId = '_testOrderId';
        $myConfig = $this->getConfig();

        $ipv6 = '2001:cdba:0000:0000:0000:0000:3257:9652';
        //set order
        $this->_oOrder = oxNew("oxOrder");
        $this->_oOrder->setId($sId);
        $this->_oOrder->oxorder__oxshopid = new \OxidEsales\Eshop\Core\Field($myConfig->getShopId(), \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->_oOrder->oxorder__oxuserid = new \OxidEsales\Eshop\Core\Field("_testUserId", \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->_oOrder->oxorder__oxbillcountryid = new \OxidEsales\Eshop\Core\Field("a7c40f6320aeb2ec2.72885259");
        $this->_oOrder->oxorder__oxdelcountryid = new \OxidEsales\Eshop\Core\Field("a7c40f631fc920687.20179984", \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->_oOrder->oxorder__oxdeltype = new \OxidEsales\Eshop\Core\Field('_testDeliverySetId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->_oOrder->oxorder__oxpaymentid = new \OxidEsales\Eshop\Core\Field('_testPaymentId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->_oOrder->oxorder__oxpaymenttype = new \OxidEsales\Eshop\Core\Field('_testPaymentId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->_oOrder->oxorder__oxcardid = new \OxidEsales\Eshop\Core\Field('_testWrappingId', \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->_oOrder->oxorder__oxip = new \OxidEsales\Eshop\Core\Field($ipv6);
        $this->_oOrder->save();
        //testing ip address
        $oOrder = oxNew("oxorder");
        $oOrder->load($sId);
        $this->assertEquals($ipv6, $oOrder->oxorder__oxip->value);
    }

    /**
     * Test case for the noticelist if the current user is a guest (so we don't want one)
     *
     * #6141
     * @see https://bugs.oxid-esales.com/view.php?id=6141
     */
    public function testFinalizeOrderDoNotHandleNoticelistWhenUserIsGuest()
    {
        /** @var \oxUser $user */
        $user = oxNew('oxUser');
        $user->setId('_testUserId');
        $user->setPassword();
        $user->save();

        /** @var \oxBasket $basket */
        $basket = oxNew('oxBasket');
        $basket->addToBasket("2000", 1.0);

        $order = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('validateOrder', '_setUser', '_loadFromBasket', '_sendOrderByEmail'));
        $order->expects($this->once())->method('_setUser');
        $order->expects($this->once())->method('_loadFromBasket');
        $order->expects($this->once())->method('_sendOrderByEmail');

        $order->finalizeOrder($basket, $user);

        $query = 'select 1 from oxuserbaskets where oxtitle = "noticelist"';
        $this->assertFalse((bool) oxDb::getDb()->getOne($query));
    }

    /**
     * Test case for the noticelist if the current user hasn't a noticelist (so we don't want one)
     *
     * #6141
     * @see https://bugs.oxid-esales.com/view.php?id=6141
     */
    public function testFinalizeOrderDoNotHandleNoticelistWhenThereIsNoNoticelist()
    {
        /** @var oxUser $oUser */
        $user = oxNew('oxUser');
        $user->setId('_testUserId');
        $user->setPassword('foobar');
        $user->save();

        /** @var \oxBasket $basket */
        $basket = oxNew('oxBasket');
        $basket->addToBasket("2000", 1.0);

        $order = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('validateOrder', '_setUser', '_loadFromBasket', '_sendOrderByEmail'));
        $order->expects($this->once())->method('_setUser');
        $order->expects($this->once())->method('_loadFromBasket');
        $order->expects($this->once())->method('_sendOrderByEmail');

        $order->finalizeOrder($basket, $user);

        $query = 'select 1 from oxuserbaskets where oxtitle = "noticelist"';
        $this->assertFalse((bool) oxDb::getDb()->getOne($query));
    }


    /**
     * Test case for the noticelist if the current user has a noticelist.
     *
     * #6141
     * @see https://bugs.oxid-esales.com/view.php?id=6141
     */
    public function testFinalizeOrderHandleNoticelist()
    {
        $query = 'insert into oxuserbaskets (oxid, oxtitle) values ("_test", "noticelist")';
        oxDb::getDb()->execute($query);

        /** @var oxUser $oUser */
        $user = oxNew('oxUser');
        $user->setId('_testUserId');
        $user->setPassword('foobar');
        $user->save();

        /** @var \oxBasket $basket */
        $basket = oxNew('oxBasket');
        $basket->addToBasket("2000", 1.0);

        $order = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('validateOrder', '_setUser', '_loadFromBasket', '_sendOrderByEmail'));
        $order->expects($this->once())->method('_setUser');
        $order->expects($this->once())->method('_loadFromBasket');
        $order->expects($this->once())->method('_sendOrderByEmail');

        $order->finalizeOrder($basket, $user);

        $query = 'select 1 from oxuserbaskets where oxid = "_test" and oxtitle = "noticelist"';
        $this->assertTrue((bool) oxDb::getDb()->getOne($query));
    }
}
