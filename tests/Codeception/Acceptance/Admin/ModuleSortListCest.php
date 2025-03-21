<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class ModuleSortListCest
{
    private string $testModuleId = 'codeception_testModule';
    private string $testModulePath = 'modules/testModule';
    private string $testModuleWithProblemsId = 'codeception_test-module-problems';
    private string $testModuleWithProblemsPath = 'modules/test-module-problems';
    private string $fixtureParentController = 'OxidEsales---Eshop---Application---Controller---ContentController';
    private string $fixtureModuleController =
        'OxidEsales\\\EshopCommunity\\\Tests\\\Codeception\\\Support\\\Data\\\modules\\\testModule\\\Controller\\\ContentController';

    public function moduleClassExtensionsArePresentOnInstalledModulePage(AcceptanceTester $I): void
    {
        $I->wantToTest('module class extensions are present on installed modules page for active and inactive module');
        $I->installModule(
            codecept_data_dir($this->testModulePath)
        );
        $I->deactivateModule($this->testModuleId);

        $moduleList = $I
            ->loginAdmin()
            ->openModules()
            ->selectModule('Codeception test module #1');
        $moduleList->openModuleTab('Installed Shop Modules');

        $I->seeElement("li#$this->fixtureParentController");
        $I->seeElement("li#$this->fixtureModuleController .disabled");

        $moduleList->openModuleTab('Overview');
        $moduleList->activateModule();

        $moduleList->openModuleTab('Installed Shop Modules');
        $I->seeElement("li#$this->fixtureParentController");
        $I->seeElement("li#$this->fixtureModuleController");
        $I->dontSeeElement("li#$this->fixtureModuleController .disabled");

        $I->uninstallModule($this->testModuleId);
    }

    public function moduleWithProblemsSortList(AcceptanceTester $I): void
    {
        $I->wantToTest('module sort list functionality with problematic module');
        $I->installModule(
            codecept_data_dir($this->testModuleWithProblemsPath)
        );
        $moduleList = $I
            ->loginAdmin()
            ->openModules()
            ->selectModule('Module with problems (Namespaced)');

        $moduleList->openModuleTab('Overview');
        $moduleList->activateModule();

        $I->expect('to see info about module problems');
        $moduleList->openModuleTab('Installed Shop Modules');
        $I->seeText(Translator::translate('MODULE_EXTENSIONISDELETED'));
        $I->see(Translator::translate('MODULE_PROBLEMATIC_FILES'));
        $I->see(Article::class);
        $I->see('NonExistentFile');

        $I->amGoingTo('remove problematic configs');
        $I->clickAndWait(['name' => 'yesButton']);
        $I->waitForElementNotVisible(['name' => 'yesButton']);
        $I->dontSee(Translator::translate('MODULE_EXTENSIONISDELETED'));

        $I->expect('to see that the module is not active');
        $moduleList->openModuleTab('Overview');
        $I->seeElement('#module_activate');
        $I->dontSeeElement('#module_deactivate');

        $I->uninstallModule($this->testModuleWithProblemsId);
    }

    public function _failed(AcceptanceTester $I): void
    {
        try {
            $I->uninstallModule($this->testModuleId);
        } catch (\Throwable) {
        }
        try {
            $I->uninstallModule($this->testModuleWithProblemsId);
        } catch (\Throwable) {
        }
    }
}
