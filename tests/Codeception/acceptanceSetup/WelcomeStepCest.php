<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceSetup;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\ShopSetup\WelcomeStep;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceSetupTester;

#[Group('setup')]
final class WelcomeStepCest
{
    public function testSendTechnicalInformationCheckboxVisibilityInDifferentEditions(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the visibility of send technical information checkbox in different editions.');

        $welcomeStep = (new WelcomeStep($I))
            ->openTab();

        if ($I->isCommunityEdition()) {
            $welcomeStep->seeTechnicalInfoButton();
        }

        if (!$I->isCommunityEdition()) {
            $welcomeStep->dontSeeTechnicalInfoButton();
        }
    }

    public function testShopLanguageSelection(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the shop language selection has English and German options.');

        (new WelcomeStep($I))
            ->openTab()
            ->selectShopLanguage('Deutsch')
            ->selectShopLanguage('English')
        ;
    }
}
