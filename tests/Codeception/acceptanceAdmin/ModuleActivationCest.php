<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class ModuleActivationCest
{
    private $testModule1Id = 'codeception/test-module-1';
    private $testModule1Path = __DIR__ . '/../_data/modules/test-module-1';

    /** @param AcceptanceAdminTester $I */
    public function moduleActivation(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('module activation in normal mode');
        $I->installModule($this->testModule1Path);
        $this->openModuleOverview($I);

        $I->seeElement('#module_activate');
        $I->dontSeeElement('#module_deactivate');

        $I->click('#module_activate');

        $I->seeElement('#module_deactivate');
        $I->dontSeeElement('#module_activate');

        $I->uninstallModule($this->testModule1Path, $this->testModule1Id);
    }

    /** @param AcceptanceAdminTester $I */
    public function moduleActivationInDemoMode(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('module activation disabled in demo mode');
        $I->updateConfigInDatabase('blDemoShop', true, 'bool');
        $I->installModule($this->testModule1Path);
        $this->openModuleOverview($I);

        $I->dontSeeElement('#module_activate');
        $I->dontSeeElement('#module_deactivate');
        $I->see(Translator::translate('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE'));

        $I->activateModule($this->testModule1Id);

        $I->dontSeeElement('#module_deactivate');
        $I->dontSeeElement('#module_activate');
        $I->see(Translator::translate('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE'));

        $I->updateConfigInDatabase('blDemoShop', false, 'bool');
        $I->uninstallModule($this->testModule1Path, $this->testModule1Id);
    }

    /** @param AcceptanceAdminTester $I */
    private function openModuleOverview(AcceptanceAdminTester $I): void
    {
        $userData = Fixtures::get('adminUser');
        $loginPage = $I->openAdmin();
        $loginPage->login($userData['userLoginName'], $userData['userPassword']);
        $moduleList = $loginPage->openModules();
        $module = $moduleList->selectModule('Codeception test module #1');
        $module->openModuleTab('Overview');
    }
}
