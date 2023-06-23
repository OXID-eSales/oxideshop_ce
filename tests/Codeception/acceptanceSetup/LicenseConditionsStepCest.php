<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceSetup;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceSetupTester;

#[Group('setup')]
final class LicenseConditionsStepCest
{
    public function testWithDeclinedConditions(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('redirect if the conditions are not accepted.');

        $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->returnToWelcomeStepIfLicenseConditionsDeclined();
    }
}
