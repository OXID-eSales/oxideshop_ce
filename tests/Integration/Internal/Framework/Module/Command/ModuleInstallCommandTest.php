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

/** @internal */
final class ModuleInstallCommandTest extends TestCase
{
    use ContainerTrait;

    private string $moduleId = 'testmodule';
    private $workingDirectoryBackup;
    private $workingDirectory;

    protected function setup(): void
    {
        parent::setUp();
        $this->assertFalse($this->isTestModuleInstalled());
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
        parent::tearDown();
    }

    public function testInstallWithAbsolutePath(): void
    {
        $consoleOutput = $this->executeModuleInstallCommand($this->getTestModulePath());

        $this->assertSame(Command::SUCCESS, $consoleOutput);
        $this->assertTrue($this->isTestModuleInstalled());
    }

    public function testInstallWithRelativePath(): void
    {
        $relativeModulePath = Path::makeRelative($this->getTestModulePath(), getcwd());

        $consoleOutput = $this->executeModuleInstallCommand($relativeModulePath);

        $this->assertSame(Command::SUCCESS, $consoleOutput);
        $this->assertTrue($this->isTestModuleInstalled());
    }

    public function testInstallWithWrongModulePath(): void
    {
        $consoleOutput = $this->executeModuleInstallCommand('wrong-path');

        $this->assertSame(Command::FAILURE, $consoleOutput);
    }

    private function executeModuleInstallCommand(string $moduleSourcePath): int
    {
        return (new CommandTester($this->get('console.command_loader')
            ->get('oe:module:install')))
            ->execute(['module-path' => $moduleSourcePath]);
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

    private function cleanupTestData(): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->uninstall($this->getTestPackage());
    }
}
