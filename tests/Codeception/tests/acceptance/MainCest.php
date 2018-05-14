<?php

use Page\UserRegistration;
use Page\Home;
use Page\UserOrderHistory;

class MainCest
{
    public function frontPageWorks(AcceptanceTester $I)
    {
        $I->amOnPage(\Page\Home::$URL);
        $I->see($I->translate("HOME"));
    }

    public function shopBrowsing(AcceptanceTester $I)
    {
        // open start page
        $I->amOnPage(\Page\Home::$URL);

        $I->see($I->translate("HOME"));
        $I->see($I->translate('START_BARGAIN_HEADER'));

        // open category
        $I->click('Test category 0 [EN] šÄßüл', '#navigation');
        $I->see('Test category 0 [EN] šÄßüл', 'h1');

        // check if subcategory exists
        $I->see('Test category 1 [EN] šÄßüл', '#moreSubCat_1');

        //open Details page
        $I->click('#productList_1');

        // login to shop
        $I->amOnPage(UserOrderHistory::$URL);
        $I->see($I->translate('LOGIN'), 'h1');

        $I->fillField(UserOrderHistory::$loginUserNameField,'example_test@oxid-esales.dev');
        $I->fillField(UserOrderHistory::$loginUserPasswordField,'useruser');
        $I->click(UserOrderHistory::$loginButton);

        $I->see($I->translate('ORDER_HISTORY'), 'h1');
    }

}
