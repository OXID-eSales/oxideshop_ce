<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIServiceWrapper;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleServicesActivationService;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleServicesActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule\OtherService;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule\TestEventSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ModuleServicesActivationServiceTest extends TestCase
{
    private $testModuleId = 'testModuleId';

    private $testModuleDirectory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule';

    /**
     * @var ProjectYamlDaoInterface | MockObject
     */
    private $projectYamlDao;

    /**
     * @var ModuleServicesActivationServiceInterface
     */
    private $shopActivationService;

    private $projectYamlArray = [];

    public function setUp()
    {
        $this->projectYamlDao = $this->getMockBuilder(ProjectYamlDaoInterface::class)->getMock();
        $this->projectYamlDao
            ->method('saveProjectConfigFile')
            ->willReturnCallback([$this, 'saveProjectYaml']);

        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('getModuleFullPath')
            ->willReturn($this->testModuleDirectory);

        $this->shopActivationService = new ModuleServicesActivationService($this->projectYamlDao, $shopAdapter);
    }

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
                'otherService'          => ['class' => OtherService::class],
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
                'otherService'          => ['class' => OtherService::class],
            ],
        ]);

        $this->projectYamlDao->method('loadProjectConfigFile')->willReturn($projectConfig);
        $this->projectYamlDao->method('loadDIConfigFile')->willReturn($moduleConfig);

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
                'otherService'          => ['class' => OtherService::class],
            ]
        ]);

        $this->projectYamlDao->method('loadProjectConfigFile')->willReturn($projectConfig);
        $this->projectYamlDao->method('loadDIConfigFile')->willReturn($moduleConfig);

        $this->shopActivationService->deactivateModuleServices($this->testModuleId, 1);
        $this->shopActivationService->deactivateModuleServices($this->testModuleId, 5);

        $this->assertArrayHasKey('imports', $this->projectYamlArray);
        $this->assertArrayNotHasKey('services', $this->projectYamlArray);
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
