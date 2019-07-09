<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Command;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Module\Command\ModuleActivateCommand;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use Symfony\Component\Console\Input\ArrayInput;

class ModuleActivateCommandTest extends ModuleCommandsTestCase
{
    public function testModuleActivation()
    {
        $moduleId = 'testmodule';
        $this->installModule($moduleId);

        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput(['command' => 'oe:module:activate', 'module-id' => $moduleId])
        );

        $this->assertSame(sprintf(ModuleActivateCommand::MESSAGE_MODULE_ACTIVATED, $moduleId) . PHP_EOL, $consoleOutput);

        $module = oxNew(Module::class);
        $module->load($moduleId);
        $this->assertTrue($module->isActive());

        $this->cleanupTestData();
    }

    public function testWhenModuleAlreadyActive()
    {
        $moduleId = 'testmodule';
        $this->installModule($moduleId);

        $this->get(ModuleActivationBridgeInterface::class)->activate($moduleId, 1);

        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput(['command' => 'oe:module:activate', 'module-id' => $moduleId])
        );

        $this->assertSame(sprintf(ModuleActivateCommand::MESSAGE_MODULE_ALREADY_ACTIVE, $moduleId) . PHP_EOL, $consoleOutput);

        $this->cleanupTestData();
    }

    public function testNonExistingModuleActivation()
    {
        $moduleId = 'test';
        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput(['command' => 'oe:module:activate', 'module-id' => $moduleId])
        );

        $this->assertSame(sprintf(ModuleActivateCommand::MESSAGE_MODULE_NOT_FOUND, $moduleId) . PHP_EOL, $consoleOutput);
    }
}
