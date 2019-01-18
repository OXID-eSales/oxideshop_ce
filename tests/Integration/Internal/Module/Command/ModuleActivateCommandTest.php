<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Command;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Module\Command\ModuleActivateCommand;
use Symfony\Component\Console\Input\ArrayInput;

class ModuleActivateCommandTest extends ModuleCommandsTestCase
{
    public function testModuleActivation()
    {
        $this->prepareTestData();

        $moduleId = 'testmodule';
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
        $this->prepareTestData();

        $moduleId = 'testmodule';
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
