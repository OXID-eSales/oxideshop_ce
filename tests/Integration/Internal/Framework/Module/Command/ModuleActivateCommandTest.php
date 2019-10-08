<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Command\ModuleActivateCommand;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use Symfony\Component\Console\Input\ArrayInput;

final class ModuleActivateCommandTest extends ModuleCommandsTestCase
{
    public function testModuleActivation(): void
    {
        $this->installTestModule();

        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput(['command' => 'oe:module:activate', 'module-id' => $this->moduleId])
        );

        $this->assertSame(
            sprintf(ModuleActivateCommand::MESSAGE_MODULE_ACTIVATED, $this->moduleId) . PHP_EOL,
            $consoleOutput
        );

        $module = oxNew(Module::class);
        $module->load($this->moduleId);
        $this->assertTrue($module->isActive());

        $this->cleanupTestData();
    }

    public function testWhenModuleAlreadyActive(): void
    {
        $this->installTestModule();

        $this->get(ModuleActivationBridgeInterface::class)->activate($this->moduleId, 1);

        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput(['command' => 'oe:module:activate', 'module-id' => $this->moduleId])
        );

        $this->assertSame(
            sprintf(ModuleActivateCommand::MESSAGE_MODULE_ALREADY_ACTIVE, $this->moduleId) . PHP_EOL,
            $consoleOutput
        );

        $this->cleanupTestData();
    }

    public function testNonExistingModuleActivation(): void
    {
        $moduleId = 'test';
        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput(['command' => 'oe:module:activate', 'module-id' => $moduleId])
        );

        $this->assertSame(
            sprintf(ModuleActivateCommand::MESSAGE_MODULE_NOT_FOUND, $moduleId) . PHP_EOL,
            $consoleOutput
        );
    }
}
