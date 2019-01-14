<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapter;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\Chain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\TestData\TestModule\ModuleEvents;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\TestData\TestModule\SomeModuleService;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
class ModuleActivationServiceTest extends TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;
    private $shopId = 1;
    private $environment = 'prod';
    private $testModuleId = 'testModuleId';

    public function setUp()
    {
        $this->container = $this->setupAndConfigureContainer();

        parent::setUp();
    }

    public function testActivation()
    {
        $this->persistModuleConfiguration($this->getTestModuleConfiguration());

        $moduleStateService = $this->container->get(ModuleStateServiceInterface::class);
        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);

        $moduleActivationService->activate($this->testModuleId, $this->shopId);

        $this->assertTrue($moduleStateService->isActive($this->testModuleId, $this->shopId));

        $moduleActivationService->deactivate($this->testModuleId, $this->shopId);

        $this->assertFalse($moduleStateService->isActive($this->testModuleId, $this->shopId));
    }

    public function testSetAutoActiveInModuleConfiguration()
    {
        $this->persistModuleConfiguration($this->getTestModuleConfiguration());

        $moduleConfigurationDao = $this->container->get(ModuleConfigurationDaoInterface::class);
        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);

        $moduleActivationService->activate($this->testModuleId, $this->shopId);
        $moduleConfiguration = $moduleConfigurationDao->get($this->testModuleId, $this->shopId);

        $this->assertTrue($moduleConfiguration->isAutoActive());

        $moduleActivationService->deactivate($this->testModuleId, $this->shopId);
        $moduleConfiguration = $moduleConfigurationDao->get($this->testModuleId, $this->shopId);

        $this->assertFalse($moduleConfiguration->isAutoActive());
    }

    public function testActivationEventWasExecuted()
    {
        $moduleConfiguration = $this->getTestModuleConfiguration();
        $moduleConfiguration->addSetting(new ModuleSetting(
            ModuleSetting::EVENTS,
            [
                'onActivate'    => ModuleEvents::class . '::onActivate',
                'onDeactivate'  => ModuleEvents::class . '::onDeactivate',
            ]
        ));

        $this->persistModuleConfiguration($moduleConfiguration);

        /** @var ModuleActivationServiceInterface $moduleActivationService */
        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);

        ob_start();
        $moduleActivationService->activate($this->testModuleId, $this->shopId);
        $eventMessage = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Method onActivate was called', $eventMessage);
    }

    public function testDeactivationEventWasExecuted()
    {
        $moduleConfiguration = $this->getTestModuleConfiguration();
        $moduleConfiguration->addSetting(new ModuleSetting(
            ModuleSetting::EVENTS,
            [
                'onActivate'    => ModuleEvents::class . '::onActivate',
                'onDeactivate'  => ModuleEvents::class . '::onDeactivate',
            ]
        ));

        $this->persistModuleConfiguration($moduleConfiguration);

        /** @var ModuleActivationServiceInterface $moduleActivationService */
        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);

        ob_start();
        $moduleActivationService->deactivate($this->testModuleId, $this->shopId);
        $eventMessage = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Method onDeactivate was called', $eventMessage);
    }

    public function testClassExtensionChainUpdate()
    {
        $shopConfigurationSettingDao = $this->container->get(ShopConfigurationSettingDaoInterface::class);

        $moduleConfiguration = $this->getTestModuleConfiguration();
        $moduleConfiguration->addSetting(new ModuleSetting(
            ModuleSetting::CLASS_EXTENSIONS,
            [
                'originalClassNamespace' => 'moduleClassNamespace',
            ]
        ));

        $this->persistModuleConfiguration($moduleConfiguration);

        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);
        $moduleActivationService->activate($this->testModuleId, $this->shopId);

        $moduleClassExtensionChain = $shopConfigurationSettingDao->get(
            ShopConfigurationSetting::MODULE_CLASS_EXTENSIONS_CHAIN,
            $this->shopId
        );

        $this->assertSame(
            ['originalClassNamespace' => 'moduleClassNamespace'],
            $moduleClassExtensionChain->getValue()
        );

        $moduleActivationService->deactivate($this->testModuleId, $this->shopId);

        $moduleClassExtensionChain = $shopConfigurationSettingDao->get(
            ShopConfigurationSetting::MODULE_CLASS_EXTENSIONS_CHAIN,
            $this->shopId
        );

        $this->assertSame(
            [],
            $moduleClassExtensionChain->getValue()
        );
    }

    public function testActivationOfModuleServices()
    {

        $moduleConfiguration = $this->getTestModuleConfiguration();
        $this->persistModuleConfiguration($moduleConfiguration);

        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);
        $moduleActivationService->activate($this->testModuleId, $this->shopId);

        $this->assertInstanceOf(
            SomeModuleService::class,
            $this->setupAndConfigureContainer()->get(SomeModuleService::class)
        );
    }

    /**
     * @return ShopAdapterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getShopAdapterMock()
    {
        $shopAdapter = $this
            ->getMockBuilder(ShopAdapter::class)
            ->setMethods(['getModuleFullPath'])
            ->getMock();

        $shopAdapter
            ->method('getModuleFullPath')
            ->willReturn(__DIR__ . '/../../TestData/TestModule');

        return $shopAdapter;
    }

    private function getTestModuleConfiguration(): ModuleConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId($this->testModuleId);

        $moduleConfiguration->addSetting(
            new ModuleSetting(ModuleSetting::PATH, 'TestModule')
        );
        /**
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
        */
        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     */
    private function persistModuleConfiguration(ModuleConfiguration $moduleConfiguration)
    {
        $chain = new Chain();
        $chain
            ->setName(Chain::CLASS_EXTENSIONS)
            ->setChain([
                'originalClassNamespace' => ['moduleClassNamespace'],
            ]);

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addChain($chain);
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $environmentConfiguration = new EnvironmentConfiguration();
        $environmentConfiguration->addShopConfiguration($this->shopId, $shopConfiguration);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addEnvironmentConfiguration($this->environment, $environmentConfiguration);

        $projectConfigurationDao = $this->container->get(ProjectConfigurationDaoInterface::class);
        $projectConfigurationDao->persistConfiguration($projectConfiguration);
    }

    /**
     * We need to replace services in the container with a mock
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function setupAndConfigureContainer()
    {
        $container = (new TestContainerFactory())->create();

        $container->set(ShopAdapterInterface::class, $this->getShopAdapterMock());
        $container->autowire(ShopAdapterInterface::class, ShopAdapter::class);

        $container->compile();

        return $container;
    }
}
