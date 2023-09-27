<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceSetup;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceSetupTester;

#[Group('setup')]
final class SystemRequirementsStepCest
{
    public function testWithNoErrors(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('system requirements page is displayed and no error messages shown');

        $I
            ->openShopSetup()
            ->selectInstallationLanguage('Deutsch')
            ->selectInstallationLanguage('English')
            ->seeSystemRequirementsStatusPage()
            ->dontSeeStatusCheckErrors();
    }

    public function testWithFailedRequirementCheck(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('installation can not proceed due to .htaccess file problem');

        $I->amGoingTo('remove the htaccess file temporarily.');
        $I->backupHtaccessFile();
        $I->removeHtaccessFile();

        $I
            ->openShopSetup()
            ->returnToSystemRequirementsStepIfModRewriteCheckFailed();

        $I->restoreHtaccessFile();
    }
}
