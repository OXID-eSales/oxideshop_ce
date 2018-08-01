<?php

use Step\Acceptance\Basket;

class CheckoutProcessCest
{
    /**
     * @group basketfrontend
     *
     * @param AcceptanceTester $I
     */
    public function basketFlyout(AcceptanceTester $I, Basket $basket)
    {
        $I->wantToTest('basket flyout');

        $basketItem1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 1,
            'price' => '50,00 €'
        ];

        //add Product to basket
        /** @var \Page\Basket $basketPage */
        $basket->addProductToBasket($basketItem1['id'], 1, 'basket')
            ->seeMiniBasketContains([$basketItem1], '50,00 €', 1);

        $basketItem1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 2,
            'price' => '100,00 €'
        ];

        $basketItem2 = [
            'id' => 1001,
            'title' => 'Test product 1 [EN] šÄßüл',
            'amount' => 1,
            'price' => '100,00 €'
        ];

        //add Product to basket
        /** @var \Page\Basket $basketPage */
        $basket->addProductToBasket($basketItem1['id'], 1, 'basket');
        $basketPage = $basket->addProductToBasket($basketItem2['id'], 1, 'basket')
            ->seeMiniBasketContains([$basketItem1, $basketItem2], '200,00 €', 3);
        $userCheckoutPage = $basketPage->openCheckoutForNotLoggedInUser();

        $userData = $this->getExistingUserData();

        $homePage = $userCheckoutPage->openHomePage();
        $homePage->loginUser($userData['userLoginName'], $userData['userPassword']);

        $homePage->openMiniBasket()->openCheckoutForLoggedInUser();
    }
    /**
     * @group basketfrontend
     *
     * @param AcceptanceTester $I
     */
    public function outOfStockNotBuyableProductDuringOrder(AcceptanceTester $I, Basket $basket)
    {
        $I->wantToTest('if no fatal errors or exceptions are thrown, but an error message is shown, if the same 
        product was sold out by other user during the checkout');

        $userData = $this->getExistingUserData();

        $I->openShop()->loginUser($userData['userLoginName'], $userData['userPassword']);

        $basketItem1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 5,
            'totalPrice' => '250,00 €'
        ];

        $basketItem2 = [
            'id' => 1001,
            'title' => 'Test product 1 [EN] šÄßüл',
            'amount' => 1,
            'totalPrice' => '100,00 €'
        ];

        //add Product to basket
        /** @var \Page\Basket $basketPage */
        $basket->addProductToBasket($basketItem1['id'], 5, 'basket');
        $basketPage = $basket->addProductToBasket($basketItem2['id'], 1, 'basket')
            ->seeBasketContains([$basketItem1, $basketItem2], '350,00 €');

        // making product out of stock now
        $I->updateInDatabase('oxarticles', ["oxstock" => '3', "oxstockflag" => '3'], ["oxid" => '1000']);

        $basketPage->updateProductAmount(7);

        $I->see($I->translate("ERROR_MESSAGE_OUTOFSTOCK_OUTOFSTOCK"));

        $basketItem1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 3,
            'totalPrice' => '150,00 €'
        ];
        $paymentPage = $basketPage->seeBasketContains([$basketItem1, $basketItem2], '250,00 €')
            ->goToNextStep()
            ->goToNextStep();

        //in second step, product availability is not checked.
        $I->see($I->translate("SELECT_SHIPPING_METHOD"));

        $orderPage = $paymentPage->selectPayment('oxidcashondel')
            ->goToNextStep();

        // someone bought some more items while client filled steps
        $I->updateInDatabase('oxarticles', ["oxstock" => '1', "oxstockflag" => '3'], ["oxid" => '1000']);

        $orderPage->clickOnSubmitOrder();

        //in second step, product availability is not checked.
        $I->see($I->translate("ERROR_MESSAGE_OUTOFSTOCK_OUTOFSTOCK"));

        // someone bought all items while client filled steps
        $I->updateInDatabase('oxarticles', ["oxstock" => '0', "oxstockflag" => '3'], ["oxid" => '1000']);

        $orderPage->clickOnSubmitOrder();

        //in second step, product availability is not checked.
        $I->see($I->translate("ERROR_MESSAGE_ARTICLE_ARTICLE_NOT_BUYABLE"));

        $orderPage->clickOnSubmitOrder();

        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('ORDER_COMPLETED');
        $I->see($breadCrumb);
    }

    private function getExistingUserData()
    {
        $userLoginData = [
            "userLoginName" => "example_test@oxid-esales.dev",
            "userPassword" => "useruser",
            "userName" => "UserNamešÄßüл",
            "userLastName" => "UserSurnamešÄßüл",
        ];
        return $userLoginData;
    }
}
