<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use Codeception\Util\Locator;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class ActiveCategoryAtStartCest
{
    /** @param AcceptanceAdminTester $I */
    public function setActiveCategoryAtStart(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('Activate and deactivate category at start');

        $I->clearShopCache();
        $adminPanel = $I->loginAdmin();
        $coreSettings = $adminPanel->openCoreSettings();
        $settingsTab = $coreSettings->openSettingsTab();

        $settingsTab =  $settingsTab->openShopFrontendDropdown();
       
        $I->seeElement("//input[@value='---']");
        $categoryPopup = $settingsTab->openStartCategoryPopup();

        $category = 'Test category 1 [DE] šÄßüл';
        $categoryPopup = $categoryPopup->selectCategory($category);
        $categoryPopup = $categoryPopup->unsetCategory();
        $categoryPopup->selectCategory($category);

        $I->closeTab();
        $I->switchToPreviousTab();
        
        $I->clearShopCache();
        $adminPanel = $I->loginAdmin();
        
        $coreSettings = $adminPanel->openCoreSettings();
        $settingsTab = $coreSettings->openSettingsTab();

        $settingsTab->openShopFrontendDropdown();
        $I->seeElement(Locator::find('input', ['value' => $category]));
    }
}
