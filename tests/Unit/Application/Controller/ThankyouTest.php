<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing thankyou class
 */
class ThankyouTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxarticles');
        parent::tearDown();
    }

    public function testGetBasket()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        /** @var oxSession $mySession */
        $mySession = oxRegistry::getSession();
        $oBasket = oxNew('oxBasket');
        //$this->getSession()->setVariable( 'basket', $oBasket );
        $mySession->setBasket($oBasket);
        $oThankyou = $this->getProxyClass('thankyou');

        /** @var \PHPUnit\Framework\MockObject\MockObject $utilsMock */
        $utilsMock = $this->createStub(\OxidEsales\Eshop\Core\Utils::class, [], ["redirect"]);
        $utilsMock->expects($this->any())->method('redirect')->willThrowException(new \Exception("expected redirect"));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utilsMock);

        try {
            $oThankyou->init();
        } catch (\Exception $e) {
            $this->assertEquals("expected redirect", $e->getMessage());
        }

        $this->assertEquals($oBasket, $oThankyou->getBasket());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage expected redirect
     */
    public function testThankYouRedirectOnNoOrder()
    {
        $oThankyou = $this->getProxyClass('thankyou');

        /** @var \PHPUnit\Framework\MockObject\MockObject $utilsMock */
        $utilsMock = $this->createStub(\OxidEsales\Eshop\Core\Utils::class, [], ["redirect"]);
        $utilsMock->expects($this->any())->method('redirect')->willThrowException(new \Exception("expected redirect"));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utilsMock);

        $oThankyou->init();
    }

    public function testGetCurrencyCovIndex()
    {
        $oThankyou = $this->getProxyClass('thankyou');
        $this->assertEquals(1, $oThankyou->getCurrencyCovIndex());
    }

    public function testGetIPaymentBasket()
    {
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(10.12);
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getPrice'));
        $oBasket->expects($this->once())->method('getPrice')->will($this->returnValue($oPrice));

        $oThankyou = $this->getProxyClass('thankyou');
        $oThankyou->setNonPublicVar('_oBasket', $oBasket);

        $this->assertEquals(10.12 * 100, $oThankyou->getIPaymentBasket());
    }

    public function testGetIPaymentAccount()
    {
        $this->getConfig()->setConfigParam('iShopID_iPayment_Account', 'testAccount');
        $oThankyou = $this->getProxyClass('thankyou');
        $this->assertEquals('testAccount', $oThankyou->getIPaymentAccount());
    }

    public function testGetIPaymentUser()
    {
        $this->getConfig()->setConfigParam('iShopID_iPayment_User', 'testUser');
        $oThankyou = $this->getProxyClass('thankyou');
        $this->assertEquals('testUser', $oThankyou->getIPaymentUser());
    }

    public function testGetIPaymentPassword()
    {
        $this->getConfig()->setConfigParam('iShopID_iPayment_Passwort', 'testPasswort');
        $oThankyou = $this->getProxyClass('thankyou');
        $this->assertEquals('testPasswort', $oThankyou->getIPaymentPassword());
    }

    public function testGetMailError()
    {
        $this->setRequestParameter('mailerror', 'testShop');
        $oThankyou = $this->getProxyClass('thankyou');
        $this->assertEquals('testShop', $oThankyou->getMailError());
    }

    public function testGetOrder()
    {
        $myDB = oxDb::getDb();
        $sInsert = "Insert into oxorder (`oxid`, `oxordernr`) values ('_test', '158')";
        $myDB->Execute($sInsert);

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getOrderId'));
        $oBasket->expects($this->once())->method('getOrderId')->will($this->returnValue('_test'));

        $oThankyou = $this->getProxyClass('thankyou');
        $oThankyou->setNonPublicVar('_oBasket', $oBasket);

        $this->assertEquals('_test', $oThankyou->getOrder()->getId());
    }

    public function testGetAlsoBoughtTheseProducts()
    {
        $this->oArticle = $this->getProxyClass('oxarticle');
        $this->oArticle->load('1126');
        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array('getArticle'));
        $oBasketItem->expects($this->once())->method('getArticle')->will($this->returnValue($this->oArticle));

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getContents'));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array($oBasketItem)));

        $oThankyou = $this->getProxyClass('thankyou');
        $oThankyou->setNonPublicVar('_oBasket', $oBasket);
        $this->assertNull($oThankyou->getAlsoBoughtTheseProducts());
    }

    // #1276: If product is "If out out stock, offline" and remaining stock is ordered, "Shp offline" error is shown in Order step 5
    public function testRender()
    {
        $this->oArticle = $this->getProxyClass('oxarticle');
        $this->oArticle->setId('_testArt');
        $this->oArticle->oxarticles__oxprice = new oxField(15.5, oxField::T_RAW);
        $this->oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $this->oArticle->oxarticles__oxtitle = new oxField("test", oxField::T_RAW);
        $this->oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $this->oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $this->oArticle->save();


        $oBasketItem = $this->getProxyClass('oxbasketitem');
        $oBasketItem->setNonPublicVar('_sProductId', '_testArt');
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getContents', 'getProductsCount', 'getOrderId'));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array($oBasketItem)));
        $oBasket->expects($this->once())->method('getProductsCount')->will($this->returnValue(1));
        $oBasket->expects($this->once())->method('getOrderId')->will($this->returnValue(1));
        $oThankyou = $this->getProxyClass('thankyou');
        $oThankyou->setNonPublicVar('_oBasket', $oBasket);
        $oThankyou->render();
    }

    // #2580: Checking if after order unregistered user contact data were deleted
    public function testRender_resetUnregisteredUser()
    {
        $oUser = oxNew("oxuser");
        $oUser->oxuser__oxpassword = new oxField("");

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('getProductsCount'));
        $oBasket->expects($this->once())->method('getProductsCount')->will($this->returnValue(1));

        $oThankyou = $this->getProxyClass('thankyou');
        $oThankyou->setNonPublicVar('_oBasket', $oBasket);
        $oThankyou->setUser($oUser);

        $this->getSession()->setVariable("usr", "testValue1");
        $this->getSession()->setVariable("dynvalue", "testValue2");

        $oThankyou->render();

        $this->assertFalse(oxRegistry::getSession()->hasVariable("usr"));
        $this->assertFalse(oxRegistry::getSession()->hasVariable("dynvalue"));
    }

    public function testGetActionClassName()
    {
        $oThankyou = $this->getProxyClass('thankyou');
        $this->assertEquals('start', $oThankyou->getActionClassName());
    }

    /**
     * Testing Thankyou::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oTh = oxNew('Thankyou');

        $this->assertEquals(1, count($oTh->getBreadCrumb()));
    }

    /**
     * Testing Thankyou::getCountryISO3()
     *
     * @return null
     */
    public function testGetCountryISO3()
    {
        $oOrder = oxNew('oxOrder');
        $oOrder->oxorder__oxbillcountryid = new oxField('a7c40f631fc920687.20179984');

        $oTh = $this->getMock(\OxidEsales\Eshop\Application\Controller\ThankYouController::class, array('getOrder'));
        $oTh->expects($this->any())->method('getOrder')->will($this->returnValue($oOrder));

        $this->assertEquals('DEU', $oTh->getCountryISO3());
    }
}
