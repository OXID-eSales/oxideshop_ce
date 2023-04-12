<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceSetup;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\ShopSetup\LicenseConditionsStep;
use OxidEsales\Codeception\ShopSetup\WelcomeStep;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceSetupTester;

#[Group('setup')]
final class LicenseConditionsStepCest
{
    public function testItShouldRedirectToTheWelcomePage(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the setup redirects to the welcome page if the conditions are not accepted.');

        $I->amGoingTo('submit the form without accepting the conditions.');
        $licenseConditionsStep = (new LicenseConditionsStep($I))
            ->openTab()
            ->doNotAcceptLicenseConditions()
            ->submit();

        $I->expect('the installation is canceled.');
        $licenseConditionsStep->seeInstallationCanceledErrorMessage();

        $I->expectTo('redirect back to the welcome step.');
        (new WelcomeStep($I))->seeDeliveryCountrySelect();
    }
}
