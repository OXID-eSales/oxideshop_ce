<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Admin\Locales;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'locales')]
final class LanguageLocaleCest
{
    private string $testLocaleCode = 'te_ST';

    public function localeDropdownIsShownWithAvailableLocales(AcceptanceTester $I): void
    {
        $I->wantToTest('that the locale dropdown is shown when editing a language');

        $I->loginAdmin()
            ->openLanguages()
            ->openLanguageByName('Deutsch')
            ->seeLocalesAvailable('Deutsch (Deutschland)', 'English (United Kingdom)');
    }

    public function localeCanBeSavedForLanguage(AcceptanceTester $I): void
    {
        $I->wantToTest('that a locale can be selected and saved for a language');

        $I->loginAdmin()
            ->openLanguages()
            ->openLanguageByName('Deutsch')
            ->selectLocale('en_GB')
            ->save()
            ->seeLocaleSelected('English (United Kingdom) (en_GB)');
    }

    public function localeManagerShowsConfiguredLocales(AcceptanceTester $I): void
    {
        $I->wantToTest('locale manager shows configured locales with fallback selections');

        $locales = $this->openLocales($I);

        $locales
            ->seeLocale('de_DE')
            ->seeLocaleName('de_DE', 'Deutsch (Deutschland)')
            ->seeLocaleIsActive('de_DE')
            ->seeLocale('en_GB')
            ->seeLocaleName('en_GB', 'English (United Kingdom)')
            ->seeLocaleFallbackSelected('en_GB', 'Deutsch (Deutschland) (de_DE)')
            ->seeLocaleIsActive('en_GB');

        $locales->seeLocaleFallbacksAvailable(
            'de_DE',
            'English (United Kingdom) (en_GB)'
        );
    }

    public function addLocaleAndChangeValues(AcceptanceTester $I): void
    {
        $I->wantToTest('adding and changing a locale');

        $this->addTestLocale($I)
            ->seeLocale($this->testLocaleCode)
            ->seeLocaleName($this->testLocaleCode, 'Test Locale')
            ->seeLocaleFallbackSelected($this->testLocaleCode, 'Deutsch (Deutschland) (de_DE)')
            ->seeLocaleIsActive($this->testLocaleCode)
            ->fillLocaleName($this->testLocaleCode, 'Changed Test Locale')
            ->selectLocaleFallback($this->testLocaleCode, 'en_GB')
            ->save()
            ->seeLocaleName($this->testLocaleCode, 'Changed Test Locale')
            ->seeLocaleFallbackSelected($this->testLocaleCode, 'English (United Kingdom) (en_GB)');
    }

    public function changeLocaleActiveState(AcceptanceTester $I): void
    {
        $I->wantToTest('changing locale active state');

        $locales = $this->addTestLocale($I)
            ->seeLocaleIsActive($this->testLocaleCode)
            ->toggleLocaleActive($this->testLocaleCode)
            ->save()
            ->seeLocaleIsInactive($this->testLocaleCode);

        $locales
            ->toggleLocaleActive($this->testLocaleCode)
            ->save()
            ->seeLocaleIsActive($this->testLocaleCode);
    }

    public function deleteLocale(AcceptanceTester $I): void
    {
        $I->wantToTest('deleting an existing locale');

        $this->addTestLocale($I)
            ->deleteLocale($this->testLocaleCode)
            ->dontSeeLocale($this->testLocaleCode);
    }

    private function addTestLocale(AcceptanceTester $I): Locales
    {
        return $this->openLocales($I)
            ->addLocale($this->testLocaleCode, 'Test Locale', 'de_DE')
            ->save();
    }

    private function openLocales(AcceptanceTester $I): Locales
    {
        return $I->loginAdmin()->openLocales();
    }
}
