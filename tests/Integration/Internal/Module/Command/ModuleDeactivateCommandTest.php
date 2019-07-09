<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Command;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Module\Command\ModuleDeactivateCommand;
use Symfony\Component\Console\Input\ArrayInput;

class ModuleDeactivateCommandTest extends ModuleCommandsTestCase
{
    public function testModuleDeactivation()
    {
        $moduleId = 'testmodule';
        $this->installModule($moduleId);
        $this->activateModule($moduleId);

        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
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
        $this->installModule($moduleId);

        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
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
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput(['command' => 'oe:module:deactivate', 'module-id' => $moduleId])
        );

        $this->assertSame(sprintf(ModuleDeactivateCommand::MESSAGE_MODULE_NOT_FOUND, $moduleId) . PHP_EOL, $consoleOutput);
    }
}
