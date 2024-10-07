<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Admin\Languages;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class NewLanguageCreationCest
{
    public function newLanguagesCreation(AcceptanceTester $I): void
    {
        $I->wantToTest('if we can create four new languages successfully.');

        $adminPanel = $I->loginAdmin();
        $languages = $adminPanel->openLanguages();

        $I->amGoingTo('create the first language.');
        $this->createNewLanguage($languages, $I, 'lt', 'Lietuviu');

        $I->amGoingTo('create the second language.');
        $this->createNewLanguage($languages, $I, 'hu', 'Hungarian');

        $I->amGoingTo('create the third language.');
        $this->createNewLanguage($languages, $I, 'mt', 'Maltese');

        $I->amGoingTo('create the fourth language.');
        $this->createNewLanguage($languages, $I, 'es', 'Spanish');

        $I->amGoingTo('generate DB views.');
        $tools = $adminPanel->openTools();
        $tools->updateDbViews();

        $I->amGoingTo('check the new languages fields.');
        $this->checkLanguageFields($I);
    }

    private function createNewLanguage(Languages $languages, AcceptanceTester $I, string $code, string $name): void
    {
        $languages->createNewLanguage($code, $name);
        $I->amGoingTo('check extra messages.');
        $this->checkMessages($I);
    }

    private function checkMessages(AcceptanceTester $I): void
    {
        $I->selectEditFrame();
        $I->expect('not to see the multilingual fields error message. Four language fields are predefined,
        so on the addition of a fifth language, new fields should be added as well.');
        $I->dontSee(Translator::translate('LANGUAGE_ERROR_ADDING_MULTILANG_FIELDS'));
    }

    private function checkLanguageFields(AcceptanceTester $I): void
    {
        $I->retryGrabFromDatabase('oxv_oxarticles_lt', 'oxid', ['oxartnum' => '3503']);
        $I->retryGrabFromDatabase('oxv_oxarticles_hu', 'oxid', ['oxartnum' => '3503']);
        $I->retryGrabFromDatabase('oxv_oxarticles_mt', 'oxid', ['oxartnum' => '3503']);
        $I->retryGrabFromDatabase('oxv_oxarticles_es', 'oxid', ['oxartnum' => '3503']);
    }
}
