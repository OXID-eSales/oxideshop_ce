<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('productLabel')]
final class ProductLabelCest
{
    public function addingProductLabel(AcceptanceTester $I): void
    {
        $I->wantToTest('product customization (product labels AKA `persparam`) functionality');
        $label1 = uniqid('product-label', true);
        $label2 = uniqid('product-label', true);

        $I->amGoingTo('enable products labels for 2 products via admin');
        $productList = $I
            ->loginAdmin()
            ->openProducts();
        $productList
            ->find($productList->searchNumberInput, '1000')
            ->openExtendedTab()
            ->enableProductCustomization();
        $productList
            ->find($productList->searchNumberInput, '1001')
            ->openExtendedTab()
            ->enableProductCustomization();

        $I->amGoingTo('add these 2 and any other product to the cart');
        $I
            ->openShop()
            ->switchLanguage('English');
        $shop = $I->loginShopWithExistingUser();

        $I->amGoingTo('add label for product 1 on its details page');
        $shop
            ->searchFor('1000')
            ->openFirstProductInSearchResults()
            ->addProductLabel($label1)
            ->addProductToBasket();

        $I->amGoingTo('check the input for product 2 is there but add label later');
        $shop
            ->searchFor('1001')
            ->openFirstProductInSearchResults()
            ->seeProductLabelInput()
            ->addProductToBasket();

        $I->amGoingTo('check there is no input for product 3');
        $shop
            ->searchFor('1002')
            ->openFirstProductInSearchResults()
            ->dontSeeProductLabelInput()
            ->selectVariant(1, 'var1 [EN] šÄßüл')
            ->dontSeeProductLabelInput()
            ->addProductToBasket();

        $I->amGoingTo('check the inputs and add label for product 2 on the shopping cart page');
        $basket = $shop->openBasket();
        $basket->seeProductLabel($label1, 1);
        $basket->seeProductLabel('', 2);
        $basket->addProductLabel($label2, 2);
        $basket->seeProductLabel($label2, 2);
        $basket->dontSeeProductLabelInput(3);

        $I->amGoingTo('click-through the checkout steps');
        $orderCheckout = $basket
            ->goToNextStep()
            ->goToNextStep()
            ->goToNextStep();

        $I->amGoingTo('check the labels on the order checkout page and submit the order');
        $orderCheckout
            ->seeOrderItemLabel($label1, 1)
            ->seeOrderItemLabel($label2, 2)
            ->dontSeeOrderItemHasLabel(3)
            ->submitOrder();

        $I->amGoingTo('make sure that labels are visible in the user order history');
        $shop
            ->openUserAccountPage()
            ->openOrderHistory()
            ->seeOrderItemsLabel($label1, 1, 1)
            ->seeOrderItemsLabel($label2, 1, 2)
            ->dontSeeOrderItemHasLabel(1, 3);

        $I->amGoingTo('make sure that labels are visible in the admin order history');

        $I->amGoingTo('check labels on order overview tab');
        $orderOverviewPage = $I
            ->loginAdmin()
            ->openOrders()
            ->findByOrderNumber('1')
            ->seeOrderProductLabel($label1, 1)
            ->seeOrderProductLabel($label2, 2)
            ->dontSeeOrderProductHasLabel(3);

        $I->amGoingTo('check labels on order products tab');
        $orderOverviewPage
            ->openProductsTab()
            ->seeOrderProductLabel($label1, 1)
            ->seeOrderProductLabel($label2, 2)
            ->dontSeeOrderProductHasLabel(3);
    }
}
