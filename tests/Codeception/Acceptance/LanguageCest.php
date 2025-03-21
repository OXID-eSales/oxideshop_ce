<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

final class LanguageCest
{
    public function checkLanguageSwitch(AcceptanceTester $I): void
    {
        $I->wantToTest('Check if Language switch works');

        $startPage = $I->openShop();
        $I->seeText('Test category 0 [EN] šÄßüл');
        $startPage->switchLanguage('Deutsch');
        $I->seeText('Test category 0 [DE] šÄßüл');

        $categoryPage = $startPage->openCategoryPage('Test category 0 [DE] šÄßüл');
        $I->seeText('Test category 0 [DE] šÄßüл');
        $startPage->switchLanguage('English');
        $I->seeText('Test category 0 [EN] šÄßüл');

        $categoryPage->openProductDetailsPage(1);
        $I->seeText('Test product 0 [EN] šÄßüл');
        $startPage->switchLanguage('Deutsch');
        $I->seeText('[DE 4] Test product 0 šÄßüл');
    }
}
