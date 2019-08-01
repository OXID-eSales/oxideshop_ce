<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Step\ProductNavigation;
use OxidEsales\Codeception\Module\Translation\Translator;

class ListmaniaCest
{
    /**
     * @group myAccount
     * @group listmania
     *
     * @param AcceptanceTester $I
     */
    public function createNewListmania(AcceptanceTester $I)
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('creation of the listmania');

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

        $productListmaniaPage = $detailsPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->addToListmania();
        $I->see(Translator::translate('NO_LISTMANIA_LIST'));
        $userListmaniaPage = $productListmaniaPage->createNewList();
        $I->see(Translator::translate('NO_LISTMANIA_LIST_FOUND'));
        $userListmaniaPage = $userListmaniaPage->createNewList('recomm title1', 'recomm author1', 'recom introduction1');
        $I->see(Translator::translate('LISTMANIA_LIST_SAVED'));
        $userListmaniaPage = $userListmaniaPage->openListmaniaPage()
            ->seeListData('recomm title1', 'recomm author1', 'recom introduction1')
            ->openListByTitle('recomm title1');

        $userAccountPage =  $userListmaniaPage->openAccountPage();
        $I->see(Translator::translate('MY_LISTMANIA'), $userAccountPage->dashboardListmaniaPanelHeader);
        $I->see(Translator::translate('LISTS').' 1', $userAccountPage->dashboardListmaniaPanelContent);
    }

    public function _failed(AcceptanceTester $I)
    {
        $this->clearListmaniaData($I);
        $I->clearShopCache();
    }

    public function _after(AcceptanceTester $I)
    {
        $this->clearListmaniaData($I);
        $I->clearShopCache();
    }

    private function getExistingUserData()
    {
        return \Codeception\Util\Fixtures::get('existingUser');
    }

    private function clearListmaniaData(AcceptanceTester $I)
    {
        $I->deleteFromDatabase('oxrecommlists', ['OXTITLE'=>'recomm title1']);
        $I->deleteFromDatabase('oxobject2list', ['OXLISTID !='=>'']);
    }
}
