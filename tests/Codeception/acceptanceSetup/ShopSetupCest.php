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
final class ShopSetupCest
{
    private string $maintenancePage = '//div[@class="page"] / img[@class="logo"]';
    private string $homePage = '//div[@id="wrapper" and @class="wrapper"]';

    public function testInstallShopWithoutDemoData(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the full setup flow without demo data.');

        if (!$I->isCommunityEdition()) {
            $I->markTestSkipped('This test is for Community edition only.');
        }

        $I->amGoingTo('start the setup with the system requirements step.');
        $welcomeStep = (new SystemRequirementsStep($I))
            ->openTab()
            ->selectInstallationLanguage('English')
            ->goToWelcomeStep();

        $I->expect('the license conditions step after setup and submit the welcome step data.');
        $licenseConditionsStep = $welcomeStep
            ->selectDeliveryCountry('Germany')
            ->selectShopLanguage('English')
            ->selectUpdateCheck()
            ->goToLicenseConditionStep();

        $I->expect('the database step after accepting the license conditions.');
        $databaseStep = $licenseConditionsStep
            ->acceptLicenseConditions()
            ->goToDBStep();

        $I->expect('the directory and login step after filling and submit the DB connection data.');
        $directoryAndLoginStep = $databaseStep
            ->fillDatabaseConnectionFields(
                $I->getDbHost(),
                $I->getDbPort(),
                $I->getDbName(),
                $I->getDbUserName(),
                $I->getDbUserPassword(),
            )
            ->dontInstallDemoData()
            ->goToDirectoryAndLoginStep();

        $I->expectTo('to reach the final step after filling and submit the form.');
        $finishStep = $directoryAndLoginStep
            ->fillAdminCredentials(
                'test-user@login.email',
                'test123',
                'test123'
            )
            ->goToFinalStep();

        $I->expectTo('see the admin link.');
        $finishStep->seeAdminLink();

        $I->amGoingTo('go to the shop home page.');
        $finishStep->goToShop();
        $I->switchToNextTab();

        $I->expectTo('see the maintenance mode, because of the inactive theme.');
        $I->waitForElement($this->maintenancePage);

        $I->amGoingTo('activate a shop theme.');
        $I->activateTheme($I->getThemeId());

        $I->expectTo('see the shop home page after the page reload.');
        $I->reloadPage();
        $I->waitForElement($this->homePage);
        $I->closeTab();
    }
}
