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
        $basket->addProductToBasket($basketItem2['id'], 1, 'basket')
            ->seeMiniBasketContains([$basketItem1, $basketItem2], '200,00 €', 3);


    }

}
