<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class ModuleSettingsCest
{
    private $module;
    private $testModule1Id = 'codeception/test-module-1';
    private $testModule1Path = __DIR__ . '/../_data/modules/test-module-1';

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
        $I->uninstallModule($this->testModule1Path, $this->testModule1Id);
    }

    /** @param AcceptanceAdminTester $I */
    public function moduleEmptySettingsForm(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('module empty settings are loaded from metadata and form save works');
        $this->selectModule($I, 'Codeception test module #1');
        $this->module->openModuleTab('Settings');

        $I->click($I->see('Empty settings group'));
        $this->checkEmptyInitialSettingsLoaded($I);

        $this->modifyEmptyInitialSettings($I);
        $I->click('save');
        $I->click($I->see('Empty settings group'));
        $this->checkModifiedSettingsNotEmpty($I);
    }

    /** @param AcceptanceAdminTester $I */
    public function moduleSettingsForm(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('module settings are loaded from metadata');
        $this->selectModule($I, 'Codeception test module #1');
        $this->module->openModuleTab('Settings');

        $I->click($I->see('Filled Settings Group'));
        $this->checkFilledInitialSettingsLoaded($I);
    }

    /**
     * @param AcceptanceAdminTester $I
     * @param string $moduleName
     */
    private function selectModule(AcceptanceAdminTester $I, string $moduleName): void
    {
        $loginPage = $I->loginAdmin();

        $moduleList = $loginPage->openModules();
        $this->module = $moduleList->selectModule($moduleName);
    }

    /** @param AcceptanceAdminTester $I */
    private function checkEmptyInitialSettingsLoaded(AcceptanceAdminTester $I): void
    {
        $I->dontSeeCheckboxIsChecked('confbools[testEmptyBoolConfig]');
        $I->canSeeInField('confstrs[testEmptyStrConfig]', '');
        $I->canSeeInField('confarrs[testEmptyArrConfig]', '');
        $I->canSeeInField('confaarrs[testEmptyAArrConfig]', '');
        $I->canSeeOptionIsSelected('confselects[testEmptySelectConfig]', 'Option 0');
        $I->canSeeInField('confpassword[testEmptyPasswordConfig]', '');
    }

    /** @param AcceptanceAdminTester $I */
    private function checkFilledInitialSettingsLoaded(AcceptanceAdminTester $I): void
    {
        $I->seeCheckboxIsChecked('confbools[testFilledBoolConfig]');
        $I->canSeeInField('confstrs[testFilledStrConfig]', 'testStr');
        $I->canSeeInField('confarrs[testFilledArrConfig]', "option1\noption2");
        $I->canSeeInField('confaarrs[testFilledAArrConfig]', "key1 => option1\nkey2 => option2");
        $I->canSeeInField('confselects[testFilledSelectConfig]', '2');
        $I->dontSee('confpassword[testFilledPasswordConfig]');
        $I->canSeeInField('confpassword[testFilledPasswordConfig]', '');
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
    }

    /** @param AcceptanceAdminTester $I */
    private function checkModifiedSettingsNotEmpty(AcceptanceAdminTester $I): void
    {
        $I->seeCheckboxIsChecked('confbools[testEmptyBoolConfig]');
        $I->canSeeInField('confstrs[testEmptyStrConfig]', 'new-string');
        $I->canSeeInField('confarrs[testEmptyArrConfig]', "new-option-1\nnew-option-2");
        $I->canSeeInField('confaarrs[testEmptyAArrConfig]', "key1 => new-option-1\nkey2 => new-option-2");
        $I->canSeeOptionIsSelected('confselects[testEmptySelectConfig]', 'Option 2');
        $I->dontSee('confpassword[testEmptyPasswordConfig]');
        $I->canSeeInField('confpassword[testEmptyPasswordConfig]', '');
    }
}
