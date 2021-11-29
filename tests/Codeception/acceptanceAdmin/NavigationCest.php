<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class NavigationCest
{
    public function shopsStartPageButton(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('"Shop\'s start page" button');
        $adminPanel = $I->loginAdmin();
        $I->seeInCurrentUrl('admin');
        $adminPanel->openShopsStartPage();
        $I->dontSeeInCurrentUrl('admin');
    }
}
