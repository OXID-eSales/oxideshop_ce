<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Command;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Module\ModuleInstaller;
use OxidEsales\Eshop\Core\Module\ModuleList;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Module\Command\ModuleDeactivateCommand;
use Symfony\Component\Console\Input\ArrayInput;

class ModuleDeactivateCommandTest extends ModuleCommandsTestCase
{
    public function testModuleDeactivation()
    {
        $moduleId = 'testmodule';
        $this->prepareTestData();
        $this->activateModule($moduleId);

        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_collection_builder'),
            new ArrayInput(['command' => 'oe:module:deactivate', 'module-id' => $moduleId])
        );

        $this->assertSame(sprintf(ModuleDeactivateCommand::MESSAGE_MODULE_DEACTIVATED, $moduleId) . PHP_EOL, $consoleOutput);

        $module = oxNew(Module::class);
        $module->load($moduleId);
        $this->assertFalse($module->isActive());

        $this->cleanupTestData();
    }

    public function testWhenModuleNotActive()
    {
        $moduleId = 'testmodule';
        $this->prepareTestData();

        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_collection_builder'),
            new ArrayInput(['command' => 'oe:module:deactivate', 'module-id' => $moduleId])
        );

        $this->assertSame(sprintf(ModuleDeactivateCommand::MESSAGE_NOT_POSSIBLE_TO_DEACTIVATE, $moduleId) . PHP_EOL, $consoleOutput);

        $this->cleanupTestData();
    }

    public function testNonExistingModuleActivation()
    {
        $moduleId = 'test';
        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_collection_builder'),
            new ArrayInput(['command' => 'oe:module:deactivate', 'module-id' => $moduleId])
        );

        $this->assertSame(sprintf(ModuleDeactivateCommand::MESSAGE_MODULE_NOT_FOUND, $moduleId) . PHP_EOL, $consoleOutput);
    }

    private function activateModule(string $moduleId)
    {
        /** @var ModuleInstaller $moduleInstaller */
        $moduleInstaller = Registry::get(ModuleInstaller::class);
        $moduleList = oxNew(ModuleList::class);
        $moduleList->getModulesFromDir(Registry::getConfig()->getModulesDir());
        $modules = $moduleList->getList();
        /** @var Module $module */
        $module = $modules[$moduleId];
        $moduleInstaller->activate($module);
    }
}
