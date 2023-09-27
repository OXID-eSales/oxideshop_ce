<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Admin\ModulesList;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class ModuleSortListCest
{
    private ModulesList $module;
    private string $testModuleId = 'codeception_testModule';
    private string $testModulePath = 'modules/testModule';
    private string $testModuleWithProblemsId = 'codeception_test-module-problems';
    private string $testModuleWithProblemsPath = 'modules/test-module-problems';

    public function moduleClassExtensionsArePresentOnInstalledModulePage(AcceptanceTester $I): void
    {
        $I->wantToTest('module class extensions are present on installed modules page for active and inactive module');
        $I->installModule(
            codecept_data_dir($this->testModulePath)
        );
        $I->deactivateModule($this->testModuleId);
        $this->selectModule($I, 'Codeception test module #1');
        $this->module->openModuleTab('Installed Shop Modules');

        //OxidEsales\EshopCommunity\Tests\Codeception\Support\Data\modules\testModule\Controller;
        $I->seeElement('li#OxidEsales---Eshop---Application---Controller---ContentController');
        $I->seeElement('li#OxidEsales\\\EshopCommunity\\\Tests\\\Codeception\\\Support\\\Data\\\modules\\\testModule\\\Controller\\\ContentController .disabled');

        $this->activateSelectedModule($I);
        $this->module->openModuleTab('Installed Shop Modules');

        $I->seeElement('li#OxidEsales---Eshop---Application---Controller---ContentController');
        $I->seeElement('li#OxidEsales\\\EshopCommunity\\\Tests\\\Codeception\\\Support\\\Data\\\modules\\\testModule\\\Controller\\\ContentController');
        $I->dontSeeElement('li#OxidEsales\\\EshopCommunity\\\Tests\\\Codeception\\\Support\\\Data\\\modules\\\testModule\\\Controller\\\ContentController .disabled');

        $I->uninstallModule($this->testModuleId);
    }

    public function moduleWithProblemsSortList(AcceptanceTester $I): void
    {
        $I->wantToTest('module sort list functionality with problematic module');
        $I->installModule(
            codecept_data_dir($this->testModuleWithProblemsPath)
        );
        $this->selectModule($I, 'Module with problems (Namespaced)');

        $this->activateSelectedModule($I);
        $this->module->openModuleTab('Installed Shop Modules');
        /** info about existing problems is displayed */
        $I->see(Translator::translate('MODULE_EXTENSIONISDELETED'));
        $I->see(Translator::translate('MODULE_PROBLEMATIC_FILES'));
        $I->see(Article::class);
        $I->see('NonExistentFile');

        /** click remove problematic configs */
        $I->click(['name' => 'yesButton']);

        $I->dontSee(Translator::translate('MODULE_EXTENSIONISDELETED'));
        /** check module's not active */
        $this->module->openModuleTab('Overview');
        $I->seeElement('#module_activate');
        $I->dontSeeElement('#module_deactivate');

        $I->uninstallModule($this->testModuleWithProblemsId);
    }

    private function selectModule(AcceptanceTester $I, string $moduleId): void
    {
        $loginPage = $I->loginAdmin();
        $moduleList = $loginPage->openModules();
        $this->module = $moduleList->selectModule($moduleId);
    }

    private function activateSelectedModule(AcceptanceTester $I): void
    {
        $this->module->openModuleTab('Overview');
        $I->click('#module_activate');
    }
}
