<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceSetup;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\ShopSetup\SystemRequirementsStep;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceSetupTester;

#[Group('setup')]
final class SystemRequirementsStepCest
{
    public function testInstallationLanguageChange(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the installation language selection between English and German');

        $I->expectTo('switch between languages');
        (new SystemRequirementsStep($I))
            ->openTab()
            ->selectInstallationLanguage('Deutsch')
            ->selectInstallationLanguage('English');
    }

    public function testItShowsTranslatedModuleText(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the page shows well module names and groups');

        $systemRequirementsStep = (new SystemRequirementsStep($I))
            ->openTab()
            ->selectInstallationLanguage('English');

        $I->expect('the requirement groups are loaded');
        $systemRequirementsStep->seeRequirementGroups();

        $I->expect('the module names are loaded properly');
        $systemRequirementsStep->seeTranslatedModules();
    }

    public function testItShouldNotAllowTheInstallation(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the installation can not proceed due to Htaccess problem');

        $I->backupHtaccessFile();

        $I->amGoingTo('open the requirement step.');
        $systemRequirementsStep = (new SystemRequirementsStep($I))
            ->openTab()
            ->selectInstallationLanguage('English');

        $I->expect('a valid step.');
        $systemRequirementsStep->seeModRewriteFitting();

        $I->amGoingTo('remove the htaccess file temporarily.');
        $I->removeHtaccessFile();
        $I->reloadPage();

        $I->expectTo('get an error about the missing rewrite module.');
        $systemRequirementsStep->dontSeeModRewriteFitting();

        $I->amGoingTo('restore the htaccess file.');
        $I->restoreHtaccessFile();
    }
}
