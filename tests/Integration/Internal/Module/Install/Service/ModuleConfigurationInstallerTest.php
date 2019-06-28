<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationInstallerTest extends TestCase
{
    use ContainerTrait;

    private $modulePath;
    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    public function setUp()
    {
        $this->modulePath = realpath(__DIR__ . '/../../TestData/TestModule/');

        $this->projectConfigurationDao = $this->get(ProjectConfigurationDaoInterface::class);

        $this->prepareTestProjectConfiguration();

        parent::setUp();
    }

    public function testInstall()
    {
        $configurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);
        $configurationInstaller->install($this->modulePath, 'targetPath');

        $this->assertProjectConfigurationHasModuleConfigurationForAllShops();
    }

    public function testIsInstalled()
    {
        $moduleConfigurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);

        $this->assertFalse(
            $moduleConfigurationInstaller->isInstalled($this->modulePath)
        );

        $moduleConfigurationInstaller->install($this->modulePath, 'targetPath');

        $this->assertTrue(
            $moduleConfigurationInstaller->isInstalled($this->modulePath)
        );
    }

    public function testModuleTargetPathIsSetToModuleConfigurations()
    {
        $moduleConfigurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);
        $moduleConfigurationInstaller->install($this->modulePath, 'myModules/TestModule');

        $shopConfiguration = $this
            ->projectConfigurationDao
            ->getConfiguration()
            ->getEnvironmentConfiguration($this->getEnvironment())
            ->getShopConfiguration(1);

        $this->assertSame(
            'myModules/TestModule',
            $shopConfiguration->getModuleConfiguration('testModule')->getPath()
        );
    }

    public function testModuleTargetPathIsSetToModuleConfigurationsIfAbsolutePathGiven()
    {
        $modulesPath = $this->get(ContextInterface::class)->getModulesPath();

        $moduleConfigurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);
        $moduleConfigurationInstaller->install($this->modulePath, $modulesPath . '/myModules/TestModule');

        $shopConfiguration = $this
            ->projectConfigurationDao
            ->getConfiguration()
            ->getEnvironmentConfiguration($this->getEnvironment())
            ->getShopConfiguration(1);

        $this->assertSame(
            'myModules/TestModule',
            $shopConfiguration->getModuleConfiguration('testModule')->getPath()
        );
    }

    private function assertProjectConfigurationHasModuleConfigurationForAllShops()
    {
        $environmentConfiguration = $this
            ->projectConfigurationDao
            ->getConfiguration()
            ->getEnvironmentConfiguration($this->getEnvironment());

        foreach ($environmentConfiguration->getShopConfigurations() as $shopConfiguration) {
            $this->assertContains(
                'testModule',
                $shopConfiguration->getModuleIdsOfModuleConfigurations()
            );
        }
    }

    private function prepareTestProjectConfiguration()
    {
        $shopConfigurationWithChain = new ShopConfiguration();

        $chain = new ClassExtensionsChain();
        $chain->setChain([
            'shopClass'             => ['alreadyInstalledShopClass', 'anotherAlreadyInstalledShopClass'],
            'someAnotherShopClass'  => ['alreadyInstalledShopClass'],
        ]);

        $shopConfigurationWithChain->setClassExtensionsChain($chain);

        $shopConfigurationWithoutChain = new ShopConfiguration();

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration(1, $shopConfigurationWithChain);
        $environmentConfiguration->addShopConfiguration(2, $shopConfigurationWithoutChain);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration($this->getEnvironment(), $environmentConfiguration);

        $this->projectConfigurationDao->save($projectConfiguration);
    }

    private function getEnvironment(): string
    {
        return $this->get(ContextInterface::class)->getEnvironment();
    }
}
