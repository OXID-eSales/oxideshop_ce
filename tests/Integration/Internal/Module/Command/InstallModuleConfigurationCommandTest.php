<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Command;

use OxidEsales\EshopCommunity\Internal\Module\Command\InstallModuleConfigurationCommand;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleFilesInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
class InstallModuleConfigurationCommandTest extends ModuleCommandsTestCase
{
    private $shopId;
    private $environment;
    private $moduleId = 'testmodule';
    private $moduleTargetPath = 'testmodule';
    private $workingDirectoryBackup;
    private $workingDirectory;

    public function setUp()
    {
        $context = $this->get(ContextInterface::class);
        $this->shopId = $context->getCurrentShopId();
        $this->environment = $context->getEnvironment();
        $this->workingDirectoryBackup = getcwd();
        $this->setWorkingDirectoryForConsole(__DIR__);

        parent::setUp();
    }

    public function tearDown()
    {
        $this->setWorkingDirectoryForConsole($this->workingDirectoryBackup);
        parent::tearDown();
    }

    public function testInstallFromModulesDirectoryWithAbsoluteSourcePath()
    {
        $this->installTestModuleFiles();

        $context = $this->get(ContextInterface::class);
        $consoleOutput = $this->executeModuleInstallCommand($context->getModulesPath() . '/' . $this->moduleTargetPath);

        $this->assertContains(InstallModuleConfigurationCommand::MESSAGE_INSTALLATION_WAS_SUCCESSFUL, $consoleOutput);

        $moduleConfiguration = $this->get(ModuleConfigurationDaoInterface::class)->get($this->moduleId, $this->shopId);
        $this->assertSame(
            $this->moduleId,
            $moduleConfiguration->getId()
        );
    }

    public function testInstallFromModulesDirectoryWithRelativeSourcePath()
    {
        $this->installTestModuleFiles();

        $context = $this->get(ContextInterface::class);

        $relativeModulePath = Path::makeRelative(
            $context->getModulesPath() . '/' . $this->moduleTargetPath,
            $this->workingDirectory
        );

        $this->assertContains(
            InstallModuleConfigurationCommand::MESSAGE_INSTALLATION_WAS_SUCCESSFUL,
            $this->executeModuleInstallCommand($relativeModulePath)
        );

        $moduleConfiguration = $this->get(ModuleConfigurationDaoInterface::class)->get($this->moduleId, $this->shopId);
        $this->assertSame(
            $this->moduleId,
            $moduleConfiguration->getId()
        );
    }

    public function testInstallFromNotModulesDirectoryWithProvidedAbsoluteTargetPath()
    {
        $context = $this->get(ContextInterface::class);

        $consoleOutput = $this->executeModuleInstallCommand(
            $this->getTestModuleSourcePath(),
            $context->getModulesPath() . '/' . $this->moduleTargetPath
        );

        $this->assertContains(InstallModuleConfigurationCommand::MESSAGE_INSTALLATION_WAS_SUCCESSFUL, $consoleOutput);

        $moduleConfiguration = $this->get(ModuleConfigurationDaoInterface::class)->get($this->moduleId, $this->shopId);
        $this->assertSame(
            $this->moduleTargetPath,
            $moduleConfiguration->getPath()
        );
    }

    public function testInstallFromNotModulesDirectoryWithProvidedRelativeTargetPath()
    {
        $context = $this->get(ContextInterface::class);

        $relativeModulePath = Path::makeRelative(
            $context->getModulesPath() . '/' . $this->moduleTargetPath,
            $this->workingDirectory
        );

        $consoleOutput = $this->executeModuleInstallCommand(
            $this->getTestModuleSourcePath(),
            $relativeModulePath
        );

        $this->assertContains(InstallModuleConfigurationCommand::MESSAGE_INSTALLATION_WAS_SUCCESSFUL, $consoleOutput);

        $moduleConfiguration = $this->get(ModuleConfigurationDaoInterface::class)->get($this->moduleId, $this->shopId);
        $this->assertSame(
            $this->moduleTargetPath,
            $moduleConfiguration->getPath()
        );
    }

    public function testInstallFromNotModulesDirectoryWithoutProvidedTargetPath()
    {
        $consoleOutput = $this->executeModuleInstallCommand($this->getTestModuleSourcePath());

        $this->assertContains(InstallModuleConfigurationCommand::MESSAGE_TARGET_PATH_IS_REQUIRED, $consoleOutput);
    }

    public function testInstallWithWrongModuleSourcePath()
    {
        $consoleOutput = $this->executeModuleInstallCommand('fakePath');

        $this->assertContains(InstallModuleConfigurationCommand::MESSAGE_INSTALLATION_FAILED, $consoleOutput);
    }

    public function testInstallWithWrongModuleTargetPath()
    {
        $consoleOutput = $this->executeModuleInstallCommand($this->getTestModuleSourcePath(), 'fakePath');

        $this->assertContains(InstallModuleConfigurationCommand::MESSAGE_INSTALLATION_FAILED, $consoleOutput);
    }

    private function executeModuleInstallCommand(string $moduleSourcePath, string $moduleTargetPath = null): string
    {
        $input = [
            'command' => 'oe:module:install-configuration',
            'module-source-path' => $moduleSourcePath,
        ];

        if ($moduleTargetPath) {
            $input['module-target-path'] = $moduleTargetPath;
        }

        $app = $this->getApplication();

        return $this->execute(
            $app,
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput($input)
        );
    }

    private function installTestModuleFiles()
    {
        $this->get(ModuleFilesInstallerInterface::class)->install(
            new OxidEshopPackage($this->moduleId, $this->getTestModuleSourcePath())
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
