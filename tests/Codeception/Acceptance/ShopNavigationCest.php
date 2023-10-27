<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Step\Basket;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

final class ShopNavigationCest
{
    public function checkBadgeCountWhenProductIsAddedToCart(AcceptanceTester $I): void
    {
        $I->wantToTest('that the badge next to the cart icon shows the number of items in the cart');

        $homePage = $I->openShop();
        $userData = $this->getExistingUserData();
        $homePage->loginUser($userData['userLoginName'], $userData['userPassword']);

        $basket = new Basket($I);
        $basket->dontSeeItemCountBadge();
        $basket->addProductToBasket('1001', 1);
        $basket->seeItemCountBadge('1');
        $basket->addProductToBasket('1001', 3);
        $basket->seeItemCountBadge('4');
        $basket->addProductToBasket('1000', 7);
        $basket->seeItemCountBadge('11');

        $basket->openMiniBasket()->openCheckout()->goToNextStep()->submitOrder();
        $basket->dontSeeItemCountBadge();
    }

    private function getExistingUserData(): array
    {
        return Fixtures::get('existingUser');
    }
}
