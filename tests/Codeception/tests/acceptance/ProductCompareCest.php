<?php

use Step\Acceptance\ProductNavigation;
use Step\Acceptance\Start;
use Step\Acceptance\Compare\ProductCompareList;

class ProductCompareCest
{
    /**
     * @group myAccount
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function enabledProductCompare(AcceptanceTester $I, ProductNavigation $productNavigation)
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

        $detailsPage->openAccountMenu()->checkCompareListItemCount(0)->closeAccountMenu();
        $detailsPage = $detailsPage->addToCompareList();
        $detailsPage->openAccountMenu()->checkCompareListItemCount(1)->closeAccountMenu();

        $userAccountPage = $detailsPage->openAccountPage();
        $I->see($I->translate('MY_PRODUCT_COMPARISON'), $userAccountPage::$dashboardCompareListPanelHeader);
        $I->see($I->translate('PRODUCT').' 1', $userAccountPage::$dashboardCompareListPanelContent);

        $userLoginPage = $userAccountPage->logoutUser();
        $userAccountPage = $userLoginPage->login($userData['userLoginName'], $userData['userPassword']);
        $I->see($I->translate('MY_PRODUCT_COMPARISON'), $userAccountPage::$dashboardCompareListPanelHeader);
        $I->see($I->translate('PRODUCT').' 1', $userAccountPage::$dashboardCompareListPanelContent);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $detailsPage->removeFromCompareList();
        $detailsPage->openAccountMenu()->checkCompareListItemCount(0)->closeAccountMenu();
    }

    /**
     * @group myAccount
     *
     * @param Start              $I
     * @param ProductNavigation  $productNavigation
     * @param ProductCompareList $compareList
     */
    public function userCompareList(Start $I, ProductNavigation $productNavigation, ProductCompareList $compareList)
    {
        $I->wantToTest('user product compare list functionality');

        $productData1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $productData2 = [
            'id' => 1001,
            'title' => 'Test product 1 [EN] šÄßüл',
            'desc' => 'Test product 1 short desc [EN] šÄßüл',
            'price' => '100,00 € *'
        ];

        $productData3 = [
            'id' => 10014,
            'title' => '14 EN product šÄßüл',
            'desc' => '13 EN description šÄßüл',
            'price' => 'from 15,00 € *'
        ];

        $userData = $this->getExistingUserData();

        $I->loginOnStartPage($userData['userLoginName'], $userData['userPassword']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData1['id']);
        $I->see($productData1['title']);
        //add to compare list
        $compareList->addProductToCompareList($detailsPage, 1);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData2['id']);
        $I->see($productData2['title']);
        //add to compare list
        $compareList->addProductToCompareList($detailsPage, 2);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData3['id']);
        $I->see($productData3['title']);
        //add to compare list
        $detailsPage = $compareList->addProductToCompareList($detailsPage, 3);

        //open compare list page
        $comparePage = $detailsPage->openProductComparePage();

        $I->seeElement("#compareRight_1000");
        $I->seeElement("#compareRight_1001");
        $I->seeElement("#compareLeft_10014");
        $comparePage->seeProductData($productData1, 1);
        $comparePage->seeProductData($productData2, 2);
        $comparePage->seeProductData($productData3, 3);

        // TODO: add variant assertion
        //$this->assertEquals("selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $I->clearString($this->getText("//div[@id='compareSelections_2']//ul")));
        //$this->assertEquals("var1 [EN] šÄßüл var2 [EN] šÄßüл", $I->clearString($this->getText("//div[@id='compareVariantSelections_3']//ul")));

        //open product details page
        $detailsPage = $comparePage->openProductDetailsPage(1);
        $I->see($productData1['title'], $detailsPage::$productTitle);
        $comparePage = $detailsPage->openProductComparePage();
        $detailsPage = $comparePage->openProductDetailsPage(2);
        $I->see($productData2['title'], $detailsPage::$productTitle);
        $comparePage = $detailsPage->openProductComparePage();

        $comparePage->seeProductAttributeName('Test attribute 1 [EN] šÄßüл:',1);
        $comparePage->seeProductAttributeValue('attr value 1 [EN] šÄßüл', 1, 1);
        $comparePage->seeProductAttributeValue('attr value 11 [EN] šÄßüл', 1, 2);
        $comparePage->seeProductAttributeName('Test attribute 3 [EN] šÄßüл:',2);
        $comparePage->seeProductAttributeValue('attr value 3 [EN] šÄßüл', 2, 1);
        $comparePage->seeProductAttributeValue('attr value 3 [EN] šÄßüл', 2, 2);
        $comparePage->seeProductAttributeName('Test attribute 2 [EN] šÄßüл:',3);
        $comparePage->seeProductAttributeValue('attr value 2 [EN] šÄßüл', 3, 1);
        $comparePage->seeProductAttributeValue('attr value 12 [EN] šÄßüл', 3, 2);

        $comparePage->moveItemToRight($productData1['id']);
        $comparePage->seeProductData($productData1, 2);
        $comparePage->seeProductData($productData2, 1);

        $comparePage->moveItemToLeft($productData1['id']);
        $comparePage->seeProductData($productData1, 1);
        $comparePage->seeProductData($productData2, 2);

        $comparePage->removeProductFromList($productData1['id']);
        $comparePage->removeProductFromList($productData2['id']);
        //TODO: is not working with flow theme anymore
        //$I->see($I->translate('MESSAGE_SELECT_MORE_PRODUCTS'));
        $comparePage->removeProductFromList($productData3['id']);
        $I->see($I->translate('MESSAGE_SELECT_AT_LEAST_ONE_PRODUCT'));

    }

    /**
     * @group myAccount
     *
     * @param Start             $I
     * @param ProductNavigation $productNavigation
     */
    public function disabledProductCompare(Start $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('if product compare functionality is correctly disabled');

        //(Use product compare) is disabled
        $I->updateInDatabase('oxconfig', ["OXVARVALUE" => ''], ["OXVARNAME" => 'bl_showCompareList']);

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

        $I->dontSeeElement($detailsPage::$addToCompareListLink);
        $detailsPage->openAccountMenu();
        $I->dontSee($I->translate('MY_PRODUCT_COMPARISON'));
        $detailsPage->closeAccountMenu();

        $accountPage = $detailsPage->openAccountPage();
        $I->dontSee($I->translate('MY_PRODUCT_COMPARISON'), $accountPage::$dashboardCompareListPanelHeader);

        //(Use product compare) is enabled
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
