<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Account\UserAccount;
use OxidEsales\Codeception\Step\ProductNavigation;
use OxidEsales\Codeception\Step\Start;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

final class GiftRegistryCest
{
    /**
     * @group myAccount
     * @group giftRegistry
     */
    public function addProductToUserGiftRegistry(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('if product gift registry functionality is enabled');

        //(Use gift registry) is enabled again
        $I->updateConfigInDatabase('bl_showWishlist', true);

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];
        $userData = $this->getExistingUserData();

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $detailsPage = $detailsPage->loginUser($userData['userLoginName'], $userData['userPassword']);

        $detailsPage = $detailsPage->openAccountMenu()
            ->checkGiftRegistryItemCount(0)
            ->closeAccountMenu();

        $detailsPage = $detailsPage->addProductToGiftRegistryList()
            ->openAccountMenu()
            ->checkGiftRegistryItemCount(1)
            ->closeAccountMenu();

        $userAccountPage = $detailsPage->openAccountPage()->seeItemNumberOnGiftRegistryPanel('1');

        /** @var UserAccount $userAccountPage */
        $userAccountPage = $userAccountPage->logoutUserInAccountPage()
            ->login($userData['userLoginName'], $userData['userPassword'])
            ->seeItemNumberOnGiftRegistryPanel('1');

        $giftRegistryPage = $userAccountPage->openGiftRegistryPage()
            ->seeProductData($productData);

        //open product details page
        $detailsPage = $giftRegistryPage->openProductDetailsPage(1);
        $I->see($productData['title'], $detailsPage->productTitle);

        $giftRegistryPage = $detailsPage->openUserGiftRegistryPage()
            ->addProductToBasket(1, 2);
        $I->see('2', $giftRegistryPage->miniBasketMenuElement);

        $giftRegistryPage->removeFromGiftRegistry(1);
        $I->see(Translator::translate('GIFT_REGISTRY_EMPTY'));
        $giftRegistryPage->openAccountMenu()
            ->checkGiftRegistryItemCount(0)
            ->closeAccountMenu();

        $I->deleteFromDatabase('oxuserbaskets', ['oxuserid' => 'testuser']);
        $I->clearShopCache();
    }

    /**
     * @group myAccount
     * @group giftRegistry
     */
    public function makeUserGiftRegistryPublic(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('user gift registry functionality setting it as searchable and public');

        //(Use gift registry) is enabled again
        $I->updateConfigInDatabase('bl_showWishlist', true);

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];
        $userData = $this->getExistingUserData();
        $adminUserData = $this->getAdminUserData();

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        //add to gift registry and open the page of it
        $giftRegistryPage = $detailsPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->addProductToGiftRegistryList()
            ->openUserGiftRegistryPage();

        //making gift registry searchable and logout
        $giftRegistryPage = $giftRegistryPage->makeListSearchable()
            ->logoutUser();
        //login with different user and search for this list
        $giftRegistryPage = $giftRegistryPage->loginUser($adminUserData['userLoginName'], $adminUserData['userPassword'])
            ->searchForGiftRegistry($userData['userLoginName']);
        $I->see(Translator::translate('GIFT_REGISTRY_SEARCH_RESULTS'));
        $I->see(Translator::translate('GIFT_REGISTRY_OF') . ' ' . $userData['userName'] . ' ' . $userData['userLastName']);
        $giftRegListPage = $giftRegistryPage->openFoundGiftRegistryList();
        $title = Translator::translate('GIFT_REGISTRY_OF') . ' ' . $userData['userName'] . ' ' . $userData['userLastName'];
        $I->see($title, $giftRegListPage->headerTitle);
        $I->see(sprintf(Translator::translate('WISHLIST_PRODUCTS'), $userData['userName'] . ' ' . $userData['userLastName']));
        $giftRegListPage->seeProductData($productData, 1);

        //making gift registry not searchable
        $giftRegistryPage = $giftRegListPage->openUserGiftRegistryPage()
            ->logoutUser()
            ->loginUser($userData['userLoginName'], $userData['userPassword']);
        $I->see(Translator::translate('MESSAGE_MAKE_GIFT_REGISTRY_PUBLISH'));
        $giftRegistryPage = $giftRegistryPage->makeListNotSearchable();

        $giftRegistryPage = $giftRegistryPage->logoutUser()
            ->loginUser($adminUserData['userLoginName'], $adminUserData['userPassword'])
            ->searchForGiftRegistry($userData['userLoginName']);
        $I->see(Translator::translate('MESSAGE_SORRY_NO_GIFT_REGISTRY'));

        //send notification about gift registry
        $giftRegistryPage = $giftRegistryPage->logoutUser()
            ->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->sendGiftRegistryEmail(
                'example@oxid-esales.dev',
                'recipient',
                'Hi, I created a Gift Registry at OXID.'
            );
        $I->see(sprintf(Translator::translate('GIFT_REGISTRY_SENT_SUCCESSFULLY'), 'example@oxid-esales.dev'));

        $giftRegistryPage->removeFromGiftRegistry(1);
        $I->see(Translator::translate('GIFT_REGISTRY_EMPTY'));
    }

    /**
     * @group myAccount
     * @group giftRegistry
     */
    public function disableUserGiftRegistry(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $start = new Start($I);
        $I->wantToTest('disabled user gift registry via performance options');

        //(Use gift registry) is disabled
        $I->updateConfigInDatabase('bl_showWishlist', false, "bool");

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $userData = $this->getExistingUserData();

        $start->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $I->dontSeeElement($detailsPage->addToGiftRegistryLink);
        $detailsPage->openAccountMenu();
        $I->dontSee(Translator::translate('MY_GIFT_REGISTRY'));
        $detailsPage->closeAccountMenu();

        $accountPage = $detailsPage->openAccountPage();
        $accountPage->dontSeeGiftRegistryLink();

        //(Use gift registry) is enabled again
        $I->updateConfigInDatabase('bl_showWishlist', true, "bool");
    }

    private function getExistingUserData()
    {
        return Fixtures::get('existingUser');
    }

    private function getAdminUserData()
    {
        return Fixtures::get('adminUser');
    }
}
