<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Setup;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\TestData\TestModule\ModuleEvents;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
class ModuleActivationServiceTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp()
    {
        $this->container = $this->setupAndConfigureContainer();

        $projectConfigurationDao = $this->container->get(ProjectConfigurationDaoInterface::class);
        $projectConfigurationDao->persistConfiguration($this->getTestProjectConfiguration());

        parent::setUp();
    }

    public function testActivation()
    {
        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);

        $moduleActivationService->activate('testModuleConfiguration', 1);

        $this->assertTrue(
            $this->container->get(ModuleStateServiceInterface::class)->isActive('testModuleConfiguration', 1)
        );

        $moduleActivationService->deactivate('testModuleConfiguration', 1);

        $this->assertFalse(
            $this->container->get(ModuleStateServiceInterface::class)->isActive('testModuleConfiguration', 1)
        );
    }

    public function testActivationEventWasExecuted()
    {
        /** @var ModuleActivationServiceInterface $moduleActivationService */
        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);

        ob_start();
        $moduleActivationService->activate('testModuleConfiguration', 1);
        $eventMessage = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Method onActivate was called', $eventMessage);
    }

    public function testDeactivationEventWasExecuted()
    {
        /** @var ModuleActivationServiceInterface $moduleActivationService */
        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);

        ob_start();
        $moduleActivationService->deactivate('testModuleConfiguration', 1);
        $eventMessage = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Method onDeactivate was called', $eventMessage);
    }

    /**
     * @return ShopAdapterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getShopAdapterMock()
    {
        $shopAdapter = $this
            ->getMockBuilder(ShopAdapterInterface::class)
            ->getMock();

        $shopAdapter
            ->method('getModuleFullPath')
            ->willReturn(__DIR__ . '/../TestData/TestModule');

        return $shopAdapter;
    }

    private function getTestProjectConfiguration(): ProjectConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModuleConfiguration');

        $moduleConfiguration->addSetting(
            new ModuleSetting(ModuleSetting::PATH, 'somePath')
        )
        ->addSetting(
            new ModuleSetting('version', 'v2.1')
        )
        ->addSetting(new ModuleSetting(
            ModuleSetting::CONTROLLERS,
            [
                'originalClassNamespace' => 'moduleClassNamespace',
                'otherOriginalClassNamespace' => 'moduleClassNamespace',
            ]
        ))
        ->addSetting(new ModuleSetting(
            ModuleSetting::TEMPLATES,
            [
                'originalTemplate' => 'moduleTemplate',
                'otherOriginalTemplate' => 'moduleTemplate',
            ]
        ))
        ->addSetting(new ModuleSetting(
            ModuleSetting::SMARTY_PLUGIN_DIRECTORIES,
            [
                'SmartyPlugins/directory1',
                'SmartyPlugins/directory2',
            ]
        ))
        ->addSetting(new ModuleSetting(
            ModuleSetting::TEMPLATE_BLOCKS,
            [
                [
                    'block'     => 'testBlock',
                    'position'  => '3',
                    'theme'     => 'flow_theme',
                    'template'  => 'extendedTemplatePath',
                    'file'      => 'filePath',
                ],
            ]
        ))
        ->addSetting(new ModuleSetting(
            ModuleSetting::CLASS_EXTENSIONS,
            [
                'originalClassNamespace' => 'moduleClassNamespace',
                'otherOriginalClassNamespace' => 'moduleClassNamespace',
            ]
        ))
        ->addSetting(new ModuleSetting(
            ModuleSetting::SHOP_MODULE_SETTING,
            [
                [
                    'group' => 'frontend',
                    'name'  => 'sGridRow',
                    'type'  => 'str',
                    'value' => 'row',
                ],
            ]
        ))
        ->addSetting(new ModuleSetting(
            ModuleSetting::EVENTS,
            [
                'onActivate'    => ModuleEvents::class . '::onActivate',
                'onDeactivate'  => ModuleEvents::class . '::onDeactivate'
            ]
        ));

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration(1, $shopConfiguration);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration('prod', $environmentConfiguration);

        return $projectConfiguration;
    }

    /**
     * We need to replace services in the container with a mock
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function setupAndConfigureContainer()
    {
        $containerBuilder = new ContainerBuilder(new Facts());
        $container = $containerBuilder->getContainer();

        $smartyPluginDirectoriesValidatordefinition = $container->getDefinition(
            'oxid_esales.module.setup.validator.smarty_plugin_directories_module_setting_validator'
        );

        $shopAdapter = $this->getShopAdapterMock();
        $smartyPluginDirectoriesValidatordefinition->setArguments([$shopAdapter]);
        $container->setDefinition(
            'oxid_esales.module.setup.validator.smarty_plugin_directories_module_setting_validator',
            $smartyPluginDirectoriesValidatordefinition
        );

        $projectConfigurationYmlStorageDefinition = $container->getDefinition('oxid_esales.module.configuration.project_configuration_yaml_file_storage');
        $projectConfigurationYmlStorageDefinition->setArgument(
            '$filePath',
            tempnam(sys_get_temp_dir() . '/test_project_configuration', 'test_')
        );
        $container->setDefinition(
            'oxid_esales.module.configuration.project_configuration_yaml_file_storage',
            $projectConfigurationYmlStorageDefinition
        );

        $this->setContainerDefinitionToPublic($container, ProjectConfigurationDaoInterface::class);
        $this->setContainerDefinitionToPublic($container, ModuleActivationServiceInterface::class);

        $container->compile();

        return $container;
    }
}
