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
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 1,
            'price' => '50,00 €'
        ];

        $basket->addProductToBasket($basketItem1['id'], 1);
        $homePage = $homePage->seeMiniBasketContains([$basketItem1], '50,00 €', 1);

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
    public function buyOutOfStockNotBuyableProductDuringOrder(AcceptanceTester $I)
    {
        $basket = new Basket($I);
        $I->wantToTest('if no fatal errors or exceptions are thrown, but an error message is shown, if the same 
        product was sold out by other user during the checkout');

        $userData = $this->getExistingUserData();

        $homePage = $I->openShop()->loginUser($userData['userLoginName'], $userData['userPassword']);

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
            'id' => 1000,
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
            'id' => 1000,
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
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 1,
            'totalPrice' => '50,00 €'
        ];

        $bundledProductData = [
            'id' => 1001,
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
