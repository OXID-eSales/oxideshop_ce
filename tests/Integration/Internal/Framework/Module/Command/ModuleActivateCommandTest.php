<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Command\ModuleActivateCommand;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use Symfony\Component\Console\Command\Command;

final class ModuleActivateCommandTest extends ModuleCommandsTestCase
{
    private $commandName = 'oe:module:activate';

    public function testModuleActivation(): void
    {
        $this->installTestModule();

        $commandTester = $this->executeCommand($this->commandName, ['module-id' => $this->moduleId]);

        $this->assertStringContainsString(
            sprintf(ModuleActivateCommand::MESSAGE_MODULE_ACTIVATED, $this->moduleId),
            $commandTester->getDisplay()
        );
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());

        $module = oxNew(Module::class);
        $module->load($this->moduleId);
        $this->assertTrue($module->isActive());

        $this->cleanupTestData();
    }

    public function testNonExistingModuleActivation(): void
    {
        $moduleId = 'test';
        $commandTester = $this->executeCommand($this->commandName, ['module-id' => $moduleId]);

        $this->assertStringContainsString(
            sprintf(ModuleActivateCommand::MESSAGE_MODULE_NOT_FOUND, $moduleId),
            $commandTester->getDisplay()
        );
        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
    }
}
