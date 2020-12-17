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
    private $testModule1Id = 'codeception/test-module-1';
    private $testModule1Path = __DIR__ . '/../_data/modules/test-module-1';

    /** @param AcceptanceAdminTester $I */
    public function moduleSettingsForm(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('module settings are loaded from metadata and form save works');
        $I->installModule($this->testModule1Path);
        $I->activateModule($this->testModule1Id);

        $adminPanel = $I->loginAdmin();
        $moduleList = $adminPanel->openModules();
        $module =  $moduleList->selectModule('Codeception test module #1');
        $module->openModuleTab('Settings');

        $I->click($I->see('Empty Settings Group'));
        $this->checkEmptyInitialSettingsLoaded($I);

        $I->click($I->see('Filled Settings Group'));
        $this->checkFilledInitialSettingsLoaded($I);

        $I->click($I->see('Empty Settings Group'));
        $this->modifyEmptyInitialSettings($I);
        $I->click('save');

        $I->waitForText('Empty Settings Group');
        $I->click($I->see('Empty Settings Group'));
        $this->checkModifiedSettingsNotEmpty($I);

        $I->uninstallModule($this->testModule1Path, $this->testModule1Id);
    }

    /** @param AcceptanceAdminTester $I */
    private function checkEmptyInitialSettingsLoaded(AcceptanceAdminTester $I): void
    {
        $I->dontSeeCheckboxIsChecked('confbools[testEmptyBoolConfig]');
        $I->seeInField('confstrs[testEmptyStrConfig]', '');
        $I->seeInField('confarrs[testEmptyArrConfig]', '');
        $I->seeInField('confaarrs[testEmptyAArrConfig]', '');
        $I->seeInField('confselects[testEmptySelectConfig]', 0);
        $I->seeInField('confpassword[testEmptyPasswordConfig]', '');
    }

    /** @param AcceptanceAdminTester $I */
    private function checkFilledInitialSettingsLoaded(AcceptanceAdminTester $I): void
    {
        $I->seeCheckboxIsChecked('confbools[testFilledBoolConfig]');
        $I->seeInField('confstrs[testFilledStrConfig]', 'testStr');
        $I->seeInField('confarrs[testFilledArrConfig]', "option1\noption2");
        $I->seeInField('confaarrs[testFilledAArrConfig]', "key1 => option1\nkey2 => option2");
        $I->seeInField('confselects[testFilledSelectConfig]', 2);
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
    }

    /** @param AcceptanceAdminTester $I */
    private function checkModifiedSettingsNotEmpty(AcceptanceAdminTester $I): void
    {
        $I->seeCheckboxIsChecked('confbools[testEmptyBoolConfig]');
        $I->seeInField('confstrs[testEmptyStrConfig]', 'new-string');
        $I->seeInField('confarrs[testEmptyArrConfig]', "new-option-1\nnew-option-2");
        $I->seeInField('confaarrs[testEmptyAArrConfig]', "key1 => new-option-1\nkey2 => new-option-2");
        $I->seeInField('confselects[testEmptySelectConfig]', 2);
        $I->dontSee('confpassword[testEmptyPasswordConfig]');
        $I->seeInField('confpassword[testEmptyPasswordConfig]', '');
    }
}
