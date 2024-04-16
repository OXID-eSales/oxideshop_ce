<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolver;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ModuleConfigurationValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\TestData\TestModule\SomeModuleService;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

final class ModuleActivationServiceTest extends IntegrationTestCase
{
    use ProphecyTrait;

    private ContainerInterface $container;
    private int $shopId = 1;
    private string $testModuleId = 'testModuleId';
    private ?TestContainerFactory $testContainerFactory = null;
    private string $testModulePath = __DIR__ . '/../../TestData/TestModule';

    public function setup(): void
    {
        parent::setUp();
        $this->container = $this->setupAndConfigureContainer();
        $this->persistModuleConfiguration($this->getTestModuleConfiguration());
    }

    public function tearDown(): void
    {
        ContainerFacade::get(ModuleInstallerInterface::class)->uninstall(
            new OxidEshopPackage($this->testModulePath)
        );
        parent::tearDown();
    }

    public function testActivation()
    {
        $moduleStateService = $this->container->get(ModuleStateServiceInterface::class);
        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);

        $moduleActivationService->activate($this->testModuleId, $this->shopId);

        $this->assertTrue($moduleStateService->isActive($this->testModuleId, $this->shopId));

        $moduleActivationService->deactivate($this->testModuleId, $this->shopId);

        $this->assertFalse($moduleStateService->isActive($this->testModuleId, $this->shopId));
    }

    public function testSetActivatedInModuleConfiguration()
    {
        $moduleConfigurationDao = $this->container->get(ModuleConfigurationDaoInterface::class);
        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);

        $moduleActivationService->activate($this->testModuleId, $this->shopId);
        $moduleConfiguration = $moduleConfigurationDao->get($this->testModuleId, $this->shopId);

        $this->assertTrue($moduleConfiguration->isActivated());

        $moduleActivationService->deactivate($this->testModuleId, $this->shopId);
        $moduleConfiguration = $moduleConfigurationDao->get($this->testModuleId, $this->shopId);

        $this->assertFalse($moduleConfiguration->isActivated());
    }

    public function testActivationOfModuleServices()
    {
        $moduleActivationService = $this->container->get(ModuleActivationServiceInterface::class);
        $moduleActivationService->activate($this->testModuleId, $this->shopId);

        $this->assertInstanceOf(
            SomeModuleService::class,
            $this->setupAndConfigureContainer()->get(SomeModuleService::class)
        );
    }

    public function testDeActivationOfModuleServices(): void
    {
        ContainerFacade::get(ModuleInstallerInterface::class)
            ->install(
                new OxidEshopPackage($this->testModulePath)
            );

        ContainerFacade::get(ModuleActivationBridgeInterface::class)
            ->activate('test-module', $this->shopId);

        ContainerFactory::resetContainer();

        ContainerFacade::get(SomeModuleService::class);

        ContainerFacade::get(ModuleActivationBridgeInterface::class)
            ->deactivate('test-module', $this->shopId);

        ContainerFactory::resetContainer();

        $this->expectException(ServiceNotFoundException::class);
        ContainerFacade::get(SomeModuleService::class);
    }

    public function testActivationWillCallValidatorsAggregate(): void
    {
        $controllersValidator = $this->prophesize(ModuleConfigurationValidatorInterface::class);
        $classExtensionsValidator = $this->prophesize(ModuleConfigurationValidatorInterface::class);
        $eventsValidator = $this->prophesize(ModuleConfigurationValidatorInterface::class);
        $servicesValidator = $this->prophesize(ModuleConfigurationValidatorInterface::class);

        $container = $this->testContainerFactory->create();
        $container->set(
            'oxid_esales.module.setup.validator.controllers_module_setting_validator',
            $controllersValidator->reveal()
        );
        $container->set(
            'oxid_esales.module.setup.validator.class_extensions_module_setting_validator',
            $classExtensionsValidator->reveal()
        );
        $container->set(
            'oxid_esales.module.setup.validator.events_module_setting_validator',
            $eventsValidator->reveal()
        );
        $container->set(
            'oxid_esales.module.setup.validator.services_yaml_validator',
            $servicesValidator->reveal()
        );
        $container->compile();

        $container->get(ModuleActivationServiceInterface::class)
            ->activate($this->testModuleId, $this->shopId);

        $controllersValidator->validate(Argument::type(ModuleConfiguration::class), $this->shopId)
            ->shouldHaveBeenCalledOnce();
        $classExtensionsValidator->validate(Argument::type(ModuleConfiguration::class), $this->shopId)
            ->shouldHaveBeenCalledOnce();
        $eventsValidator->validate(Argument::type(ModuleConfiguration::class), $this->shopId)
            ->shouldHaveBeenCalledOnce();
        $servicesValidator->validate(Argument::type(ModuleConfiguration::class), $this->shopId)
            ->shouldHaveBeenCalledOnce();
    }

    private function getTestModuleConfiguration(): ModuleConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId($this->testModuleId);
        $moduleConfiguration->setModuleSource('test');

        $setting = new Setting();
        $setting
            ->setName('test')
            ->setValue([1, 2])
            ->setType('aarr')
            ->setGroupName('group')
            ->setPositionInGroup(7)
            ->setConstraints([1, 2]);

        $moduleConfiguration->addModuleSetting($setting);

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
            );

        $setting = new Setting();
        $setting
            ->setName('grid')
            ->setValue('row')
            ->setType('str')
            ->setGroupName('frontend');
        $moduleConfiguration->addModuleSetting($setting);

        $setting = new Setting();
        $setting
            ->setName('array')
            ->setValue(['1', '2'])
            ->setType('arr')
            ->setGroupName('frontend');
        $moduleConfiguration->addModuleSetting($setting);

        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     */
    private function persistModuleConfiguration(ModuleConfiguration $moduleConfiguration)
    {
        $chain = new ClassExtensionsChain();
        $chain->setChain([
            'originalClassNamespace' => ['moduleClassNamespace'],
        ]);

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->setClassExtensionsChain($chain);
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $shopConfigurationDao = $this->container->get(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao->save($shopConfiguration, $this->shopId);
    }

    /**
     * We need to replace services in the container with a mock
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function setupAndConfigureContainer()
    {
        if ($this->testContainerFactory === null) {
            $this->testContainerFactory = new TestContainerFactory();
        }
        $container = $this->testContainerFactory->create();

        $modulePathResolver = $this->prophesize(ModulePathResolverInterface::class);
        $modulePathResolver->getFullModulePathFromConfiguration($this->testModuleId, $this->shopId)
            ->willReturn(__DIR__ . '/../../TestData/TestModule');
        $container->set(ModulePathResolverInterface::class, $modulePathResolver->reveal());
        $container->autowire(ModulePathResolverInterface::class, ModulePathResolver::class);

        $container->compile();

        return $container;
    }
}
