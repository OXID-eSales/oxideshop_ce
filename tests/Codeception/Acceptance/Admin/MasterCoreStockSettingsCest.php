<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'core', 'stock')]
final class MasterCoreStockSettingsCest
{
    public function testStockDefaultMessageSettings(AcceptanceTester $I): void
    {
        $I->wantToTest('stock default message settings management');

        $adminPanel = $I->loginAdmin();
        $coreSettings = $adminPanel->openCoreSettings();
        $settingsTab = $coreSettings->openSettingsTab();

        $I->amGoingTo('check that all stock default message options are activated');
        $stockSettings = $settingsTab->openStockSettings();
        $stockSettings->seeInStockMessageSelected();
        $stockSettings->seeLowStockMessageSelected();
        $stockSettings->seeOutOfStockMessageSelected();

        $I->amGoingTo('deactivate all stock default message settings and verify the changes');
        $stockSettings->uncheckInStockMessageOption();
        $stockSettings->uncheckLowStockMessageOption();
        $stockSettings->uncheckOutOfStockMessageOption();
        $settingsTab->save();

        $stockSettings = $settingsTab->openStockSettings();
        $stockSettings->dontSeeLowStockMessageSelected();
        $stockSettings->dontSeeInStockMessageSelected();
        $stockSettings->dontSeeOutOfStockMessageSelected();
    }
}
