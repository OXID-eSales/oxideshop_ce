<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Command\InstallModuleConfigurationCommand;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleFilesInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
class InstallModuleConfigurationCommandTest extends ModuleCommandsTestCase
{
    use ContainerTrait;
    
    private $shopId;
    private $testModuleId = 'testmodule';
    private $workingDirectoryBackup;
    private $workingDirectory;

    public function setup(): void
    {
        $context = $this->get(ContextInterface::class);
        $this->shopId = $context->getCurrentShopId();
        $this->workingDirectoryBackup = getcwd();
        $this->setWorkingDirectoryForConsole(__DIR__);

        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->setWorkingDirectoryForConsole($this->workingDirectoryBackup);
        parent::tearDown();
    }

    public function testInstallFromModulesDirectoryWithAbsoluteSourcePath()
    {
        $this->installTestModuleFiles();

        $consoleOutput = $this->executeModuleInstallCommand($this->getTestModuleSourcePath());

        $this->assertStringContainsString(InstallModuleConfigurationCommand::MESSAGE_INSTALLATION_WAS_SUCCESSFUL, $consoleOutput);

        $moduleConfiguration = $this->get(ModuleConfigurationDaoInterface::class)->get($this->testModuleId, $this->shopId);
        $this->assertSame(
            $this->testModuleId,
            $moduleConfiguration->getId()
        );
    }

    public function testInstallFromModulesDirectoryWithRelativeSourcePath()
    {
        $this->installTestModuleFiles();

        $relativeModulePath = Path::makeRelative(
            $this->getTestModuleSourcePath(),
            $this->workingDirectory
        );

        $this->assertStringContainsString(
            InstallModuleConfigurationCommand::MESSAGE_INSTALLATION_WAS_SUCCESSFUL,
            $this->executeModuleInstallCommand($relativeModulePath)
        );

        $moduleConfiguration = $this->get(ModuleConfigurationDaoInterface::class)->get($this->testModuleId, $this->shopId);
        $this->assertSame(
            $this->testModuleId,
            $moduleConfiguration->getId()
        );
    }

    public function testInstallWithWrongModuleSourcePath()
    {
        $consoleOutput = $this->executeModuleInstallCommand('fakePath');

        $this->assertStringContainsString(InstallModuleConfigurationCommand::MESSAGE_INSTALLATION_FAILED, $consoleOutput);
    }

    private function executeModuleInstallCommand(string $moduleSourcePath): string
    {
        $input = [
            'module-source-path' => $moduleSourcePath,
        ];

        return $this->executeCommand('oe:module:install-configuration', $input);
    }

    private function installTestModuleFiles()
    {
        $this->get(ModuleFilesInstallerInterface::class)->install(
            new OxidEshopPackage($this->getTestModuleSourcePath())
        );
    }

    private function getTestModuleSourcePath(): string
    {
        return __DIR__ . '/Fixtures/modules/testmodule';
    }

    /**
     * @param string $workingDirectory
     */
    private function setWorkingDirectoryForConsole(string $workingDirectory)
    {
        chdir($workingDirectory);
        $this->workingDirectory = $workingDirectory;
    }
}
