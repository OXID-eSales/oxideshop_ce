<?php

use Step\Acceptance\ProductNavigation;
use Step\Acceptance\Start;

class GiftRegistryCest
{
    /**
     * @group myAccount
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function enabledGiftRegistry(AcceptanceTester $I, ProductNavigation $productNavigation)
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

        $detailsPage->openAccountMenu()->checkGiftRegistryItemCount(0)->closeAccountMenu();
        $detailsPage = $detailsPage->addToGiftRegistryList();
        $detailsPage->openAccountMenu()->checkGiftRegistryItemCount(1)->closeAccountMenu();

        $userAccountPage = $detailsPage->openAccountPage();
        $I->see($I->translate('MY_GIFT_REGISTRY'), $userAccountPage::$dashboardGiftRegistryPanelHeader);
        $I->see($I->translate('PRODUCT').' 1', $userAccountPage::$dashboardGiftRegistryPanelContent);

        $userLoginPage = $userAccountPage->logoutUser();
        $userAccountPage = $userLoginPage->login($userData['userLoginName'], $userData['userPassword']);
        $I->see($I->translate('MY_GIFT_REGISTRY'), $userAccountPage::$dashboardGiftRegistryPanelHeader);
        $I->see($I->translate('PRODUCT').' 1', $userAccountPage::$dashboardGiftRegistryPanelContent);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $detailsPage->removeFromGiftRegistryList();
        $detailsPage->openAccountMenu()->checkGiftRegistryItemCount(0)->closeAccountMenu();
    }

    /**
     * @group myAccount
     *
     * @param Start             $I
     * @param ProductNavigation $productNavigation
     */
    public function userGiftRegistry(Start $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('user gift registry functionality');

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

        //add to gift registry
        $detailsPage = $detailsPage->addToGiftRegistryList();
        $detailsPage->openAccountMenu()->checkGiftRegistryItemCount(1)->closeAccountMenu();

        $giftRegistryPage = $detailsPage->openAccountPage()->openGiftRegistryPage();

        //assert product
        $giftRegistryPage->seeProductData($productData);

        //open product details page
        $detailsPage = $giftRegistryPage->openProductDetailsPage(1);
        $I->see($productData['title'], $detailsPage::$productTitle);

        $giftRegistryPage = $detailsPage->openUserGiftRegistryPage()
            ->addProductToBasket(1, 2);
        $I->see(2, $giftRegistryPage::$miniBasketMenuElement);

        $giftRegistryPage->removeFromGiftRegistry(1);
        $I->see($I->translate('GIFT_REGISTRY_EMPTY'));

        $I->deleteFromDatabase('oxuserbaskets', ['oxuserid' => 'testuser']);
        $I->clearShopCache();
    }

    /**
     * @group myAccount
     *
     * @param Start             $I
     * @param ProductNavigation $productNavigation
     */
    public function makingPublicUserGiftRegistry(Start $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('user gift registry functionality setting it as searchable and public');

        //set country, username, password for default user
        $I->updateInDatabase(
            'oxuser',
            [
                "oxcountryid" => 'a7c40f631fc920687.20179984',
                "oxusername" => 'admin@myoxideshop.com',
                "oxpassword" => '6cb4a34e1b66d3445108cd91b67f98b9',
                "oxpasssalt" => '6631386565336161636139613634663766383538633566623662613036636539',
            ],
            ["OXUSERNAME" => 'admin']
        );

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

        //add to gift registry and open the page of it
        $giftRegistryPage = $detailsPage->addToGiftRegistryList()->openUserGiftRegistryPage();

        //making gift registry searchable
        $giftRegistryPage = $giftRegistryPage->makeListSearchable()
            ->logoutUser()
            ->loginUser('admin@myoxideshop.com', 'admin0303')
            ->searchForGiftRegistry($userData['userLoginName']);
        $I->see($I->translate('GIFT_REGISTRY_SEARCH_RESULTS'));
        $I->see($I->translate('GIFT_REGISTRY_OF') .' '. $userData['userName'] .' '. $userData['userLastName']);
        $giftRegListPage = $giftRegistryPage->openFoundGiftRegistryList();
        $title = $I->translate('GIFT_REGISTRY_OF') .' '. $userData['userName'] .' '. $userData['userLastName'];
        $I->see($title, $giftRegListPage::$headerTitle);
        $I->see(sprintf($I->translate('WISHLIST_PRODUCTS'), $userData['userName'] .' '. $userData['userLastName']));
        $giftRegListPage->seeProductData($productData, 1);

        $giftRegistryPage = $giftRegListPage->openUserGiftRegistryPage()
            ->logoutUser()
            ->loginUser($userData['userLoginName'], $userData['userPassword']);

        //making gift registry not searchable
        $I->see($I->translate('MESSAGE_MAKE_GIFT_REGISTRY_PUBLISH'));
        $giftRegistryPage = $giftRegistryPage->makeListNotSearchable()
            ->logoutUser()
            ->loginUser('admin@myoxideshop.com', 'admin0303')
            ->searchForGiftRegistry($userData['userLoginName']);
        $I->see($I->translate('MESSAGE_SORRY_NO_GIFT_REGISTRY'));

        $giftRegistryPage = $giftRegistryPage->logoutUser()
            ->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->sendGiftRegistryEmail(
                'example@oxid-esales.dev',
                'recipient',
                'Hi, I created a Gift Registry at OXID.'
            );
        $I->see(sprintf($I->translate('GIFT_REGISTRY_SENT_SUCCESSFULLY'), 'example@oxid-esales.dev'));

        $giftRegistryPage->removeFromGiftRegistry(1);
        $I->see($I->translate('GIFT_REGISTRY_EMPTY'));
    }

    /**
     * @group myAccount
     *
     * @param Start             $I
     * @param ProductNavigation $productNavigation
     */
    public function disabledUserGiftRegistry(Start $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('disabled user gift registry via performance options');

        //(Use gift registry) is disabled
        $I->updateInDatabase('oxconfig', ["OXVARVALUE" => ''], ["OXVARNAME" => 'bl_showWishlist']);

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $userData = $this->getExistingUserData();

        $I->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $I->dontSeeElement($detailsPage::$addToGiftRegistryLink);
        $detailsPage->openAccountMenu();
        $I->dontSee($I->translate('MY_GIFT_REGISTRY'));
        $detailsPage->closeAccountMenu();

        $accountPage = $detailsPage->openAccountPage();
        $I->dontSee($I->translate('MY_GIFT_REGISTRY'), $accountPage::$giftRegistryLink);

        //(Use gift registry) is enabled
        $I->cleanUp();

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
