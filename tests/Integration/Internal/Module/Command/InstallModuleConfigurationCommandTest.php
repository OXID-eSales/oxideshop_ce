<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Command;

use OxidEsales\EshopCommunity\Internal\Module\Command\InstallModuleConfigurationCommand;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * @internal
 */
class InstallModuleConfigurationCommandTest extends ModuleCommandsTestCase
{
    private $shopId;
    private $environment;
    private $moduleId = 'testmodule';

    /**
     * ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    public function setUp()
    {
        $context = $this->get(ContextInterface::class);
        $this->shopId = $context->getCurrentShopId();
        $this->environment = $context->getEnvironment();

        $this->projectConfigurationDao = $this->get(ProjectConfigurationDaoInterface::class);
        $this->createTestProjectConfiguration();

        parent::setUp();
    }

    public function testInstall()
    {
        $app = $this->getApplication();

        $consoleOutput = $this->execute(
            $app,
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput([
                'command' => 'oe:module:install-configuration',
                'module-source-path' => __DIR__ . '/Fixtures/modules/testmodule',
                'module-target-path' => 'testmodule',
            ])
        );

        $this->assertContains(InstallModuleConfigurationCommand::MESSAGE_INSTALLATION_WAS_SUCCESSFUL, $consoleOutput);

        $this->assertInstanceOf(
            ModuleConfiguration::class,
            $this->get(ModuleConfigurationDaoInterface::class)->get($this->moduleId, $this->shopId)
        );
    }

    public function testInstallWithWrongModulePath()
    {
        $app = $this->getApplication();

        $consoleOutput = $this->execute(
            $app,
            $this->get('oxid_esales.console.commands_provider.services_commands_provider'),
            new ArrayInput([
                'command' => 'oe:module:install-configuration',
                'module-source-path' => 'fakePath',
                'module-target-path' => 'testmodule',
            ])
        );

        $this->assertContains(InstallModuleConfigurationCommand::MESSAGE_INSTALLATION_FAILED, $consoleOutput);
    }

    private function createTestProjectConfiguration()
    {
        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration($this->shopId, new ShopConfiguration());

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration($this->environment, $environmentConfiguration);

        $this->projectConfigurationDao->persistConfiguration($projectConfiguration);
    }
}
