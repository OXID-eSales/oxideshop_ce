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
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleActivationServiceTest extends TestCase
{
    use ContainerTrait;

    /** @var vfsStreamDirectory */
    private $vfsStreamDirectory = null;

    public function testActivation()
    {
        $this->createModuleStructure();
        $container = $this->setupAndConfigureContainer();

        $projectConfigurationDao = $container->get(ProjectConfigurationDaoInterface::class);
        $projectConfigurationDao->persistConfiguration($this->getTestProjectConfiguration());

        $moduleActivationService = $container->get(ModuleActivationServiceInterface::class);
        $moduleActivationService->activate('testModuleConfiguration', 1);
    }

    /**
     * We need to replace services in the container with a mock
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function setupAndConfigureContainer()
    {
        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->getContainer();

        $defintion = $container->getDefinition('oxid_esales.module.setup.validator.smarty_plugin_directories_module_setting_validator');

        $shopAdapter = $this->getShopAdapterMock();

        $defintion->setArguments([$shopAdapter]);
        $container->setDefinition('oxid_esales.module.setup.validator.smarty_plugin_directories_module_setting_validator', $defintion);

        $this->setContainerDefinitionToPublic($container, ProjectConfigurationDaoInterface::class);
        $this->setContainerDefinitionToPublic($container, ModuleActivationServiceInterface::class);

        $container->compile();

        return $container;
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
            ->willReturn(vfsStream::url('root/modules/smartyTestModule'));

        return $shopAdapter;
    }

    private function getTestProjectConfiguration(): ProjectConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModuleConfiguration')
            ->setState('active');

        $moduleConfiguration->addSetting(
            new ModuleSetting('path', 'somePath')
        )
        ->addSetting(
            new ModuleSetting('version', 'v2.1')
        )
        ->addSetting(new ModuleSetting(
            'controllers',
            [
                'originalClassNamespace' => 'moduleClassNamespace',
                'otherOriginalClassNamespace' => 'moduleClassNamespace',
            ]
        ))
        ->addSetting(new ModuleSetting(
            'templates',
            [
                'originalTemplate' => 'moduleTemplate',
                'otherOriginalTemplate' => 'moduleTemplate',
            ]
        ))
        ->addSetting(new ModuleSetting(
            'smartyPluginDirectories',
            [
                'firstSmartyDirectory',
                'secondSmartyDirectory',
            ]
        ))
        ->addSetting(new ModuleSetting(
            'blocks',
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
            'extend',
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
        /**
        ->setSetting(new ModuleSetting(
            'events',
            [
                'onActivate' => 'ModuleClass::onActivate',
                'onDeactivate' => 'ModuleClass::onDeactivate',
            ]
        ))
         */;

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration(1, $shopConfiguration);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration('dev', $environmentConfiguration);

        return $projectConfiguration;
    }

    private function createModuleStructure()
    {
        $structure = [
            'modules' => [
                'smartyTestModule' => [
                    'firstSmartyDirectory' => [
                        'smartyPlugin.php' => '*this is the first test smarty plugin*'
                    ],
                    'secondSmartyDirectory' => [
                        'smartyPlugin.php' => '*this is the second test smarty plugin*'
                    ],
                ]
            ]
        ];

        if (!$this->vfsStreamDirectory) {
            $this->vfsStreamDirectory = vfsStream::setup('root', null, $structure);
        }
    }
}
