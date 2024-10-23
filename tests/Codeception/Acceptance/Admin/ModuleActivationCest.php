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

#[Group('admin')]
final class ModuleActivationCest
{
    private string $testModule1Id = 'codeception_testModule';
    private string $testModule1Path = 'modules/testModule';

    public function _before(AcceptanceTester $I): void
    {
        $I->installModule(
            codecept_data_dir($this->testModule1Path)
        );
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->deactivateModule($this->testModule1Id);
        $I->uninstallModule($this->testModule1Id);
    }


    public function moduleActivation(AcceptanceTester $I): void
    {
        $I->wantToTest('module activation in normal mode');

        $this->openModuleOverview($I);

        $I->seeElement('#module_activate');
        $I->dontSeeElement('#module_deactivate');

        $I->click('#module_activate');

        $I->seeElement('#module_deactivate');
        $I->dontSeeElement('#module_activate');
    }

    public function moduleActivationInDemoMode(AcceptanceTester $I): void
    {
        $I->wantToTest('module activation disabled in demo mode');
        $I->updateProjectConfigurations(['oxid_demo_shop_mode' => true], []);

        $this->openModuleOverview($I);

        $I->dontSeeElement('#module_activate');
        $I->dontSeeElement('#module_deactivate');
        $I->see(Translator::translate('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE'));

        $I->activateModule($this->testModule1Id);

        $I->dontSeeElement('#module_deactivate');
        $I->dontSeeElement('#module_activate');
        $I->see(Translator::translate('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE'));

        $I->restoreProjectConfigurations();
    }

    private function openModuleOverview(AcceptanceTester $I): void
    {
        $loginPage = $I->loginAdmin();
        $moduleList = $loginPage->openModules();
        $module = $moduleList->selectModule('Codeception test module #1');
        $module->openModuleTab('Overview');
    }
}
