<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Command\ModuleDeactivateCommand;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;

final class ModuleDeactivateCommandTest extends ModuleCommandsTestCase
{
    private string $commandName = 'oe:module:deactivate';

    public function testModuleDeactivation(): void
    {
        $this->installTestModule();
        $this->get(ModuleActivationBridgeInterface::class)->activate($this->moduleId, 1);

        $consoleOutput = $this->executeCommand($this->commandName, ['module-id' => $this->moduleId]);

        $this->assertSame(
            sprintf(ModuleDeactivateCommand::MESSAGE_MODULE_DEACTIVATED, $this->moduleId) . PHP_EOL,
            $consoleOutput
        );

        $module = oxNew(Module::class);
        $module->load($this->moduleId);
        $this->assertFalse($module->isActive());

        $this->cleanupTestData();
    }

    public function testNonExistingModuleActivation(): void
    {
        $moduleId = 'test';
        $consoleOutput = $this->executeCommand($this->commandName, ['module-id' => $moduleId]);

        $this->assertSame(
            sprintf(ModuleDeactivateCommand::MESSAGE_MODULE_NOT_FOUND, $moduleId) . PHP_EOL,
            $consoleOutput
        );
    }
}
