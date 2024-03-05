<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Command\ModuleActivateCommand;

final class ModuleActivateCommandTest extends ModuleCommandsTestCase
{
    private string $commandName = 'oe:module:activate';

    public function testModuleActivation(): void
    {
        $this->installTestModule();

        $consoleOutput = $this->executeCommand($this->commandName, ['module-id' => $this->moduleId]);

        $this->assertSame(
            sprintf(ModuleActivateCommand::MESSAGE_MODULE_ACTIVATED, $this->moduleId) . PHP_EOL,
            $consoleOutput
        );

        $module = oxNew(Module::class);
        $module->load($this->moduleId);
        $this->assertTrue($module->isActive());

        $this->cleanupTestData();
    }

    public function testNonExistingModuleActivation(): void
    {
        $moduleId = 'test';
        $consoleOutput = $this->executeCommand($this->commandName, ['module-id' => $moduleId]);

        $this->assertSame(
            sprintf(ModuleActivateCommand::MESSAGE_MODULE_NOT_FOUND, $moduleId) . PHP_EOL,
            $consoleOutput
        );
    }
}
