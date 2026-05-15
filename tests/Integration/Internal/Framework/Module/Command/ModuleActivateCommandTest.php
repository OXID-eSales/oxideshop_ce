<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use Symfony\Component\Console\Command\Command;

final class ModuleActivateCommandTest extends ModuleCommandsTestCase
{
    private string $commandName = 'oe:module:activate';

    public function testModuleActivation(): void
    {
        $this->installTestModule();

        $exitCode = $this->executeCommand($this->commandName, ['module-id' => $this->moduleId]);

        $this->assertSame(Command::SUCCESS, $exitCode);

        $module = oxNew(Module::class);
        $module->load($this->moduleId);
        $this->assertTrue($module->isActive());
    }

    public function testAlreadyActiveModuleActivation(): void
    {
        $this->installTestModule();
        $this->get(ModuleActivationBridgeInterface::class)->activate($this->moduleId, 1);

        $exitCode = $this->executeCommand($this->commandName, ['module-id' => $this->moduleId]);

        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    public function testNonExistingModuleActivation(): void
    {
        $moduleId = 'test';

        $exitCode = $this->executeCommand($this->commandName, ['module-id' => $moduleId]);

        $this->assertSame(Command::FAILURE, $exitCode);
    }
}