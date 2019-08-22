<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Step\Basket;
use OxidEsales\Codeception\Module\Translation\Translator;

class CheckoutProcessCest
{
    /**
     * @group basketfrontend
     *
     * @param AcceptanceTester $I
     */
    public function checkBasketFlyout(AcceptanceTester $I)
    {
        $basket = new Basket($I);
        $I->wantToTest('basket flyout');

        $homePage = $I->openShop();

        //add Product to basket
        $basketItem1 = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 1,
            'price' => '50,00 €'
        ];

        $basket->addProductToBasket($basketItem1['id'], 1);
        $homePage = $homePage->seeMiniBasketContains([$basketItem1], '50,00 €', 1);

        $basketItem1 = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 2,
            'price' => '100,00 €'
        ];

        $basketItem2 = [
            'id' => '1001',
            'title' => 'Test product 1 [EN] šÄßüл',
            'amount' => 1,
            'price' => '100,00 €'
        ];
        $basket->addProductToBasket($basketItem1['id'], 1);
        $basket->addProductToBasket($basketItem2['id'], 1);
        $userCheckoutPage = $homePage->seeMiniBasketContains([$basketItem1, $basketItem2], '200,00 €', 3)
            ->openCheckout();

        $breadCrumbName = Translator::translate("ADDRESS");
        $userCheckoutPage->seeOnBreadCrumb($breadCrumbName);

        $userData = $this->getExistingUserData();
        $homePage = $userCheckoutPage->openHomePage()
            ->loginUser($userData['userLoginName'], $userData['userPassword']);

        $paymentCheckoutPage = $homePage->openMiniBasket()->openCheckout();

        $breadCrumbName = Translator::translate("PAY");
        $paymentCheckoutPage->seeOnBreadCrumb($breadCrumbName);
    }

    /**
     * @group basketfrontend
     *
     * @param AcceptanceTester $I
     */
    public function createOrder(AcceptanceTester $I)
    {
        $I->wantToTest("simple order steps (without any special cases)");
        $I->updateConfigInDatabase('blShowVATForDelivery', false, 'bool');
        $I->updateConfigInDatabase('blShowVATForPayCharge', false, 'bool');
        $basket = new Basket($I);

        $userData = $this->getExistingUserData();

        $basketItem1 = [
            'id' => '1001',
            'title' => 'Test product 1 [EN] šÄßüл',
            'amount' => 1,
            'totalPrice' => '100,00 €'
        ];

        $basketItem2 = [
            'id' => '1002-2',
            'title' => 'Test product 2 [EN] šÄßüл',
            'amount' => 1,
            'totalPrice' => '67,00 €'
        ];

        //add Product to basket
        $basket->addProductToBasket($basketItem1['id'], 1);
        $basket->addProductToBasket($basketItem2['id'], 1);

        $homePage = $I->openShop()->loginUser($userData['userLoginName'], $userData['userPassword']);
        $basketPage = $homePage->openMiniBasket()->openBasketDisplay();
        $basketPage = $basketPage->addCouponToBasket('123123');
        $basketPage = $basketPage->openGiftSelection(1)
            ->selectWrapping(1, 'testwrapping')
            ->selectCard('testcard')
            ->addGreetingMessage('Greeting card text')
            ->submitChanges();
        $I->see('Greeting card text');
        $userCheckoutPage = $basketPage->goToNextStep();
        $paymentPage = $userCheckoutPage->enterOrderRemark('remark text')->goToNextStep();

        $I->see(Translator::translate("SELECT_SHIPPING_METHOD"));

        $orderPage = $paymentPage->selectPayment('oxidcashondel')
            ->goToNextStep()
            ->validateRemarkText('remark text');

        $I->see('Test wrapping [EN] šÄßüл');
        $I->see('Greeting card text');
        $I->dontSee(Translator::translate('HERE_YOU_CAN_ENETER_MESSAGE'));
        $userCheckoutPage = $orderPage->editUserAddress();
        $orderPage = $userCheckoutPage->enterOrderRemark('my message')->goToNextStep()->goToNextStep();
        $orderPage->validateRemarkText('my message');

        $paymentPage = $orderPage->editPaymentMethod();
        $orderPage = $paymentPage->selectPayment('oxidpayadvance')->goToNextStep();

        $orderPage->validateShippingMethod('Standard');
        $orderPage->validatePaymentMethod('Cash in advance');
        $paymentPage = $orderPage->editPaymentMethod();
        $orderPage = $paymentPage->selectPayment('oxidcashondel')->goToNextStep();

        $orderPage->validateShippingMethod('Standard');
        $orderPage->validatePaymentMethod('COD (Cash on Delivery)');
        $orderPage->validateOrderItems([$basketItem1, $basketItem2]);
        $orderPage->validateCoupon('123123', '-83,50 €');
        $orderPage->validateVat(['4,55 €', '5,35 €']);
        $I->see('73,60 €', $orderPage->basketSummaryNet);
        $I->see('167,00 €', $orderPage->basketSummaryGross);
        $I->see('0,00 €', $orderPage->basketShippingGross);
        $I->see('7,50 €', $orderPage->basketPaymentGross);
        $I->see('0,90 €', $orderPage->basketWrappingGross);
        $I->see('0,20 €', $orderPage->basketGiftCardGross);
        $I->see('92,10 €', $orderPage->basketTotalPrice);




      /*  $this->assertEquals("101,00 €", $this->getText("//tr[@id='cartItem_1']//td[5]/s"), "price with discount not shown in basket");
        $this->assertEquals("136,40 €", $this->getText("basketTotalNetto"), "Net price changed or didn't displayed");
        $this->assertEquals("8,29 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"), "VAT 10% changed");
        $this->assertEquals("10,16 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"), "VAT 19%changed");
        $this->assertEquals("163,00 €", $this->getText("basketTotalProductsGross"), "Brut price changed or didn't displayed");
        $this->assertEquals("%COUPON% (%NUMBER_2% 222222)", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]/th")), "Coupon changed or didn't displayed");
        $this->assertEquals("0,00 €", $this->getText("basketDeliveryGross"), "Shipping price changed or didn't displayed");
        $this->assertEquals("7,50 €", $this->getText("basketPaymentGross"), "Payment price changed or didn't displayed");
        $this->assertEquals("0,90 €", $this->getText("basketWrappingGross"), "Wrapping price changed or didn't displayed");
        $this->assertEquals("0,20 €", $this->getText("basketGiftCardGross"), "Card price changed or didn't displayed");
        $this->assertEquals("163,45 €", $this->getText("basketGrandTotal"), "Grand total price changed or didn't displayed");*/

    }

    /**
     * @group basketfrontend
     *
     * @param AcceptanceTester $I
     */
    public function buyOutOfStockNotBuyableProductDuringOrder(AcceptanceTester $I)
    {
        $basket = new Basket($I);
        $I->wantToTest('if no fatal errors or exceptions are thrown, but an error message is shown, if the same 
        product was sold out by other user during the checkout');

        $userData = $this->getExistingUserData();

        $homePage = $I->openShop()->loginUser($userData['userLoginName'], $userData['userPassword']);

        $basketItem1 = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 5,
            'totalPrice' => '250,00 €'
        ];

        $basketItem2 = [
            'id' => '1001',
            'title' => 'Test product 1 [EN] šÄßüл',
            'amount' => 1,
            'totalPrice' => '100,00 €'
        ];

        //add Product to basket
        /** @var \OxidEsales\Codeception\Page\Checkout\Basket $basketPage */
        $basket->addProductToBasket($basketItem1['id'], 5);
        $basket->addProductToBasket($basketItem2['id'], 1);
        $basketPage = $homePage->openMiniBasket()
            ->openBasketDisplay()
            ->seeBasketContains([$basketItem1, $basketItem2], '350,00 €');

        // making product out of stock now
        $I->updateInDatabase('oxarticles', ["oxstock" => '3', "oxstockflag" => '3'], ["oxid" => '1000']);

        $basketPage->updateProductAmount(7);

        $I->see(Translator::translate("ERROR_MESSAGE_OUTOFSTOCK_OUTOFSTOCK"));

        $basketItem1 = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 3,
            'totalPrice' => '150,00 €'
        ];
        $paymentPage = $basketPage->seeBasketContains([$basketItem1, $basketItem2], '250,00 €')
            ->goToNextStep()
            ->goToNextStep();

        //in second step, product availability is not checked.
        $I->see(Translator::translate("SELECT_SHIPPING_METHOD"));

        $orderPage = $paymentPage->selectPayment('oxidcashondel')
            ->goToNextStep();

        // someone bought some more items while client filled steps
        $I->updateInDatabase('oxarticles', ["oxstock" => '1', "oxstockflag" => '3'], ["oxid" => '1000']);

        $orderPage->submitOrder();

        //in second step, product availability is not checked.
        $I->see(Translator::translate("ERROR_MESSAGE_OUTOFSTOCK_OUTOFSTOCK"));

        // someone bought all items while client filled steps
        $I->updateInDatabase('oxarticles', ["oxstock" => '0', "oxstockflag" => '3'], ["oxid" => '1000']);

        $orderPage->submitOrder();

        //in second step, product availability is not checked.
        $I->see(Translator::translate("ERROR_MESSAGE_ARTICLE_ARTICLE_NOT_BUYABLE"));

        $orderPage->submitOrder();

        $breadCrumb = Translator::translate('ORDER_COMPLETED');
        $orderPage->seeOnBreadCrumb($breadCrumb);

        //cleanUp data
        $I->updateInDatabase('oxarticles', ["oxstock" => '15', "oxstockflag" => '1'], ["oxid" => '1000']);
    }

    /**
     * @group basketfrontend
     *
     * @param AcceptanceTester $I
     */
    public function checkMinimalOrderPrice(AcceptanceTester $I)
    {
        $I->wantToTest('minimal order price in checkout process (min order sum is 49 €)');

        // prepare data for test
        $I->updateInDatabase('oxdelivery', ["OXTITLE_1" => 'OXTITLE'], ["OXTITLE_1" => '']);
        $I->updateInDatabase('oxdiscount', ["OXACTIVE" => 1], ["OXID" => 'testcatdiscount']);

        $I->updateConfigInDatabase('iMinOrderPrice', '49', 'str');
        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 1,
            'totalPrice' => '50,00 €'
        ];

        $userData = $this->getExistingUserData();

        $homePage = $I->openShop();

        //add Product to basket
        $basket = new Basket($I);
        $basket->addProductToBasket($productData['id'], 1);
        $basketPage = $homePage->openMiniBasket()
            ->openBasketDisplay()
            ->seeBasketContains([$productData], '50,00 €');
        $I->dontSee(Translator::translate('MIN_ORDER_PRICE') . ' 49,00 €');
        $I->see(Translator::translate('CONTINUE_TO_NEXT_STEP'));

        $basketPage = $basketPage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $I->see(Translator::translate('MIN_ORDER_PRICE') . ' 49,00 €');
        $I->dontSee(Translator::translate('CONTINUE_TO_NEXT_STEP'));

        $basketPage = $basketPage->updateProductAmount(2);
        $I->dontSee(Translator::translate('MIN_ORDER_PRICE') . ' 49,00 €');
        $I->see(Translator::translate('CONTINUE_TO_NEXT_STEP'));

        $basketPage = $basketPage->addCouponToBasket('123123');
        $I->see(Translator::translate('MIN_ORDER_PRICE') . ' 49,00 €');
        $I->dontSee(Translator::translate('CONTINUE_TO_NEXT_STEP'));

        $basketPage = $basketPage->removeCouponFromBasket();
        $I->dontSee(Translator::translate('MIN_ORDER_PRICE') . ' 49,00 €');
        $userCheckoutPage = $basketPage->goToNextStep();
        $breadCrumbName = Translator::translate("ADDRESS");
        $userCheckoutPage->seeOnBreadCrumb($breadCrumbName);
        $I->updateInDatabase('oxdiscount', ["OXACTIVE" => 0], ["OXID" => 'testcatdiscount']);
    }

    /**
     * @group basketfrontend
     *
     * @param AcceptanceTester $I
     */
    public function buyProductWithBundledItem(AcceptanceTester $I)
    {
        $basket = new Basket($I);
        $I->wantToTest('bundled product');

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 1,
            'totalPrice' => '50,00 €'
        ];

        $bundledProductData = [
            'id' => '1001',
            'title' => 'Test product 1 [EN] šÄßüл',
            'amount' => '+1'
        ];

        $this->prepareTestDataForBundledProduct($I, $productData['id'], $bundledProductData['id']);

        $homePage = $I->openShop();

        //add Product to basket
        $basket->addProductToBasket($productData['id'], 1);
        $homePage->openMiniBasket()
            ->openBasketDisplay()
            ->seeBasketContains([$productData], '50,00 €')
            ->seeBasketContainsBundledProduct($bundledProductData, 2);

        $this->removeBundleFromProduct($I, $productData['id']);
    }

    /**
     * @return mixed
     */
    private function getExistingUserData()
    {
        return \Codeception\Util\Fixtures::get('existingUser');
    }

    /**
     * @param AcceptanceTester $I
     * @param string $productId
     * @param string $bundledProductId
     */
    private function prepareTestDataForBundledProduct(AcceptanceTester $I, $productId, $bundledProductId)
    {
        $I->updateInDatabase('oxarticles', ["OXBUNDLEID" => $bundledProductId], ["OXID" => $productId]);
    }

    /**
     * @param AcceptanceTester $I
     * @param string           $productId
     */
    private function removeBundleFromProduct(AcceptanceTester $I, $productId)
    {
        $I->updateInDatabase('oxarticles', ["OXBUNDLEID" => ''], ["OXID" => $productId]);
    }
}
