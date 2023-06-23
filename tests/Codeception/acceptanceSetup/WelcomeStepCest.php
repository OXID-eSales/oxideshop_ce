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
final class WelcomeStepCest
{
    public function testDataCollectionCheckbox(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('visibility of allow-data-collection-checkbox in different editions.');

        $welcomeStep = $I
            ->openShopSetup()
            ->selectInstallationLanguage('English')
            ->proceedToWelcomeStep();

        if ($I->isCommunityEdition()) {
            $welcomeStep->seeAllowDataCollectionInput();
        } else {
            $welcomeStep->dontSeeAllowDataCollectionInput();
        }
    }
}
