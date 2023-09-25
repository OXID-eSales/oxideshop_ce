<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Account\UserAccount;
use OxidEsales\Codeception\Page\Checkout\OrderCheckout;
use OxidEsales\Codeception\Page\Details\ProductDetails;
use OxidEsales\Codeception\Step\Basket;
use OxidEsales\Codeception\Step\UserRegistrationInCheckout;
use OxidEsales\Facts\Facts;

/**
 * @group basketfrontend
 */
final class CheckoutProcessCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->updateConfigInDatabase('blShowVATForDelivery', false, 'bool');
        $I->updateConfigInDatabase('blShowVATForPayCharge', false, 'bool');
        $I->updateConfigInDatabase('blShowVATForWrapping', false, 'bool');
    }

    public function checkBasketFlyout(AcceptanceTester $I): void
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
        $homePage = $homePage->seeMiniBasketContains([$basketItem1], '50,00 €', '1');

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
        $userCheckoutPage = $homePage->seeMiniBasketContains([$basketItem1, $basketItem2], '200,00 €', '3')
            ->openCheckout();

        $breadCrumbName = Translator::translate('ADDRESS');
        $userCheckoutPage->seeOnBreadCrumb($breadCrumbName);

        $userData = $this->getExistingUserData();
        $homePage = $userCheckoutPage->openHomePage()
            ->loginUser($userData['userLoginName'], $userData['userPassword']);

        $paymentCheckoutPage = $homePage->openMiniBasket()->openCheckout();

        $breadCrumbName = Translator::translate('PAY');
        $paymentCheckoutPage->seeOnBreadCrumb($breadCrumbName);
    }

    public function createOrder(AcceptanceTester $I): void
    {
        $I->wantToTest('simple order steps (without any special cases)');

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
        $homePage = $I->openShop();

        //add Product to basket
        $basket->addProductToBasket($basketItem1['id'], 1);
        $basket->addProductToBasket($basketItem2['id'], 1);

        $homePage = $homePage->loginUser($userData['userLoginName'], $userData['userPassword']);
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

        $I->see(Translator::translate('PAYMENT_METHOD'));

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
        $paymentPage = $orderPage->editShippingMethod();
        $orderPage = $paymentPage->selectPayment('oxidcashondel')->goToNextStep();

        $orderPage->validateShippingMethod('Standard');
        $orderPage->validatePaymentMethod('COD (Cash on Delivery)');
        $orderPage->validateOrderItems([$basketItem1, $basketItem2]);
        $orderPage->validateCoupon('123123', '-83,50 €');
        $orderPage->validateVat(['4,55 €', '5,35 €']);
        $orderPage->validateTotalPrice([
            'net' => '73,60 €',
            'gross' => '167,00 €',
            'shipping' => '0,00 €',
            'payment' => '7,50 €',
            'total' => '92,10 €',
        ]);
        $orderPage->validateWrappingPrice('0,90 €');
        $orderPage->validateGiftCardPrice('0,20 €');

        $I->updateInDatabase('oxvouchers', ['oxreserved' => 0], ['OXVOUCHERNR' => '123123']);
    }

    /**
     * @group todo_add_clean_cache_after_database_update
     */
    public function buyOutOfStockNotBuyableProductDuringOrder(AcceptanceTester $I): void
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
        $I->updateInDatabase('oxarticles', ['oxstock' => '3', 'oxstockflag' => '3'], ['oxid' => '1000']);

        $basketPage->updateProductAmount(7);

        $I->see(Translator::translate('ERROR_MESSAGE_OUTOFSTOCK_OUTOFSTOCK'));

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
        $I->see(Translator::translate('PAYMENT_METHOD'));

        $orderPage = $paymentPage->selectPayment('oxidcashondel')
            ->goToNextStep();

        // someone bought some more items while client filled steps
        $I->updateInDatabase('oxarticles', ['oxstock' => '1', 'oxstockflag' => '3'], ['oxid' => '1000']);

        $orderPage->submitOrder();

        //in second step, product availability is not checked.
        $I->see(Translator::translate('ERROR_MESSAGE_OUTOFSTOCK_OUTOFSTOCK'));

        // someone bought all items while client filled steps
        $I->updateInDatabase('oxarticles', ['oxstock' => '0', 'oxstockflag' => '3'], ['oxid' => '1000']);

        $orderPage->submitOrder();

        //in second step, product availability is not checked.
        $I->see(Translator::translate('ERROR_MESSAGE_ARTICLE_ARTICLE_NOT_BUYABLE'));

        $orderPage->submitOrderSuccessfully();

        //cleanUp data
        $I->updateInDatabase('oxarticles', ['oxstock' => '15', 'oxstockflag' => '1'], ['oxid' => '1000']);
    }

    public function checkMinimalOrderPrice(AcceptanceTester $I): void
    {
        $I->wantToTest('minimal order price in checkout process (min order sum is 49 €)');

        // prepare data for test
        $I->updateInDatabase('oxdelivery', ['OXTITLE_1' => 'OXTITLE'], ['OXTITLE_1' => '']);
        $I->updateInDatabase('oxdiscount', ['OXACTIVE' => 1], ['OXID' => 'testcatdiscount']);

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
        $basketPage->seeNextStep();

        $basketPage = $basketPage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $I->see(Translator::translate('MIN_ORDER_PRICE') . ' 49,00 €');
        $basketPage->dontSeeNextStep();

        $productData['amount'] = 2;
        $productData['totalPrice'] = '90,00 €';
        $basketPage = $basketPage->updateProductAmount(2)
            ->seeBasketContains([$productData], '90,00 €');
        $I->dontSee(Translator::translate('MIN_ORDER_PRICE') . ' 49,00 €');
        $basketPage->seeNextStep();

        $basketPage = $basketPage->addCouponToBasket('123123');
        $I->see(Translator::translate('MIN_ORDER_PRICE') . ' 49,00 €');
        $basketPage->dontSeeNextStep();

        $basketPage = $basketPage->removeCouponFromBasket();
        $I->dontSee(Translator::translate('MIN_ORDER_PRICE') . ' 49,00 €');
        $userCheckoutPage = $basketPage->goToNextStep();
        $breadCrumbName = Translator::translate('ADDRESS');
        $userCheckoutPage->seeOnBreadCrumb($breadCrumbName);
        $I->updateInDatabase('oxdiscount', ['OXACTIVE' => 0], ['OXID' => 'testcatdiscount']);
        $I->updateInDatabase('oxvouchers', ['oxreserved' => 0], ['OXVOUCHERNR' => '123123']);
    }

    public function buyProductWithBundledItem(AcceptanceTester $I): void
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

    public function checkGuestUserNameSwitching(AcceptanceTester $I): void
    {
        $I->wantToTest('guest checkout with username switching');
        $I->updateConfigInDatabase('blShowBirthdayFields', true, 'bool');

        $basket = new Basket($I);
        $userRegistration = new UserRegistrationInCheckout($I);
        $email1 = 'abc@def.gh';
        $email2 = 'xyz@def.gh';

        /** Start guest1 checkout with email1, then logout */
        $basket->addProductToBasketAndOpenUserCheckout('1000', 10);
        $userRegistration->createNotRegisteredUserInCheckout(
            $email1,
            $this->getUserFormData(),
            $this->getUserAddressFormData()
        );
        $I->clearShopCache();

        /** Start guest2 checkout with email2 */
        $basket->addProductToBasketAndOpenUserCheckout('1000', 100);
        $paymentPage = $userRegistration->createNotRegisteredUserInCheckout(
            $email2,
            $this->getUserFormData(),
            $this->getUserAddressFormData()
        );

        /** Check both accounts are present in DB */
        $I->seeInDatabase('oxuser', ['oxusername' => $email1]);
        $I->seeInDatabase('oxuser', ['oxusername' => $email2]);

        /** Check guest2 can use email1 (@see #0006965) */
        $paymentPage->goToPreviousStep()
            ->openUserBillingAddressForm()
            ->modifyUserName($email1)
            ->goToNextStep();

        /** Check user2 is removed from DB */
        $I->seeInDatabase('oxuser', ['oxusername' => $email1]);
        $I->dontSeeInDatabase('oxuser', ['oxusername' => $email2]);

        /** Re-open and re-submit user form without changes, check payment methods are available (@see #0007109) */
        $paymentPage->goToPreviousStep()
            ->goToNextStep()
            ->selectPayment('oxidcashondel');
    }

    public function checkCreateShippingAddress(AcceptanceTester $I): void
    {
        $I->wantToTest('creating shipping address during authenticated user`s checkout');
        $basket = new Basket($I);
        $userData = $this->getExistingUserData();
        $existingProductId = '1001';
        $userShippingAddress = [
            'userSalutation' => 'Mrs',
            'userFirstName' => 'Some first name',
            'userLastName' => 'Some last name',
            'companyName' => 'Some company',
            'street' => 'Some street',
            'streetNr' => '1-1',
            'ZIP' => '1234',
            'city' => 'Some city',
            'fonNr' => '111-111-1',
            'faxNr' => '111-111-111-1',
            'countryId' => 'Germany',
            'stateId' => 'Berlin',
        ];

        $homePage = $I->openShop();
        $homePage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $basket->addProductToBasket($existingProductId, 1);
        $paymentPage = $homePage->openMiniBasket()
            ->openBasketDisplay()
            ->goToNextStep();

        $paymentPage->openShippingAddressForm();
        $paymentPage->enterShippingAddressData($userShippingAddress);

        $paymentPage->goToNextStep()
            ->goToNextStep()
            ->validateUserDeliveryAddress($userShippingAddress);
    }

    public function checkForceIdDisabledDuringCheckout(AcceptanceTester $I): void
    {
        $I->wantToTest('Change session during payment');
        $I->updateConfigInDatabase('blShowBirthdayFields', true, 'bool');

        $basket = new Basket($I);
        $userRegistration = new UserRegistrationInCheckout($I);
        $email1 = 'abc@def.gh';

        $basket->addProductToBasketAndOpenUserCheckout('1000', 10);
        $userRegistration->createNotRegisteredUserInCheckout(
            $email1,
            $this->getUserFormData(),
            $this->getUserAddressFormData()
        );

        $userSid = $I->grabCookie('sid');
        $I->amOnPage('/index.php?cl=payment&new_user=1&success=1&force_sid=pdgfk373csd38v3uhm02mo4qeu');

        $I->assertEquals($userSid, $I->grabCookie('sid'));
    }

    public function checkNoSessionCookiesCheckout(AcceptanceTester $I): void
    {
        $I->wantToTest('Check if checkout is possible without cookies');
        $I->updateConfigInDatabase('blShowBirthdayFields', true, 'bool');

        file_put_contents(
            (new Facts())->getShopRootPath() . '/cust_config.inc.php',
            '<?php $this->blSessionUseCookies = false;'
        );

        $basket = new Basket($I);
        $userRegistration = new UserRegistrationInCheckout($I);
        $email1 = 'abc@def.gh';

        $basket->addProductToBasketAndOpenUserCheckout('1000', 10);
        $paymentPage = $userRegistration->createNotRegisteredUserInCheckout(
            $email1,
            $this->getUserFormData(),
            $this->getUserAddressFormData()
        );

        $orderPage = $paymentPage->selectPayment('oxidcashondel')
            ->goToNextStep();


        $orderPage->submitOrderSuccessfully();
    }

    public function checkAttributesInBasket(AcceptanceTester $I): void
    {
        $I->wantToTest('Check if attributes are visible in basket');

        $I->updateInDatabase('oxattribute', ['OXDISPLAYINBASKET' => 1], ['OXID' => 'testattribute1']);
        $I->haveInDatabase(
            'oxobject2attribute',
            [
                'OXID' => '1001attribute',
                'OXOBJECTID' => '1001',
                'OXATTRID' => '9438ac75bac3e344628b14bf7ed82c15',
                'OXVALUE' => 'Schwarz',
                'OXVALUE_1' => 'Black',
            ]
        );

        (new Basket($I))->addProductToBasket('1001', 1);

        $I->openShop()->openMiniBasket()->openBasketDisplay()
          ->seeBasketContainsAttribute('attr value 11 [EN] šÄßüл', 1);
    }

    public function vatsInCheckoutSummary(AcceptanceTester $I): void
    {
        $I->wantToTest('enabling configuration for displaying VAT values works on checkout summary');

        $I->amGoingTo('enable displaying the VATs via config');
        $I->updateConfigInDatabase('blShowVATForDelivery', 'true', 'bool');
        $I->updateConfigInDatabase('blShowVATForPayCharge', 'true', 'bool');
        $I->updateConfigInDatabase('blShowVATForWrapping', 'true', 'bool');
        $I->amGoingTo('add some product to the basket to prepare for the checkout');
        (new Basket($I))->addProductToBasket('1000', 3);

        $I->amGoingTo('select additional product wrapping and click-through to the summary');
        $loginData = $this->getExistingUserData();
        $orderCheckout = $I
            ->openShop()
            ->loginUser(
                $loginData['userLoginName'],
                $loginData['userPassword']
            )
            ->openMiniBasket()
            ->openBasketDisplay()
            ->openGiftSelection(1)
            ->selectWrapping(1, 'testwrapping')
            ->submitChanges()
            ->goToNextStep()
            ->goToNextStep()
            ->goToNextStep();

        $I->amGoingTo('validate checkout summary values');
        $orderCheckout->seeVat('5');
        $orderCheckout->seeTotalNet('142,86 €');
        $orderCheckout->seeTotalGross('150,00 €');
        $orderCheckout->seeShippingNet('0,00 €');
        $orderCheckout->seePaymentMethodNet('7,14 €');
        $orderCheckout->seePaymentMethodVat('0,36 €', '5');
        $orderCheckout->seeWrappingNet('2,57 €');
        $orderCheckout->seeWrappingVat('0,13 €');
        $orderCheckout->seeGrandTotal('160,20 €');
    }

    public function modifyShippingAndPaymentMethods(AcceptanceTester $I): void
    {
        $I->wantToTest('switching between payment and shipping methods');

        $I->amGoingTo('make non-zero delivery cost to see it at checkout');
        $I->updateInDatabase(
            'oxdelivery',
            ['OXADDSUM' => 123],
            ['OXID' => 'testdelivery']
        );

        $I->amGoingTo('add some product to the basket to prepare for the checkout');
        $basket = new Basket($I);
        $basket->addProductToBasket('1001', 1);
        $basket->addProductToBasket('1002-2', 1);

        $I->amGoingTo('click-through the checkout to the summary');
        $userData = $this->getExistingUserData();
        $orderCheckoutPage = $I
            ->openShop()
            ->loginUser(
                $userData['userLoginName'],
                $userData['userPassword']
            )
            ->openMiniBasket()
            ->openBasketDisplay()
            ->goToNextStep()
            ->goToNextStep()
            ->goToNextStep();

        $I->amGoingTo('go back to modify the default shipping method and see the change at checkout');
        $paymentPage = $orderCheckoutPage->editShippingMethod();
        $paymentPage->selectShipping('Alternative');
        $orderCheckoutPage = $paymentPage->goToNextStep();
        $orderCheckoutPage->validateShippingMethod('Alternative');
        $orderCheckoutPage->seeShippingGross('123,00 €');
        $orderCheckoutPage->seePaymentSurchargePrice('7,50 €');

        $I->amGoingTo('go back to modify the default payment method and see the change at checkout');
        $paymentPage = $orderCheckoutPage->editPaymentMethod();
        $paymentPage->selectPayment('oxidpayadvance');
        $paymentPage->goToNextStep();
        $orderCheckoutPage->validatePaymentMethod('Cash in advance');
        $orderCheckoutPage->dontSeePaymentSurchargePrice();

        $I->amGoingTo('check if ordering works');
        $orderCheckoutPage->submitOrderSuccessfully();
    }

    public function checkVATOptions(AcceptanceTester $I): void
    {
        $basket = new Basket($I);
        $I->wantToTest('the VAT Options');

        $homePage = $I->openShop();

        // Display shipping costs as net price and VAT (instead of gross) in shopping cart and invoice
        $I->updateConfigInDatabase('blShowVATForDelivery', 'true', 'bool');
        // Display VAT contained in Payment Method Charges in Shopping Cart and Invoice
        $I->updateConfigInDatabase('blShowVATForPayCharge', 'true', 'bool');
        // Display VAT contained in Gift Wrappings and Greeting Cards in Shopping Cart and Invoice
        $I->updateConfigInDatabase('blShowVATForWrapping', 'true', 'bool');

        $basket->addProductToBasket("1000", 3);

        $userData = $this->getExistingUserData();
        $homePage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $basketPage = $homePage->openMiniBasket()->openBasketDisplay();
        /** @var OrderCheckout $orderCheckoutPage */
        $orderCheckoutPage = $basketPage->goToNextStep()->goToNextStep()->goToNextStep();

        $priceInformation = array(
            'net' => '142,86 €',
            'vat' => '7,14 €',
            'percentVat' => '5',
            'gross' => '150,00 €',
            'shipping' => '0,00 €',
            'payment_method' => '7,14 €',
            'percent_surcharge_tax' => '5',
            'payment_method_tax' => '0,36 €',
            'total' => '157,50 €',
        );

        $orderCheckoutPage->seeTotalNet($priceInformation['net']);
        $orderCheckoutPage->seeTotalVat($priceInformation['vat'], $priceInformation['percentVat']);
        $orderCheckoutPage->seeTotalGross($priceInformation['gross']);
        $orderCheckoutPage->seeShippingNet($priceInformation['shipping']);
        $orderCheckoutPage->seePaymentMethodNet($priceInformation['payment_method']);
        $orderCheckoutPage->seePaymentMethodVat(
            $priceInformation['payment_method_tax'],
            $priceInformation['percent_surcharge_tax']
        );
        $orderCheckoutPage->seeGrandTotal($priceInformation['total']);
    }

    public function checkPaymentStep(AcceptanceTester $I): void
    {
        $basket = new Basket($I);
        $I->wantToTest('the payment step');

        $homePage = $I->openShop();

        $basket->addProductToBasket("1001", 1);
        $basket->addProductToBasket("1002-2", 1);

        $userData = $this->getExistingUserData();
        $homePage->loginUser($userData['userLoginName'], $userData['userPassword']);
        $basketPage = $homePage->openMiniBasket()->openBasketDisplay();

        $basketPage->addCouponToBasket("123123");
        $paymentMethodPage = $basketPage->goToNextStep()->goToNextStep();
        $I->see(Translator::translate('PAYMENT_METHOD'));

        /** @var OrderCheckout $orderCheckoutPage */
        $orderCheckoutPage = $paymentMethodPage->goToNextStep();

        $orderCheckoutPage->validatePaymentMethod('COD (Cash on delivery)');

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

        $orderCheckoutPage->validateOrderItems([$basketItem1, $basketItem2]);

        $orderCheckoutPage->seeShippingGross('0,00 €');
        $newShippingMethod = 'Alternative';
        $paymentPage = $orderCheckoutPage->editShippingMethod();
        $paymentPage->selectShipping($newShippingMethod);
        $orderCheckoutPage = $paymentPage->goToNextStep();
        $orderCheckoutPage->validateShippingMethod($newShippingMethod);
        $orderCheckoutPage->seeShippingGross('0,00 €');

        $I->waitForElement('//div[contains(text(),"Surcharge Payment method")]/span');
        $orderCheckoutPage->seePaymentSurchargePrice('7,50 €');
        $paymentPage = $orderCheckoutPage->editPaymentMethod();
        $paymentPage->selectPayment('oxidpayadvance');
        $paymentPage->goToNextStep();
        $orderCheckoutPage->validatePaymentMethod('Cash in advance');
        $orderCheckoutPage->dontSeePaymentSurchargePrice();

        $orderCheckoutPage->submitOrderSuccessfully();
    }

    public function checkOrderToOtherCountries(AcceptanceTester $I): void
    {
        $I->wantToTest('ordering to another country');

        $homePage = $I->openShop();
        $userData = $this->getExistingUserData();
        $homePage->loginUser($userData['userLoginName'], $userData['userPassword']);

        $basket = new Basket($I);
        $basket->addProductToBasket("1000", 3);

        $paymentCheckout = $homePage->openMiniBasket()->openCheckout();
        $paymentCheckout->selectShippingIsAvailable();

        $userShippingAddress = [
            'userSalutation' => 'Mrs',
            'userFirstName' => 'Some first name',
            'userLastName' => 'Some last name',
            'companyName' => 'Some company',
            'street' => 'Some street',
            'streetNr' => '1-1',
            'ZIP' => '1234',
            'city' => 'Some city',
            'fonNr' => '111-111-1',
            'faxNr' => '111-111-111-1',
            'countryId' => 'United States',
            'stateId' => 'CO',
        ];

        $userCheckout = $paymentCheckout->goToPreviousStep();
        $userCheckout->openUserBillingAddressForm();
        $userCheckout->enterAddressData($userShippingAddress);

        $paymentCheckout = $userCheckout->goToNextStep();
        $paymentCheckout->selectShippingIsNotAvailable();

        $checkoutPage = $paymentCheckout->goToNextStep();

        $checkoutPage->validateShippingMethod('');
        $checkoutPage->validatePaymentMethod('Empty');
        $checkoutPage->submitOrderSuccessfully();
    }

    public function checkFrontendPerfOptionsAlsoBought(AcceptanceTester $I): void
    {
        $basket = new Basket($I);
        $I->wantToTest('the frontend performance option enabling/disabling "also bought"');

        $homePage = $I->openShop();

        $basket->addProductToBasket("1000", 1);
        $basket->addProductToBasket("1001", 1);

        $userData = $this->getExistingUserData();
        $homePage->loginUser($userData['userLoginName'], $userData['userPassword']);

        $basket->openMiniBasket();
        $thankYouPage = $basket->openCheckout()->goToNextStep()->submitOrderSuccessfully();
        $thankYouPage->backToShop();

        $searchList = $homePage->searchFor('1000');
        $productPage = $searchList->openProduct();
        $productPage->openAlsoBoughtProduct();
        $productPage->seeProductTitle('Test product 1 [EN]');

        $basket->addProductToBasket("1000", 1);
        $basket->openMiniBasket();
        $thankYouPage = $basket->openCheckout()->goToNextStep()->submitOrderSuccessfully();
        $thankYouPage->openAlsoBoughtProduct();
        $productPage->seeProductTitle('Test product 1 [EN]');

        // Performance options turn "who bought also bought" off
        $I->updateConfigInDatabase('bl_perfLoadCustomerWhoBoughtThis', false);

        $searchList = $homePage->searchFor('1000');
        $productPage = $searchList->openProduct();
        $productPage->dontSeeAlsoBought();

        $basket->addProductToBasket("1000", 1);
        $basket->openMiniBasket();
        $thankYouPage = $basket->openCheckout()->goToNextStep()->submitOrderSuccessfully();
        $thankYouPage->dontSeeAlsoBought();
    }

    public function checkMyAccountOrderHistory(AcceptanceTester $I)
    {
        $I->wantToTest('my accounts order history');

        $homePage = $I->openShop();
        $userData = $this->getExistingUserData();
        $homePage->loginUser($userData['userLoginName'], $userData['userPassword']);

        $basket = new Basket($I);
        $basket->addProductToBasket("1001", 2);
        $basket->openMiniBasket();
        $thankYouPage = $basket->openCheckout()->goToNextStep()->submitOrderSuccessfully();
        $orderHistory = $thankYouPage->goToOrderHistory();
        $orderHistory->seePageHeader();

        // check a different way to get to the orderHistory
        $user_account = new UserAccount($I);
        $orderHistory = $user_account->openAccountPage()->openOrderHistory();

        $orderHistory->seePageHeader();

        $orderInformation = [
            'orderNumber' => '1',
            'status' => Translator::translate('SHIPPED'),
            'name' => 'UserNamešÄßüл UserSurnamešÄßüл',
            'itemNumber' => '1',
            'amount' => '1',
            'product' => 'Test product 1 [EN] šÄßüл'
            ];

        $orderHistory->seeOrder($orderInformation);
        $productDetailsPage = $orderHistory->openProduct($orderInformation);
        $productDetailsPage->seeProductTitle('Test product 1 [EN] šÄßüл');
    }


    public function checkOrderStepChangedAddress(AcceptanceTester $I): void
    {
        $basket = new Basket($I);
        $I->wantToTest('whether changing the shipping/billing address in the payment/shipping step ' .
            'works and is displayed on the final checkout page');

        $homePage = $I->openShop();

        $basket->addProductToBasket("1001", 1);

        $userData = $this->getExistingUserData();
        $homePage->loginUser($userData['userLoginName'], $userData['userPassword']);

        /** @var OrderCheckout $orderCheckout */
        $orderCheckout = $basket->openMiniBasket()->openCheckout()->goToNextStep();

        $userData = [
            'shippingFirstName' => 'UserNamešÄßüл',
            'shippingLastName' => 'UserSurnamešÄßüл',
            'shippingCompany' => 'UserCompany šÄßüл',
            'shippingAdditionalInfo' => 'User additional info šÄßüл',
            'shippingStreet' => 'Musterstr.šÄßüл',
            'shippingStreetNr' => '1',
            'shippingZip' => '79098',
            'shippingCity' => 'Musterstadt šÄßüл',
            'shippingPhone' => '0800 111111',
            'shippingFax' => '0800 111112',
            'shippingCellPhone' => '0800 111114',
            'shippingPersonalPhone' => '0800 11111',
            'shippingCountry' => 'Germany',
            'shippingEmail' => 'example_test@oxid-esales.dev',
            'shippingTitle' => 'Mr'
        ];

        foreach ($userData as $addressPart) {
            $I->see($addressPart, $orderCheckout->deliveryAddress);
        }

        $userCheckout = $orderCheckout->editUserAddress();
        $userCheckout->openShippingAddressForm();

        $addressData = [
            'userSalutation' => 'Mrs',
            'userFirstName' => 'John',
            'userLastName' => 'Doe',
            'companyName' => 'XYZ Corp.',
            'street' => 'Main St',
            'streetNr' => '10',
            'ZIP' => '90210',
            'city' => 'Los Angeles',
            'additionalInfo' => 'Floor 5, Office 2',
            'fonNr' => '123-456-7890',
            'faxNr' => '098-765-4321',
            'countryId' => 'Austria',
        ];
        $userCheckout->enterShippingAddressData($addressData);

        $paymentCheckout = $userCheckout->goToNextStep();
        $paymentCheckout->selectShippingIsNotAvailable();

        $orderCheckout = $paymentCheckout->goToNextStep();
        $userCheckout = $orderCheckout->editUserAddress();

        $country = 'Germany';
        $userCheckout->selectCountry($country);

        $paymentCheckout = $userCheckout->goToNextStep();
        $paymentCheckout->selectShippingIsAvailable();
        $orderCheckout = $paymentCheckout->goToNextStep();
        $orderCheckout->submitOrderSuccessfully();
    }

    private function getExistingUserData(): array
    {
        return Fixtures::get('existingUser');
    }

    private function prepareTestDataForBundledProduct(AcceptanceTester $I, string $productId, string $bundleId): void
    {
        $I->updateInDatabase('oxarticles', ['OXBUNDLEID' => $bundleId], ['OXID' => $productId]);
    }

    private function removeBundleFromProduct(AcceptanceTester $I, string $productId): void
    {
        $I->updateInDatabase('oxarticles', ['OXBUNDLEID' => ''], ['OXID' => $productId]);
    }

    private function getUserFormData(): array
    {
        return [
            'userBirthDateDayField' => '31',
            'userBirthDateMonthField' => '12',
            'userBirthDateYearField' => '2000',
            'userUstIDField' => '',
            'userMobFonField' => '',
            'userPrivateFonField' => '',
        ];
    }

    private function getUserAddressFormData(): array
    {
        return [
            'userSalutation' => 'Mrs',
            'userFirstName' => 'some-name',
            'userLastName' => 'some-last-name',
            'street' => 'some-street',
            'streetNr' => '1',
            'ZIP' => 'zip-1234',
            'city' => 'some-city',
            'countryId' => 'Germany',
            'companyName' => '',
            'additionalInfo' => '',
            'fonNr' => '',
            'faxNr' => '',
        ];
    }
}
