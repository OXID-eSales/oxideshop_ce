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

use function codecept_data_dir;

#[Group('admin', 'moduleInstall')]
final class ModuleSettingsCest
{
    private string $testModule1Id = 'codeception/testModule';
    private string $testModule1Path = 'modules/testModule';

    public function _before(AcceptanceTester $I): void
    {
        $I->installModule(
            codecept_data_dir($this->testModule1Path)
        );
        $I->activateModule($this->testModule1Id);
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->deactivateModule($this->testModule1Id);
        $I->uninstallModule($this->testModule1Id);
    }

    public function moduleEmptySettingsForm(AcceptanceTester $I): void
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

    public function moduleSettingsForm(AcceptanceTester $I): void
    {
        $I->wantToTest('module settings are loaded from metadata');
        $adminPanel = $I->loginAdmin();
        $moduleList = $adminPanel->openModules();
        $module =  $moduleList->selectModule('Codeception test module #1');
        $module->openModuleTab('Settings');

        $I->click(Translator::translate('Filled Settings Group'));
        $this->checkFilledInitialSettingsLoaded($I);
    }

    private function checkEmptyInitialSettingsLoaded(AcceptanceTester $I): void
    {
        $I->dontSeeCheckboxIsChecked('confbools[testEmptyBoolConfig]');
        $I->seeInField('confstrs[testEmptyStrConfig]', '');
        $I->seeInField('confarrs[testEmptyArrConfig]', '');
        $I->seeInField('confaarrs[testEmptyAArrConfig]', '');
        $I->seeOptionIsSelected('confselects[testEmptySelectConfig]', 'Option 0');
        $I->seeInField('confpassword[testEmptyPasswordConfig]', '');
    }

    private function checkFilledInitialSettingsLoaded(AcceptanceTester $I): void
    {
        $I->seeCheckboxIsChecked('confbools[testFilledBoolConfig]');
        $I->seeInField('confstrs[testFilledStrConfig]', 'testStr');
        $I->seeInField('confarrs[testFilledArrConfig]', "option1\noption2");
        $I->seeInField('confaarrs[testFilledAArrConfig]', "key1 => option1\nkey2 => option2");
        $I->seeInField('confselects[testFilledSelectConfig]', 'Option 2');
        $I->dontSee('confpassword[testFilledPasswordConfig]');
        $I->seeInField('confpassword[testFilledPasswordConfig]', '');
    }

    private function modifyEmptyInitialSettings(AcceptanceTester $I): void
    {
        $I->checkOption('confbools[testEmptyBoolConfig]');
        $I->fillField('confstrs[testEmptyStrConfig]', 'new-string');
        $I->fillField('confarrs[testEmptyArrConfig]', "new-option-1\nnew-option-2");
        $I->fillField('confaarrs[testEmptyAArrConfig]', "key1 => new-option-1\nkey2 => new-option-2");
        $I->selectOption('confselects[testEmptySelectConfig]', 2);
        $I->fillField('.password_input', 'test-password');
        $I->fillField('confpassword[testEmptyPasswordConfig]', 'test-password');
    }

    private function checkModifiedSettingsNotEmpty(AcceptanceTester $I): void
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
