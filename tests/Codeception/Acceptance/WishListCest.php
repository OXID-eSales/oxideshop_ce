<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Step\ProductNavigation;
use OxidEsales\Codeception\Step\Start;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[group('myAccount', 'wishList')]
final class WishListCest
{
    public function addProductToUserWishList(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('if product compare functionality is enabled');

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $userData = $this->getExistingUserData();

        $I->openShop()->loginUser($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $detailsPage->checkWishListItemCount(0)
            ->addToWishList()
            ->checkWishListItemCount(1);

        $userAccountPage = $detailsPage->openAccountPage();
        $I->see(Translator::translate('MY_WISH_LIST'));
        $I->see(Translator::translate('PRODUCT') . ' 1');

        $userAccountPage->logoutUserInAccountPage()->login($userData['userLoginName'], $userData['userPassword']);
        $I->see(Translator::translate('MY_WISH_LIST'));
        $I->see(Translator::translate('PRODUCT') . ' 1');

        $userAccountPage->openWishListPage()
            ->seeProductData($productData)
            ->openProductDetailsPage(1);
        $I->see($productData['title'], $detailsPage->productTitle);

        $wishListPage = $detailsPage->openUserWishListPage()
            ->addProductToBasket(1, 2);
        $I->see('2', $wishListPage->miniBasketMenuElement);
        $wishListPage = $wishListPage->removeProductFromList(1);

        $I->see(Translator::translate('PAGE_TITLE_ACCOUNT_NOTICELIST'), $wishListPage->headerTitle);
        $I->see(Translator::translate('WISH_LIST_EMPTY'));

        $wishListPage->checkWishListItemCount(0);
    }

    public function addVariantToUserWishList(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $start = new Start($I);
        $I->wantToTest('user wish list functionality, if a variant of product was added');

        $I->updateConfigInDatabase('blUseMultidimensionVariants', true, 'bool');

        $productData = [
            'id' => '10014',
            'title' => '14 EN product šÄßüл',
            'description' => '13 EN description šÄßüл',
            'price' => 'from 15,00 €'
        ];

        $userData = $this->getExistingUserData();

        $start->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see('14 EN product šÄßüл');
        //add parent to wish list
        $wishListPage = $detailsPage->addToWishList()
            ->selectVariant(1, 'S')
            ->selectVariant(2, 'black')
            ->selectVariant(3, 'lether')
            ->addToWishList()
            ->checkWishListItemCount(2)
            ->openUserWishListPage()
            ->seeProductData($productData);

        //assert variant
        $productData = [
            'id' => '10014-1-1',
            'title' => '14 EN product šÄßüл S | black | lether',
            'description' => '',
            'price' => '25,00 €'
        ];
        $wishListPage->seeProductData($productData, 2);

        $wishListPage->removeProductFromList(2)
            ->removeProductFromList(1);

        $I->see(Translator::translate('PAGE_TITLE_ACCOUNT_NOTICELIST'), $wishListPage->headerTitle);
        $I->see(Translator::translate('WISH_LIST_EMPTY'));
    }

    public function testWishlistInTheCartForALoggedInUser(AcceptanceTester $I): void
    {
        $I->wantToTest('if a logged-in user can move a product from the basket to the wishlist');

        $start = $I->loginShopWithExistingUser();
        $productNavigation = new ProductNavigation($I);
        $I->updateConfigInDatabase('bl_showWishlist', true);

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $productNavigation
            ->openProductDetailsPage($productData['id'])
            ->addProductToBasket()
            ->openMiniBasket()
            ->openBasketDisplay()
            ->seeAddToTheWishlistStar(1)
            ->addProductToTheWishList(1);

        $I->retrySee(Translator::translate('BASKET_EMPTY'));

        $start
            ->openUserWishListPage()
            ->seeProductData($productData);
    }

    public function testWishlistInTheCartForANonLoggedInUser(AcceptanceTester $I): void
    {
        $I->wantToTest('if a non-logged-in user redirected to the login page after click on the star');
        $I->updateConfigInDatabase('bl_showWishlist', true);

        $productData = [
            'id' => '1000',
        ];

        $productNavigation = new ProductNavigation($I);

        $productNavigation
            ->openProductDetailsPage($productData['id'])
            ->addProductToBasket()
            ->openMiniBasket()
            ->openBasketDisplay()
            ->seeAddToTheWishlistStar(1)
            ->addProductToTheWishList(1);

        $I->see(Translator::translate('LOGIN'));
    }

    private function getExistingUserData()
    {
        return Fixtures::get('existingUser');
    }
}
