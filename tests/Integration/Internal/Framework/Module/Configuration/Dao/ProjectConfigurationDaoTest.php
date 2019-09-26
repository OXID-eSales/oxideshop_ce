<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Dao;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Template;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\SmartyPluginDirectory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\TemplateBlock;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDao;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Event;

/**
 * @internal
 */
class ProjectConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ProjectConfigurationIsEmptyException
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
        $templateBlock = new TemplateBlock(
            'extendedTemplatePath',
            'testBlock',
            'filePath'
        );
        $templateBlock->setTheme('flow_theme');
        $templateBlock->setPosition(3);
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModuleConfiguration')
            ->setPath('somePath')
            ->setVersion('v2.1')
            ->setDescription([
                'de' => 'ja',
                'en' => 'no',
            ]);

        $setting = new Setting();
        $setting
            ->setName('test')
            ->setValue([1, 2])
            ->setType('aarr')
            ->setGroupName('group')
            ->setPositionInGroup(7)
            ->setConstraints([1, 2]);

        $moduleConfiguration
            ->addController(
                new Controller(
                    'originalClassNamespace',
                    'moduleClassNamespace'
                )
            )->addController(
                new Controller(
                    'otherOriginalClassNamespace',
                    'moduleClassNamespace'
                )
            )
            ->addTemplate(new Template('originalTemplate', 'moduleTemplate'))
            ->addTemplate(new Template('otherOriginalTemplate', 'moduleTemplate'))
            ->addSmartyPluginDirectory(
                new SmartyPluginDirectory(
                    'firstSmartyDirectory'
                )
            )->addSmartyPluginDirectory(
                new SmartyPluginDirectory(
                    'secondSmartyDirectory'
                )
            )
            ->addTemplateBlock($templateBlock)
            ->addClassExtension(
                new ClassExtension(
                    'originalClassNamespace',
                    'moduleClassNamespace'
                )
            )
            ->addClassExtension(
                new ClassExtension(
                    'otherOriginalClassNamespace',
                    'moduleClassNamespace'
                )
            )
            ->addModuleSetting(
                $setting
            )
            ->addEvent(new Event('onActivate', 'ModuleClass::onActivate'))
            ->addEvent(new Event('onDeactivate', 'ModuleClass::onDeactivate'));

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

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addShopConfiguration(1, $shopConfiguration);
        $projectConfiguration->addShopConfiguration(2, $shopConfiguration);

        return $projectConfiguration;
    }

    private function getContainer()
    {
        $container = (new TestContainerFactory())->create();
        $container->compile();

        return $container;
    }
}
