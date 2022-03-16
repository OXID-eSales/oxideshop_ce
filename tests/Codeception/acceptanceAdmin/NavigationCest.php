<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\Codeception\Module\Translation\Translator;
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

    public function systemInfo(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('System info page is accessible');

        $adminPanel = $I->loginAdmin();
        $adminPanel->openSystemInfo();

        $I->see('PHP Version');
        $I->see('Configuration');
    }

    public function systemRequirements(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('System requirements page is accessible and translated');
        $untranslatedKeyPrefix = 'SYSREQ_';

        $adminPanel = $I->loginAdmin();
        $adminPanel->openSystemHealth();

        $I->see(Translator::translate('State of system health'));
        $I->dontSee($untranslatedKeyPrefix);
    }

    public function tools(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('Tools page is accessible and translated');
        $untranslatedKeyPrefix = 'TOOLS_';

        $adminPanel = $I->loginAdmin();
        $tools = $adminPanel->openTools();

        $I->see(Translator::translate('Update SQL'));
        $I->seeElement($tools->sqlTextInput);
        $I->seeElement($tools->uploadSqlFileInput);
        $I->seeElement($tools->runUpdateSqlButton);
        $I->seeElement($tools->updateDbViewsButton);

        $I->dontSee($untranslatedKeyPrefix);
    }
}
