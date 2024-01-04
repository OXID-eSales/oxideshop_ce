<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class ModuleSettingsCest
{
    private string $testModule1Id = 'codeception_testModule';
    private string $testModule1Path = __DIR__ . '/../_data/modules/testModule';

    /** @param AcceptanceAdminTester $I */
    public function _before(AcceptanceAdminTester $I)
    {
        $I->installModule($this->testModule1Path);
        $I->activateModule($this->testModule1Id);
    }

    /** @param AcceptanceAdminTester $I */
    public function _after(AcceptanceAdminTester $I)
    {
        $I->deactivateModule($this->testModule1Id);
        $I->uninstallModule($this->testModule1Id);
    }

    /** @param AcceptanceAdminTester $I */
    public function moduleEmptySettingsForm(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('module empty settings are loaded from metadata and form save works');
        $adminPanel = $I->loginAdmin();
        $moduleList = $adminPanel->openModules();
        $module =  $moduleList->selectModule('Codeception test module #1');
        $module->openModuleTab('Settings');

        $I->click(Translator::translate('Empty Settings Group'));
        $this->checkEmptyInitialSettingsLoaded($I);

        $this->modifyEmptyInitialSettings($I);
        $I->click('save');

        $I->waitForText('Empty Settings Group');
        $I->see('Empty Settings Group');
        $I->click('Empty Settings Group');
        $this->checkModifiedSettingsNotEmpty($I);
    }

    /** @param AcceptanceAdminTester $I */
    public function moduleSettingsForm(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('module settings are loaded from metadata');
        $adminPanel = $I->loginAdmin();
        $moduleList = $adminPanel->openModules();
        $module =  $moduleList->selectModule('Codeception test module #1');
        $module->openModuleTab('Settings');

        $I->click(Translator::translate('Filled Settings Group'));
        $this->checkFilledInitialSettingsLoaded($I);
    }

    /** @param AcceptanceAdminTester $I */
    private function checkEmptyInitialSettingsLoaded(AcceptanceAdminTester $I): void
    {
        $I->dontSeeCheckboxIsChecked('confbools[testEmptyBoolConfig]');
        $I->seeInField('confstrs[testEmptyStrConfig]', '');
        $I->seeInField('confarrs[testEmptyArrConfig]', '');
        $I->seeInField('confaarrs[testEmptyAArrConfig]', '');
        $I->seeOptionIsSelected('confselects[testEmptySelectConfig]', 'Option 0');
        $I->seeInField('confpassword[testEmptyPasswordConfig]', '');
    }

    /** @param AcceptanceAdminTester $I */
    private function checkFilledInitialSettingsLoaded(AcceptanceAdminTester $I): void
    {
        $I->seeCheckboxIsChecked('confbools[testFilledBoolConfig]');
        $I->seeInField('confstrs[testFilledStrConfig]', 'testStr');
        $I->seeInField('confarrs[testFilledArrConfig]', "option1\noption2");
        $I->seeInField('confaarrs[testFilledAArrConfig]', "key1 => option1\nkey2 => option2");
        $I->seeInField('confselects[testFilledSelectConfig]', 'Option 2');
        $I->dontSee('confpassword[testFilledPasswordConfig]');
        $I->seeInField('confpassword[testFilledPasswordConfig]', '');
    }

    /** @param AcceptanceAdminTester $I */
    private function modifyEmptyInitialSettings(AcceptanceAdminTester $I): void
    {
        $I->checkOption('confbools[testEmptyBoolConfig]');
        $I->fillField('confstrs[testEmptyStrConfig]', 'new-string');
        $I->fillField('confarrs[testEmptyArrConfig]', "new-option-1\nnew-option-2");
        $I->fillField('confaarrs[testEmptyAArrConfig]', "key1 => new-option-1\nkey2 => new-option-2");
        $I->selectOption('confselects[testEmptySelectConfig]', 2);
        $I->fillField('.password_input', 'test-password');
        $I->fillField('confpassword[testEmptyPasswordConfig]', 'test-password');
    }

    /** @param AcceptanceAdminTester $I */
    private function checkModifiedSettingsNotEmpty(AcceptanceAdminTester $I): void
    {
        $I->seeCheckboxIsChecked('confbools[testEmptyBoolConfig]');
        $I->seeInField('confstrs[testEmptyStrConfig]', 'new-string');
        $I->seeInField('confarrs[testEmptyArrConfig]', "new-option-1\nnew-option-2");
        $I->seeInField('confaarrs[testEmptyAArrConfig]', "key1 => new-option-1\nkey2 => new-option-2");
        $I->seeOptionIsSelected('confselects[testEmptySelectConfig]', 'Option 2');
        $I->dontSee('confpassword[testEmptyPasswordConfig]');
        $I->seeInField('confpassword[testEmptyPasswordConfig]', '');
    }
}
