<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class ThemeManagementCest
{
    public function themeListAndOverviewRenderInAdmin(AcceptanceTester $I): void
    {
        $I->wantToTest('admin theme list and overview render and show the active theme');

        $this->openThemeOverview($I);
        $I->see(Translator::translate('THEME_VERSION'));
    }

    public function themeSettingsTabRendersInAdmin(AcceptanceTester $I): void
    {
        $I->wantToTest('admin theme settings tab renders the settings form');

        $this->openThemeOverview($I);
        $this->openThemeSettingsTab($I);
        $I->waitForText('Favicons');
    }

    public function themeSettingsSaveInAdmin(AcceptanceTester $I): void
    {
        $I->wantToTest('admin theme settings save persists changed values');

        $this->openThemeOverview($I);
        $this->openThemeSettingsTab($I);

        $displayGroup = 'Display';
        $this->expandSettingsGroup($I, $displayGroup);
        $I->seeInField('confarrs[aNrofCatArticles]', '20');

        $I->fillField('confarrs[aNrofCatArticles]', "15\n30\n60");
        $I->clickAndWait('save');

        $I->selectEditFrame();
        $I->waitForDocumentReadyState();
        $this->expandSettingsGroup($I, $displayGroup);
        $I->seeInField('confarrs[aNrofCatArticles]', "15\n30\n60");
    }

    public function themeActivateInAdmin(AcceptanceTester $I): void
    {
        $I->wantToTest('admin theme activation activates the selected theme');

        $I->setThemeActivated(false);

        $this->openThemeOverview($I);
        $activateButton = \sprintf("//input[@type='submit' and @value='%s']", Translator::translate('THEME_ACTIVATE'));
        $I->seeElement($activateButton);
        $I->clickAndWait($activateButton);

        $I->selectEditFrame();
        $I->waitForText('APEX Theme');
        $I->dontSeeElement($activateButton);
    }

    private function openThemeOverview(AcceptanceTester $I): void
    {
        $I->loginAdmin();

        $I->selectNavigationFrame();
        $I->clickAndWait(Translator::translate('mxextensions'));
        $I->clickAndWait(Translator::translate('mxtheme'));

        $I->selectListFrame();
        $I->waitForText('APEX Theme');
        $I->click('APEX Theme');

        $I->selectEditFrame();
        $I->waitForText('APEX Theme');
    }

    private function openThemeSettingsTab(AcceptanceTester $I): void
    {
        $I->selectListFrame();
        $I->clickAndWait(\sprintf("//div[@class='tabs']//a[text()='%s']", Translator::translate('tbcltheme_config')));
        $I->selectEditFrame();
        $I->waitForDocumentReadyState();
    }

    private function expandSettingsGroup(AcceptanceTester $I, string $groupTitle): void
    {
        $I->clickAndWait(\sprintf("//a[normalize-space()='%s']", $groupTitle));
    }
}
