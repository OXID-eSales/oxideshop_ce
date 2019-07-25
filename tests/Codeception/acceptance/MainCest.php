<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Page\Home;
use OxidEsales\Codeception\Page\Account\UserOrderHistory;
use OxidEsales\Codeception\Module\Translation\Translator;

class MainCest
{
    public function frontPageWorks(AcceptanceTester $I)
    {
        $homePage = new Home($I);
        $I->amOnPage($homePage->URL);
        $I->see(Translator::translate("HOME"));
    }

    /**
     * @param AcceptanceTester $I
     */
    public function shopBrowsing(AcceptanceTester $I)
    {
        // open start page
        $homePage = new Home($I);
        $I->amOnPage($homePage->URL);

        $I->see(Translator::translate("HOME"));
        $I->see(Translator::translate('START_BARGAIN_HEADER'));

        // open category
        $I->click('Test category 0 [EN] šÄßüл', '#navigation');
        $I->waitForElement('h1', 10);
        $I->see('Test category 0 [EN] šÄßüл', 'h1');

        // check if subcategory exists
        $I->see('Test category 1 [EN] šÄßüл', '#moreSubCat_1');

        //open Details page
        $I->click('#productList_1');

        // login to shop
        $orderHistoryPage = new UserOrderHistory($I);
        $I->amOnPage($orderHistoryPage->URL);
        $I->waitForElement('h1', 10);
        $I->see(Translator::translate('LOGIN'), 'h1');

        $I->fillField($orderHistoryPage->loginUserNameField,'example_test@oxid-esales.dev');
        $I->fillField($orderHistoryPage->loginUserPasswordField,'useruser');
        $I->click($orderHistoryPage->loginButton);

        $I->see(Translator::translate('ORDER_HISTORY'), 'h1');
    }
}
