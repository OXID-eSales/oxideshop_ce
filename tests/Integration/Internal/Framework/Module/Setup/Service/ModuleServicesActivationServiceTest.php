<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIServiceWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ServicesYamlConfigurationError;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleServicesActivationService;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleServicesActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateService;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\TestData\TestModule\SomeModuleService;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Event\TestEventSubscriber;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ModuleServicesActivationServiceTest extends TestCase
{
    private $testModuleId = 'testModuleId';

    private $testModuleDirectory = __DIR__ . DIRECTORY_SEPARATOR
        . '..' . DIRECTORY_SEPARATOR
        . '..' . DIRECTORY_SEPARATOR
        . 'TestData' . DIRECTORY_SEPARATOR
        . 'TestModule';

    /**
     * @var ProjectYamlDaoInterface | MockObject
     */
    private $projectYamlDao;

    /**
     * @var EventDispatcherInterface | MockObject
     */
    private $eventDispatcher;

    /**
     * @var ModuleServicesActivationServiceInterface
     */
    private $shopActivationService;

    /**
     * @var ContextStub
     */
    private $contextStub;

    private $projectYamlArray = [];

    /** @var ModuleStateService|MockObject */
    private $moduleStateService;

    public function setUp()
    {
        $this->projectYamlDao = $this->getMockBuilder(ProjectYamlDaoInterface::class)->getMock();
        $this->projectYamlDao
            ->method('saveProjectConfigFile')
            ->willReturnCallback([$this, 'saveProjectYaml']);

        /** @var ModulePathResolverInterface|MockObject $modulePathResolver */
        $modulePathResolver = $this->getMockBuilder(ModulePathResolverInterface::class)->getMock();
        $modulePathResolver
            ->method('getFullModulePathFromConfiguration')
            ->willReturn($this->testModuleDirectory);

        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();

        $this->moduleStateService = $this->getMockBuilder(ModuleStateServiceInterface::class)->getMock();

        $this->contextStub = new ContextStub();
        $this->contextStub->setAllShopIds([1,5]);

        $this->shopActivationService = new ModuleServicesActivationService(
            $this->projectYamlDao,
            $this->eventDispatcher,
            $modulePathResolver,
            $this->moduleStateService,
            $this->contextStub
        );
    }

    /** Callback function for mock to catch the given parameter
     *
     * @param DIConfigWrapper $config
     */
    public function saveProjectYaml(DIConfigWrapper $config)
    {
        $this->projectYamlArray = $config->getConfigAsArray();
    }

    public function testActivateServicesForShops()
    {
        $projectConfig = new DIConfigWrapper([]);

        $moduleConfig = new DIConfigWrapper([
            'services' => [
                'testEventSubscriber'   => ['class' => TestEventSubscriber::class],
                'otherService'          => ['class' => SomeModuleService::class],
            ],
        ]);

        $this->projectYamlDao->method('loadProjectConfigFile')->willReturn($projectConfig);
        $this->projectYamlDao->method('loadDIConfigFile')->willReturn($moduleConfig);

        $this->shopActivationService->activateModuleServices($this->testModuleId, 1);
        $this->shopActivationService->activateModuleServices($this->testModuleId, 4);
        $this->shopActivationService->activateModuleServices($this->testModuleId, 5);

        $this->assertProjectYamlHasImport($this->getTestModuleServiceYamlPath());
        $this->assertModuleServiceIsActiveForShops('testEventSubscriber', [1,4,5]);
    }

    public function testDeactivateServicesForShops()
    {
        $shopAwareService = TestEventSubscriber::class;

        $projectConfig = new DIConfigWrapper([
            'imports' => [
                ['resource' => $this->getTestModuleServiceYamlPath()]
            ],
            'services' => [
                'testEventSubscriber' => [
                    'class' => $shopAwareService,
                    'calls' => [
                        [
                            'method' => 'setActiveShops',
                            'arguments' => [[1,5]]
                        ],
                        [
                            'method' => 'setContext',
                            'arguments' => [DIServiceWrapper::SET_CONTEXT_PARAMETER]
                        ]
                    ]
                ]
            ]
        ]);

        $moduleConfig = new DIConfigWrapper([
            'services' => [
                'testEventSubscriber'   => ['class' => $shopAwareService],
                'otherService'          => ['class' => SomeModuleService::class],
            ],
        ]);

        $this->projectYamlDao->method('loadProjectConfigFile')->willReturn($projectConfig);
        $this->projectYamlDao->method('loadDIConfigFile')->willReturn($moduleConfig);

        $this->moduleStateService->method('isActive')->willReturn(true);

        $this->shopActivationService->deactivateModuleServices($this->testModuleId, 1);

        $this->assertProjectYamlHasImport($this->getTestModuleServiceYamlPath());
        $this->assertModuleServiceIsActiveForShops('testEventSubscriber', [5]);
    }

    public function testDeactivateServicesForAllShops()
    {
        $shopAwareService = TestEventSubscriber::class;

        $projectConfig = new DIConfigWrapper([
            'imports' => [
                ['resource' => $this->getTestModuleServiceYamlPath()]
            ],
            'services' => [
                'testEventSubscriber' => [
                    'class' => $shopAwareService,
                    'calls' => [
                        [
                            'method' => 'setActiveShops',
                            'arguments' => [[1,5]]
                        ],
                        [
                            'method' => 'setContext',
                            'arguments' => [DIServiceWrapper::SET_CONTEXT_PARAMETER]
                        ]
                    ]
                ]
            ]
        ]);

        $moduleConfig = new DIConfigWrapper([
            'services' => [
                'testEventSubscriber'   => ['class' => $shopAwareService],
                'otherService'          => ['class' => SomeModuleService::class],
            ]
        ]);

        $this->projectYamlDao->method('loadProjectConfigFile')->willReturn($projectConfig);
        $this->projectYamlDao->method('loadDIConfigFile')->willReturn($moduleConfig);

        $this->moduleStateService->method('isActive')->will($this->onConsecutiveCalls(true, false));

        $this->shopActivationService->deactivateModuleServices($this->testModuleId, 1);
        $this->assertArrayHasKey('imports', $this->projectYamlArray);
        $this->assertArrayHasKey('services', $this->projectYamlArray);

        $this->shopActivationService->deactivateModuleServices($this->testModuleId, 5);
        $this->assertArrayNotHasKey('imports', $this->projectYamlArray);
        $this->assertArrayNotHasKey('services', $this->projectYamlArray);
    }

    public function testDeActivateServicesWithConfigurationError()
    {
        $moduleConfig = new DIConfigWrapper(['services' => ['testeventsubscriber' => ['class' => 'some/not/existing/class'],
                                                            'otherservice' => ['class' => 'also/not/existing/class']]]);

        $this->eventDispatcher->expects($this->once())->method('dispatch');
        $this->projectYamlDao->expects($this->never())->method('loadProjectConfigFile');
        $this->projectYamlDao->method('loadDIConfigFile')->willReturn($moduleConfig);

        $this->shopActivationService->deactivateModuleServices($this->testModuleId, 1);
    }

    public function testActivateServicesWithConfigurationError()
    {
        $this->expectException(ServicesYamlConfigurationError::class);

        $moduleConfig = new DIConfigWrapper(['services' => ['testeventsubscriber' => ['class' => 'some/not/existing/class'],
                                                            'otherservice' => ['class' => 'also/not/existing/class']]]);

        $this->eventDispatcher->expects($this->once())->method('dispatch');
        $this->projectYamlDao->expects($this->never())->method('loadProjectConfigFile');
        $this->projectYamlDao->method('loadDIConfigFile')->willReturn($moduleConfig);

        $this->shopActivationService->activateModuleServices($this->testModuleId, 1);
    }

    private function assertProjectYamlHasImport(string $import)
    {
        $this->assertArrayHasKey('imports', $this->projectYamlArray);
        $this->assertStringEndsWith($import, $this->projectYamlArray['imports'][0]['resource']);
    }

    private function assertModuleServiceIsActiveForShops(string $serviceId, array $shopIds)
    {
        $this->assertArrayHasKey('services', $this->projectYamlArray);
        $this->assertEquals(
            $shopIds,
            $this->projectYamlArray['services'][$serviceId]['calls'][0]['arguments'][0]
        );
    }

    private function getTestModuleServiceYamlPath(): string
    {
        return realpath($this->testModuleDirectory . DIRECTORY_SEPARATOR . 'services.yaml');
    }
}
