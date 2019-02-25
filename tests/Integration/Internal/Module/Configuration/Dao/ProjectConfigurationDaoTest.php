<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\Chain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Common\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDao;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ProjectConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ProjectConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ShopConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @internal
 */
class ProjectConfigurationDaoTest extends TestCase
{
    public function testProjectConfigurationSaving()
    {
        $projectConfigurationDao = $this
            ->getContainer()
            ->get(ProjectConfigurationDaoInterface::class);

        $projectConfiguration = $this->getTestProjectConfiguration();

        $projectConfigurationDao->persistConfiguration($projectConfiguration);

        $this->assertEquals(
            $projectConfiguration,
            $projectConfigurationDao->getConfiguration()
        );
    }

    public function testWithCorrectNode()
    {
        $projectConfigurationData = ['environments' => []];

        $projectConfigurationDataMapper = $this->getProjectConfigurationDataMapper();

        $arrayStorage = $this
            ->getMockBuilder(ArrayStorageInterface::class)
            ->getMock();

        $arrayStorage
            ->method('get')
            ->willReturn($projectConfigurationData);

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('projectConfiguration');
        $rootNode
            ->children()
            ->arrayNode('environments')
            ->end()
            ->end();

        $node = $treeBuilder->buildTree();

        $projectConfigurationDao = new ProjectConfigurationDao(
            $arrayStorage,
            $projectConfigurationDataMapper,
            $node
        );

        $this->assertEquals(
            $projectConfigurationDataMapper->fromData($projectConfigurationData),
            $projectConfigurationDao->getConfiguration()
        );
    }

    private function getTestProjectConfiguration(): ProjectConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModuleConfiguration')
            ->setPath('somePath')
            ->setVersion('v2.1')
            ->setDescription([
                'de' => 'ja',
                'en' => 'no',
            ]);

        $moduleConfiguration
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
                    'firstSmartyDirectory',
                    'secondSmartyDirectory',
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
                        'group'         => 'frontend',
                        'name'          => 'sGridRow',
                        'type'          => 'str',
                        'value'         => 'row',
                        'position'      => '2',
                        'constraints'   => ['first', 'second'],
                    ],
                ]
            ))
            ->addSetting(new ModuleSetting(
                ModuleSetting::EVENTS,
                [
                'onActivate' => 'ModuleClass::onActivate',
                'onDeactivate' => 'ModuleClass::onDeactivate',
                ]
            ));

        $classExtensionChain = new Chain();
        $classExtensionChain->setName('classExtensions');
        $classExtensionChain->setChain([
            'shopClassNamespace' => [
                'activeModule2ExtensionClass',
                'activeModuleExtensionClass',
                'notActiveModuleExtensionClass',
            ],
            'anotherShopClassNamespace' => [
                'activeModuleExtensionClass',
                'notActiveModuleExtensionClass',
                'activeModule2ExtensionClass',
            ],
        ]);

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);
        $shopConfiguration->addChain($classExtensionChain);

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration(1, $shopConfiguration);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration('dev', $environmentConfiguration);

        return $projectConfiguration;
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testWithIncorrectNode()
    {
        $projectConfigurationData = [
            'environments' => [],
            'incorrectKey' => [],
        ];

        $projectConfigurationDataMapper = $this->getProjectConfigurationDataMapper();

        $arrayStorage = $this
            ->getMockBuilder(ArrayStorageInterface::class)
            ->getMock();

        $arrayStorage
            ->method('get')
            ->willReturn($projectConfigurationData);

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('projectConfiguration');
        $rootNode
            ->children()
            ->arrayNode('environments')
            ->end()
            ->end();

        $node = $treeBuilder->buildTree();

        $projectConfigurationDao = new ProjectConfigurationDao(
            $arrayStorage,
            $projectConfigurationDataMapper,
            $node
        );

        $this->assertEquals(
            $projectConfigurationDataMapper->fromData($projectConfigurationData),
            $projectConfigurationDao->getConfiguration()
        );
    }

    private function getContainer()
    {
        $container = (new TestContainerFactory())->create();
        $container->compile();

        return $container;
    }

    private function getProjectConfigurationDataMapper(): ProjectConfigurationDataMapperInterface
    {
        $shopConfigurationDataMapper = $this
            ->getMockBuilder(ShopConfigurationDataMapperInterface::class)
            ->getMock();

        $shopConfigurationDataMapper
            ->method('fromData')
            ->willReturn(new ShopConfiguration());

        return new ProjectConfigurationDataMapper($shopConfigurationDataMapper);
    }
}
