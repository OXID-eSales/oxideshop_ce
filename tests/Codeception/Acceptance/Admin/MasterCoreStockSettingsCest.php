<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'core', 'stock')]
final class MasterCoreStockSettingsCest
{
    public function setActiveCategoryAtStart(AcceptanceTester $I): void
    {
        $I->wantToTest('Activate and deactivate default stock message');

        $adminPanel = $I->loginAdmin();
        $coreSettings = $adminPanel->openCoreSettings();
        $settingsTab = $coreSettings->openSettingsTab();

        $I->amGoingTo('Check Low stock default message option');
        $stockDropdown =  $settingsTab->openStockSettings();
        $stockDropdown->checkLowStockMessageOption();
        $settingsTab->save();

        $stockDropdown->seeLowStockMessageSelected();

        $I->amGoingTo('Uncheck Low stock default message option');
        $stockDropdown =  $settingsTab->openStockSettings();
        $stockDropdown->uncheckLowStockMessageOption();
        $settingsTab->save();

        $stockDropdown->dontSeeLowStockMessageSelected();
    }
}
