<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class LanguageLocaleCest
{
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
}
