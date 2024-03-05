<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Path;

final class ModuleUninstallCommandTest extends TestCase
{
    use ContainerTrait;

    private string $moduleId = 'testmodule';

    protected function setUp(): void
    {
        parent::setUp();
        $this->installTestModule();
        $this->assertTrue($this->isTestModuleInstalled());
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
        parent::tearDown();
    }

    public function testUninstallModule(): void
    {
        $consoleOutput = $this->executeModuleUninstallCommand($this->moduleId);

        $this->assertSame(Command::SUCCESS, $consoleOutput);
        $this->assertFalse($this->isTestModuleInstalled());
    }

    public function testUninstallWithWrongId(): void
    {
        $consoleOutput = $this->executeModuleUninstallCommand('some/wrong-module-id');

        $this->assertSame(Command::FAILURE, $consoleOutput);
    }

    private function installTestModule(): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install($this->getTestPackage());
    }

    private function cleanupTestData(): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->uninstall($this->getTestPackage());
    }

    private function executeModuleUninstallCommand(string $moduleId): int
    {
        return (new CommandTester($this->get('console.command_loader')
            ->get('oe:module:uninstall')))
            ->execute(['module-id' => $moduleId]);
    }

    private function isTestModuleInstalled(): bool
    {
        return $this->get(ModuleInstallerInterface::class)
            ->isInstalled($this->getTestPackage());
    }

    private function getTestPackage(): OxidEshopPackage
    {
        return new OxidEshopPackage($this->getTestModulePath());
    }

    private function getTestModulePath(): string
    {
        return Path::join(__DIR__, 'Fixtures/modules', $this->moduleId);
    }
}
