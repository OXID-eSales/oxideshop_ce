<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Admin\Theme\ThemeSettingsTab;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'theme')]
final class ThemeSettingsCest
{
    private string $themeTitle = 'APEX Theme';
    private string $settingGroup = 'display';
    private string $boolSetting = 'blShowBirthdayFields';
    private string $selectSetting = 'sDefaultListDisplayType';

    public function themeSettingsTabShowsSettingsOfTheActiveTheme(AcceptanceTester $I): void
    {
        $I->wantTo('see the theme settings form with values from the YAML configuration');

        $settingsTab = $this->openThemeSettingsTab($I);

        $I->expect('the settings of the display group are visible with their configured values');
        $settingsTab
            ->seeThemeTitle($this->themeTitle)
            ->openSettingGroup($this->getSettingGroupTitle())
            ->seeBoolSettingIsChecked($this->boolSetting)
            ->seeSettingValue($this->selectSetting, 'grid');
    }

    public function savedThemeSettingsArePersistedAndRedisplayed(AcceptanceTester $I): void
    {
        $I->wantTo('change theme settings in the admin and see them persisted');

        $settingsTab = $this->openThemeSettingsTab($I);

        $I->amGoingTo('disable a bool setting and change a select setting');
        $settingsTab
            ->openSettingGroup($this->getSettingGroupTitle())
            ->uncheckBoolSetting($this->boolSetting)
            ->selectSettingOption($this->selectSetting, 'line')
            ->save();

        $I->expect('the changed values are shown after the form is redisplayed');
        $settingsTab
            ->openSettingGroup($this->getSettingGroupTitle())
            ->dontSeeBoolSettingIsChecked($this->boolSetting)
            ->seeSettingValue($this->selectSetting, 'line');
    }

    private function openThemeSettingsTab(AcceptanceTester $I): ThemeSettingsTab
    {
        return $I->loginAdmin()
            ->openThemes()
            ->selectTheme($this->themeTitle)
            ->openSettingsTab();
    }

    private function getSettingGroupTitle(): string
    {
        return Translator::translate('SHOP_THEME_GROUP_' . $this->settingGroup);
    }
}
