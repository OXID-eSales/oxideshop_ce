<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class ModuleActivationCest
{
    private $testModule1Id = 'codeception_testModule';
    private $testModule1Path = __DIR__ . '/../_data/modules/testModule';

    /** @param AcceptanceAdminTester $I */
    public function _before(AcceptanceAdminTester $I)
    {
        $I->installModule($this->testModule1Path);
    }

    /** @param AcceptanceAdminTester $I */
    public function _after(AcceptanceAdminTester $I)
    {
        $I->deactivateModule($this->testModule1Id);
        $I->uninstallModule($this->testModule1Id);
    }


    /** @param AcceptanceAdminTester $I */
    public function moduleActivation(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('module activation in normal mode');
        
        $this->openModuleOverview($I);

        $I->seeElement('#module_activate');
        $I->dontSeeElement('#module_deactivate');

        $I->click('#module_activate');

        $I->seeElement('#module_deactivate');
        $I->dontSeeElement('#module_activate');
    }

    /** @param AcceptanceAdminTester $I */
    public function moduleActivationInDemoMode(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('module activation disabled in demo mode');
        $I->updateConfigInDatabase('blDemoShop', true, 'bool');
        
        $this->openModuleOverview($I);

        $I->dontSeeElement('#module_activate');
        $I->dontSeeElement('#module_deactivate');
        $I->see(Translator::translate('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE'));

        $I->activateModule($this->testModule1Id);

        $I->dontSeeElement('#module_deactivate');
        $I->dontSeeElement('#module_activate');
        $I->see(Translator::translate('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE'));

        $I->updateConfigInDatabase('blDemoShop', false, 'bool');
    }

    /** @param AcceptanceAdminTester $I */
    private function openModuleOverview(AcceptanceAdminTester $I): void
    {
        $loginPage = $I->loginAdmin();
        $moduleList = $loginPage->openModules();
        $module = $moduleList->selectModule('Codeception test module #1');
        $module->openModuleTab('Overview');
    }
}
