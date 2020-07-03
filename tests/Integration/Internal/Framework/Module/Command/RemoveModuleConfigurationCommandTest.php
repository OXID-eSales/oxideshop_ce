<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Command\RemoveModuleConfigurationCommand;
use Symfony\Component\Console\Input\ArrayInput;

final class RemoveModuleConfigurationCommandTest extends ModuleCommandsTestCase
{
    public function tearDown(): void
    {
        $this->cleanupTestData();
    }
    
    public function testRemoveModuleConfig(): void
    {
        $this->installTestModule();

        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput(['command' => 'oe:module:remove-configuration', 'module-id' => $this->moduleId])
        );

        $this->assertSame(
            sprintf(RemoveModuleConfigurationCommand::MESSAGE_REMOVE_WAS_SUCCESSFULL, $this->moduleId) . PHP_EOL,
            $consoleOutput
        );
    }

    public function testRemoveModuleConfigWithFakeId(): void
    {
        $this->installTestModule();

        $consoleOutput = $this->execute(
            $this->getApplication(),
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput(['command' => 'oe:module:remove-configuration', 'module-id' => 'whatsThis'])
        );

        $this->assertStringStartsWith(
            sprintf(RemoveModuleConfigurationCommand::MESSAGE_REMOVE_FAILED, 'whatsThis') . PHP_EOL,
            $consoleOutput
        );
    }
}
