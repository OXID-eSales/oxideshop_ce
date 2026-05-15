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

final class ModuleDeactivateCommandTest extends ModuleCommandsTestCase
{
    private string $commandName = 'oe:module:deactivate';

    public function testModuleDeactivation(): void
    {
        $this->installTestModule();
        $this->get(ModuleActivationBridgeInterface::class)->activate($this->moduleId, 1);

        $exitCode = $this->executeCommand($this->commandName, ['module-id' => $this->moduleId]);

        $this->assertSame(Command::SUCCESS, $exitCode);

        $module = oxNew(Module::class);
        $module->load($this->moduleId);
        $this->assertFalse($module->isActive());
    }

    public function testNonExistingModuleDeactivation(): void
    {
        $moduleId = 'test';

        $exitCode = $this->executeCommand($this->commandName, ['module-id' => $moduleId]);

        $this->assertSame(Command::FAILURE, $exitCode);
    }
}