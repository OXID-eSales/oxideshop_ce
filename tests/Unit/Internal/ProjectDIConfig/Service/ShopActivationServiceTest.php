<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 24.10.18
 * Time: 11:02
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\Service;

use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIServiceWrapper;
use OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Service\ShopActivationService;
use OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Service\ShopActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule\OtherService;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule\TestEventSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ShopActivationServiceTest extends TestCase
{
    /**
     * @var ProjectYamlDaoInterface|MockObject $daoMock ;
     */
    private $daoMock;

    /**
     * @var ShopActivationServiceInterface $service
     */
    private $service;

    private $projectYamlArray;

    public function setUp()
    {
        $this->daoMock = $this->getMockBuilder(ProjectYamlDaoInterface::class)
                ->setMethods(['loadProjectConfigFile', 'saveProjectConfigFile', 'loadDIConfigFile'])
                ->getMock();
        $this->projectYamlArray = [];
        $this->daoMock->method('saveProjectConfigFile')->willReturnCallback([$this, 'saveProjectYaml']);
        $this->service = new ShopActivationService($this->daoMock);

    }

    public function saveProjectYaml(DIConfigWrapper $config)
    {
        $this->projectYamlArray = $config->getConfigAsArray();
    }

    function testActivateServicesForShops()
    {
        $projectConfig = new DIConfigWrapper([]);
        $moduleDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule';
        $shopAwareService = TestEventSubscriber::class;
        $otherService = OtherService::class;
        $moduleConfig = new DIConfigWrapper(['services' => ['testeventsubscriber' => ['class' => $shopAwareService],
                                                            'otherservice' => ['class' => $otherService]]]);

        $this->daoMock->method('loadProjectConfigFile')->willReturn($projectConfig);
        $this->daoMock->method('loadDIConfigFile')->willReturn($moduleConfig);

        $this->service->activateServicesForShops($moduleDir, [1,4, 5]);

        $this->assertArrayHasKey('imports', $this->projectYamlArray);
        $this->assertStringEndsWith('TestModule/services.yaml', $this->projectYamlArray['imports'][0]['resource']);
        $this->assertArrayHasKey('services', $this->projectYamlArray);
        $this->assertEquals([1,4,5], $this->projectYamlArray['services']['testeventsubscriber']['calls'][0]['arguments'][0]);
    }

    function testDeActivateServicesForShops()
    {
        $shopAwareService = TestEventSubscriber::class;
        $otherService = OtherService::class;

        $moduleDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule';
        $projectConfig = new DIConfigWrapper(['imports' => [['resource' => $moduleDir . DIRECTORY_SEPARATOR . 'services.yaml']],
                                              'services' => ['testeventsubscriber' => ['class' => $shopAwareService,
            'calls' => [['method' => 'setActiveShops', 'arguments' => [[1,5]]],
                        ['method' => 'setContext', 'arguments' => [DIServiceWrapper::SET_CONTEXT_PARAMETER]]]]]]);

        $moduleConfig = new DIConfigWrapper(['services' => ['testeventsubscriber' => ['class' => $shopAwareService],
                                                            'otherservice' => ['class' => $otherService]]]);

        $this->daoMock->method('loadProjectConfigFile')->willReturn($projectConfig);
        $this->daoMock->method('loadDIConfigFile')->willReturn($moduleConfig);

        $this->service->deActivateServicesForShops($moduleDir, [1]);

        $this->assertArrayHasKey('imports', $this->projectYamlArray);
        $this->assertStringEndsWith('TestModule/services.yaml', $this->projectYamlArray['imports'][0]['resource']);
        $this->assertArrayHasKey('services', $this->projectYamlArray);
        $this->assertEquals([5], $this->projectYamlArray['services']['testeventsubscriber']['calls'][0]['arguments'][0]);

    }

    function testDeActivateServicesForAllShops()
    {
        $shopAwareService = TestEventSubscriber::class;
        $otherService = OtherService::class;

        $moduleDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule';
        $projectConfig = new DIConfigWrapper(['imports' => [['resource' => realpath($moduleDir . DIRECTORY_SEPARATOR . 'services.yaml')]],
                                              'services' => ['testeventsubscriber' => ['class' => $shopAwareService,
                                                                                       'calls' => [['method' => 'setActiveShops', 'arguments' => [[1,5]]],
                                                                                                   ['method' => 'setContext', 'arguments' => [DIServiceWrapper::SET_CONTEXT_PARAMETER]]]]]]);

        $moduleConfig = new DIConfigWrapper(['services' => ['testeventsubscriber' => ['class' => $shopAwareService],
                                                            'otherservice' => ['class' => $otherService]]]);

        $this->daoMock->method('loadProjectConfigFile')->willReturn($projectConfig);
        $this->daoMock->method('loadDIConfigFile')->willReturn($moduleConfig);

        $this->service->deActivateServicesForShops($moduleDir, [5,1]);

        $this->assertTrue(array_key_exists('imports', $this->projectYamlArray));
        $this->assertFalse(array_key_exists('services', $this->projectYamlArray));
    }
}
