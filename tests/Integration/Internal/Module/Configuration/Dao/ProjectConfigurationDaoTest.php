<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Configuration\Dao;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDao;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ProjectConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ProjectConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ShopConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;

/**
 * @internal
 */
class ProjectConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Configuration\Exception\ProjectConfigurationIsEmptyException
     */
    public function testProjectConfigurationGetterThrowsExceptionIfStorageIsEmpty(): void
    {
        $vfsStreamDirectory = vfsStream::setup();
        vfsStream::create([], $vfsStreamDirectory);

        $context = $this
            ->getMockBuilder(BasicContextInterface::class)
            ->getMock();

        $context
            ->method('getProjectConfigurationDirectory')
            ->willReturn(vfsStream::url('root'));

        $projectConfigurationDao = new ProjectConfigurationDao(
            $this->getMockBuilder(ShopConfigurationDaoInterface::class)->getMock(),
            $context,
            $this->get('oxid_esales.symfony.file_system')
        );

        $projectConfigurationDao->getConfiguration();
    }

    public function testConfigurationIsEmptyIfNoEnvironment(): void
    {
        $vfsStreamDirectory = vfsStream::setup();
        vfsStream::create([], $vfsStreamDirectory);

        $context = $this
            ->getMockBuilder(BasicContextInterface::class)
            ->getMock();

        $context
            ->method('getProjectConfigurationDirectory')
            ->willReturn(vfsStream::url('root'));

        $projectConfigurationDao = new ProjectConfigurationDao(
            $this->getMockBuilder(ShopConfigurationDaoInterface::class)->getMock(),
            $context,
            $this->get('oxid_esales.symfony.file_system')
        );

        $this->assertTrue($projectConfigurationDao->isConfigurationEmpty());
    }

    public function testConfigurationIsEmptyIfDirectoryDoesNotExist(): void
    {
        $vfsStreamDirectory = vfsStream::setup();
        vfsStream::create([], $vfsStreamDirectory);

        $context = $this
            ->getMockBuilder(BasicContextInterface::class)
            ->getMock();

        $context
            ->method('getProjectConfigurationDirectory')
            ->willReturn(vfsStream::url('root') . '/nonExistent');

        $projectConfigurationDao = new ProjectConfigurationDao(
            $this->getMockBuilder(ShopConfigurationDaoInterface::class)->getMock(),
            $context,
            $this->get('oxid_esales.symfony.file_system')
        );

        $this->assertTrue($projectConfigurationDao->isConfigurationEmpty());
    }

    public function testConfigurationNotIsEmptyIfAtLeastOneEnvironmentPresents(): void
    {
        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration('dev', new EnvironmentConfiguration());

        $projectConfigurationDao = $this
            ->getContainer()
            ->get(ProjectConfigurationDaoInterface::class);

        $projectConfigurationDao->save($projectConfiguration);

        $this->assertEquals(
            $projectConfiguration,
            $projectConfigurationDao->getConfiguration()
        );

        $this->assertFalse($projectConfigurationDao->isConfigurationEmpty());
    }

    public function testSaveEmptyEnvironment(): void
    {
        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration('someEnvironment', new EnvironmentConfiguration());
        $projectConfiguration->addEnvironmentConfiguration('andAnotherEnvironment', new EnvironmentConfiguration());

        $projectConfigurationDao = $this
            ->getContainer()
            ->get(ProjectConfigurationDaoInterface::class);

        $projectConfigurationDao->save($projectConfiguration);

        $this->assertEquals(
            $projectConfiguration,
            $projectConfigurationDao->getConfiguration()
        );
    }

    public function testDeleteEnvironment(): void
    {
        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration('toDelete', new EnvironmentConfiguration());
        $projectConfiguration->addEnvironmentConfiguration('dev', new EnvironmentConfiguration());

        $projectConfigurationDao = $this
            ->getContainer()
            ->get(ProjectConfigurationDaoInterface::class);

        $projectConfigurationDao->save($projectConfiguration);

        $projectConfiguration->deleteEnvironmentConfiguration('toDelete');

        $projectConfigurationDao->save($projectConfiguration);

        $this->assertEquals(
            $projectConfiguration,
            $projectConfigurationDao->getConfiguration()
        );
    }

    public function testProjectConfigurationSaving(): void
    {
        $projectConfigurationDao = $this
            ->getContainer()
            ->get(ProjectConfigurationDaoInterface::class);

        $projectConfiguration = $this->getTestProjectConfiguration();

        $projectConfigurationDao->save($projectConfiguration);

        $this->assertEquals(
            $projectConfiguration,
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

        $classExtensionChain = new ClassExtensionsChain();
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
        $shopConfiguration->setClassExtensionsChain($classExtensionChain);

        $devEnvironmentConfiguration = new EnvironmentConfiguration();
        $devEnvironmentConfiguration->addShopConfiguration(1, $shopConfiguration);
        $devEnvironmentConfiguration->addShopConfiguration(2, $shopConfiguration);

        $prodEnvironmentConfiguration = new EnvironmentConfiguration();
        $prodEnvironmentConfiguration->addShopConfiguration(1, $shopConfiguration);
        $prodEnvironmentConfiguration->addShopConfiguration(3, new ShopConfiguration());

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration('dev', $devEnvironmentConfiguration);
        $projectConfiguration->addEnvironmentConfiguration('prod', $prodEnvironmentConfiguration);

        return $projectConfiguration;
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
