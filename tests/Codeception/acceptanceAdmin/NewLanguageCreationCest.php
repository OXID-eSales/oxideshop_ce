<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class NewLanguageCreationCest
{

    /** @var string */
    private $languages;

    /** @var string */
    private $languageParams;

    /** @param AcceptanceAdminTester $I */
    public function _before(AcceptanceAdminTester $I)
    {
        $this->languages = $I->grabConfigValueFromDatabase('aLanguages', 1)['value'];
        $this->languageParams = $I->grabConfigValueFromDatabase('aLanguageParams', 1)['value'];
    }

    /** @param AcceptanceAdminTester $I */
    public function _after(AcceptanceAdminTester $I)
    {
        $I->updateConfigInDatabase('aLanguages', $this->languages);
        $I->updateConfigInDatabase('aLanguageParams', $this->languageParams);
        $I->regenerateDatabaseViews();
    }

    /** @param AcceptanceAdminTester $I */
    public function downloadableFiles(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('if we can create a new language successfully');

        $adminPanel = $I->loginAdmin();
        $languages = $adminPanel->openLanguages();
        $languages->createNewLanguage("lt", "Lietuviu");

        $tools = $adminPanel->openTools();
        $tools->updateDbViews();

        $I->wait(3);

        $b = $I->grabFromDatabase('oxv_oxarticles_lt', 'oxid', ['oxartnum' => "3503"]);
    }
}
