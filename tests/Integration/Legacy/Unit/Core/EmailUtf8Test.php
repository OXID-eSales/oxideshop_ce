<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\core;

use \oxField;
use oxStr;

class EmailUtf8Test extends \PHPUnit\Framework\TestCase
{
    /**
     * Testing email charset
     */
    public function testGetCharset()
    {
        $oEmail = oxNew('oxEmail');
        $this->assertSame("UTF-8", $oEmail->getCharset());
    }

    /**
     * Testing email charset
     */
    public function testGetCurrency()
    {
        $oEmail = oxNew('oxEmail');
        $this->assertSame("€", $oEmail->getCurrency()->sign);
    }

    /**
     * Test sending ordering mail to user
     */
    public function testSendOrderEmailToUser()
    {
        $oPrice = $this->getMock('oxprice');
        $oPrice->method('getPrice')->willReturn(256);
        $oPrice->method('getBruttoPrice')->willReturn(8);

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getPrice', 'getUnitPrice', 'getRegularUnitPrice', 'getTitle']);
        $oBasketItem->method('getPrice')->willReturn($oPrice);
        $oBasketItem->method('getUnitPrice')->willReturn($oPrice);
        $oBasketItem->method('getRegularUnitPrice')->willReturn($oPrice);
        $oBasketItem->method('getTitle')->willReturn("testarticle");

        // insert test article
        $oArticle = oxNew("oxArticle");
        $oArticle->setId('_testArticleId');
        $oArticle->setId('_testArticleId');

        $oArticle->oxarticles__oxtitle = new oxField();

        $aBasketContents[] = $oBasketItem;
        $aBasketArticles[] = $oArticle;

        $oPrice->setPrice(0);

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ["getBasketArticles", "getContents", "getCosts", "getBruttoSum"]);
        $oBasket->method('getBasketArticles')->willReturn($aBasketArticles);
        $oBasket->method('getContents')->willReturn($aBasketContents);
        $oBasket->method('getCosts')->willReturn($oPrice);
        $oBasket->method('getBruttoSum')->willReturn(7);

        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\UserPayment::class);
        $oPayment->oxpayments__oxdesc = new oxField("testPaymentDesc");

        $oUser = oxNew("oxuser");
        $oUser->setId('_testUserId');

        $oUser->oxuser__oxusername = new oxField('username@useremail.nl', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('testUserFName', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('testUserLName', oxField::T_RAW);

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, ["getOrderUser", "getBasket", "getPayment"]);
        $oOrder->method('getOrderUser')->willReturn($oUser);
        $oOrder->method('getBasket')->willReturn($oBasket);
        $oOrder->method('getPayment')->willReturn($oPayment);

        $oOrder->oxorder__oxordernr = new oxField('987654321', oxField::T_RAW);
        $oOrder->oxorder__oxbillfname = new oxField('');
        $oOrder->oxorder__oxbilllname = new oxField('');
        $oOrder->oxorder__oxbilladdinfo = new oxField('');
        $oOrder->oxorder__oxbillstreet = new oxField('');
        $oOrder->oxorder__oxbillcity = new oxField('');
        $oOrder->oxorder__oxbillcountry = new oxField('');
        $oOrder->oxorder__oxbillcompany = new oxField('');
        $oOrder->oxorder__oxdeltype = new oxField("oxidstandard");

        $oShop = oxNew("oxshop");
        $oShop->load($this->getConfig()->getShopId());

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendMail", "getShop", "getOrderFileList"]);
        $oEmail->expects($this->once())->method('sendMail')->willReturn(true);
        $oEmail->method('getShop')->willReturn($oShop);
        $oEmail->method('getOrderFileList')->willReturn(false);

        $blRet = $oEmail->sendOrderEmailToUser($oOrder);
        $this->assertTrue($blRet);

        $sBody = $oEmail->getBody();
        //uncoment line to generate template for checking mail body
        //file_put_contents (__DIR__ .'/../testData/email_templates/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        $oStr = oxStr::getStr();

        // checking if there are some utf-8 strings

        // translation check
        $this->assertGreaterThan(0, $oStr->strpos($sBody, "Grußkarte:"));

        // euro sign check
        $this->assertGreaterThan(0, $oStr->strpos($sBody, "256,00 €"));

        // strings, that comes from oxcontent
        $this->assertGreaterThan(0, $oStr->strpos($sBody, "Vielen Dank für Ihre Bestellung!"));
        $this->assertGreaterThan(0, $oStr->strpos($sBody, "Bitte fügen Sie hier Ihre vollständige Anbieterkennzeichnung ein."));
    }

    public function testSendForgotPwdEmailIsCaseInsensitive()
    {
        $realEmailAddress = 'admin';
        $userProvidedEmailAddress = 'ADMIN';

        $oEmailMock = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["send", "setRecipient"]);
        $oEmailMock->expects($this->once())->method("setRecipient")->with($realEmailAddress, 'John Doe');
        $oEmailMock->expects($this->once())->method("send")->willReturn(true);

        $oEmailMock->sendForgotPwdEmail($userProvidedEmailAddress);
    }

    /**
     * Test for bug #0008618
     *
     * @dataProvider dataProviderTestSendForgotPwdEmailSendsToEmailAddressStoredInDatabase
     */
    public function testSendForgotPwdEmailSendsToEmailAddressStoredInDatabase($bogusEmailAddress)
    {
        $realEmailAddress = 'admin';

        $oEmailMock = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["send", "setRecipient"]);
        $oEmailMock->expects($this->atLeastOnce())->method("setRecipient")->with($realEmailAddress, 'John Doe');
        $oEmailMock->expects($this->atLeastOnce())->method("send")->willReturn(true);

        $oEmailMock->sendForgotPwdEmail($bogusEmailAddress);
    }

    public function dataProviderTestSendForgotPwdEmailSendsToEmailAddressStoredInDatabase(): \Iterator
    {
        yield ['Admin'];
        yield ['Àdmin'];
        yield ['Ádmin'];
        yield ['Âdmin'];
        yield ['Ãdmin'];
        yield ['Ädmin'];
        yield ['Ådmin'];
        yield ['àdmin'];
        yield ['ádmin'];
        yield ['âdmin'];
        yield ['ãdmin'];
        yield ['ädmin'];
        yield ['ådmin'];
        yield ['Ādmin'];
        yield ['Ądmin'];
        yield ['ądmin'];
    }
}
