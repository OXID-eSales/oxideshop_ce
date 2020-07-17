<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class ModuleDestructionCest
{
    private $testModule1Path = __DIR__ . '/../_data/modules/WithNamespaceAndMetadataV2';
    private $testModule1Id = 'EshopAcceptanceTestModuleNine';
    private $testModule2Path = __DIR__ . '/../_data/modules/without_own_module_namespace';
    private $testModule2Id = 'without_own_module_namespace';

    /** @param AcceptanceAdminTester $I */
    public function _before(AcceptanceAdminTester $I)
    {
        $I->installModule($this->testModule1Path);
        $I->activateModule($this->testModule1Id);

        $I->installModule($this->testModule2Path);
        $I->activateModule($this->testModule2Id);
    }

    /** @param AcceptanceAdminTester $I */
    public function _after(AcceptanceAdminTester $I)
    {
        $I->deactivateModule($this->testModule1Id);
        $I->deactivateModule($this->testModule2Id);
        $I->uninstallModule($this->testModule1Path, $this->testModule1Id);
        $I->uninstallModule($this->testModule2Path, $this->testModule2Id);
    }
        
    /** @param AcceptanceAdminTester $I */
    public function testPhysicallyDeleteNamespacedModuleWithoutDeactivation(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('Physically remove an activated module from shop without deactivating it.');
        
        exec('rm ' . $I->getShopModulePath($this->testModule1Path) . ' -R');

        $this->checkAdmin($I);
    }

    /** @param AcceptanceAdminTester $I */
    public function testPhysicallyDeleteNamespacedModuleWithDeactivationAsOnlyModule(AcceptanceAdminTester $I): void
    {
        $I->uninstallModule($this->testModule2Path, $this->testModule2Id);
        $I->wantToTest('Activate then deactivate only module and then physically remove it from shop.');
        
        $I->activateModule($this->testModule1Id);
        $I->deactivateModule($this->testModule1Id);
        exec('rm ' . $I->getShopModulePath($this->testModule1Path) . ' -R');

        $this->checkAdmin($I);

        $I->installModule($this->testModule2Path);
    }

    /** @param AcceptanceAdminTester $I */
    public function testPhysicallyDeleteNamespacedModuleWithDeactivation(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('Activate then deactivate a namespace module and then physically remove it from shop.');
        
        $I->activateModule($this->testModule1Id);
        $I->deactivateModule($this->testModule1Id);
        exec('rm ' . $I->getShopModulePath($this->testModule1Path) . ' -R');

        $this->checkAdmin($I);
    }

    protected function checkAdmin(AcceptanceAdminTester $I)
    {
        $adminPanel = $I->loginAdmin();
        $moduleList = $adminPanel->openModules();

        $I->see('Problematic Files');
        $I->see($this->testModule1Id . '/metadata.php');

        $I->click(['name' => 'yesButton']);

        $moduleList->selectModule('Test module #9 - namespaced (EshopAcceptanceTestModuleNine)');

        $I->seeElement('#module_activate');
        $I->dontSeeElement('#module_deactivate');
    }
}
