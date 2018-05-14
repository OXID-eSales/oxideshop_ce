<?php

use Step\Acceptance\ProductNavigation;
use Step\Acceptance\Start;

class WishListCest
{
    /**
     * @group myAccount
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function enabledWishList(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('if product compare functionality is enabled');

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $userData = $this->getExistingUserData();

        $I->openShop()->loginUser($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $detailsPage->openAccountMenu()->checkWishListItemCount(0)->closeAccountMenu();
        $detailsPage = $detailsPage->addToWishList();
        $detailsPage->openAccountMenu()->checkWishListItemCount(1)->closeAccountMenu();

        $userAccountPage = $detailsPage->openAccountPage();
        $I->see($I->translate('MY_WISH_LIST'), $userAccountPage::$dashboardWishListPanelHeader);
        $I->see($I->translate('PRODUCT').' 1', $userAccountPage::$dashboardWishListPanelContent);

        $userLoginPage = $userAccountPage->logoutUser();
        $userAccountPage = $userLoginPage->login($userData['userLoginName'], $userData['userPassword']);
        $I->see($I->translate('MY_WISH_LIST'), $userAccountPage::$dashboardWishListPanelHeader);
        $I->see($I->translate('PRODUCT').' 1', $userAccountPage::$dashboardWishListPanelContent);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $detailsPage->removeFromWishList();
        $detailsPage->openAccountMenu()->checkWishListItemCount(0)->closeAccountMenu();
    }

    /**
     * @group myAccount
     *
     * @param Start $I
     */
    public function userWishList(Start $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('user wish list functionality');

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 €'
        ];

        $userData = $this->getExistingUserData();

        $startPage = $I->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $detailsPage = $detailsPage->addToWishList();
        $detailsPage->openAccountMenu()->checkWishListItemCount(1)->closeAccountMenu();
        $wishListPage = $detailsPage->openUserWishListPage();

        //assert product
        $wishListPage->seeProductData($productData);

        //open product details page
        $detailsPage = $wishListPage->openProductDetailsPage(1);
        $I->see($productData['title'], $detailsPage::$productTitle);

        $wishListPage = $detailsPage->openAccountPage()
            ->openWishListPage()
            ->addProductToBasket(1, 2);
        $I->see(2, $wishListPage::$miniBasketMenuElement);
        $wishListPage = $wishListPage->removeProductFromList(1);

        $I->see($I->translate('PAGE_TITLE_ACCOUNT_NOTICELIST'), $wishListPage::$headerTitle);
        $I->see($I->translate('WISH_LIST_EMPTY'));
    }

    /**
     * @group myAccount
     *
     * @param Start $I
     */
    public function userWishListAddingVariant(Start $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('user wish list functionality, if a variant of product was added');

        $productData = [
            'id' => 10014,
            'title' => '14 EN product šÄßüл',
            'desc' => '13 EN description šÄßüл',
            'price' => 'from 15,00 €'
        ];

        $userData = $this->getExistingUserData();

        $startPage = $I->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see('14 EN product šÄßüл');
        //add parent to wish list
        $detailsPage = $detailsPage->addToWishList();
        //add variant to wish list
        $detailsPage = $detailsPage->selectVariant(1, 'S')
            ->selectVariant(2, 'black')
            ->selectVariant(3, 'lether')
            ->addToWishList();
        $detailsPage->openAccountMenu()->checkWishListItemCount(2);
        $detailsPage->closeAccountMenu();
        $wishListPage = $detailsPage->openUserWishListPage();

        $wishListPage->seeProductData($productData);
        //assert product
        $productData = [
            'id' => '10014-1-1',
            'title' => '14 EN product šÄßüл S | black | lether',
            'desc' => '',
            'price' => '25,00 €'
        ];
        $wishListPage->seeProductData($productData, 2);
        $wishListPage = $wishListPage->removeProductFromList(2);
        $wishListPage = $wishListPage->removeProductFromList(1);

        $I->see($I->translate('PAGE_TITLE_ACCOUNT_NOTICELIST'), $wishListPage::$headerTitle);
        $I->see($I->translate('WISH_LIST_EMPTY'));
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
