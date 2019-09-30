<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\SystemServiceOverwriteException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ContainerStub implements ContainerInterface
{
    public function get($key)
    {
        return null;
    }
    public function has($key)
    {
        return $key == 'existing.service';
    }
}

class DIConfigWrapperTest extends TestCase
{
    private $servicePath1;
    private $servicePath2;

    public function setUp()
    {
        $this->servicePath1 = __DIR__ . DIRECTORY_SEPARATOR .
                       '..' . DIRECTORY_SEPARATOR .
                       'TestModule1' . DIRECTORY_SEPARATOR .
                       'services.yaml';
        $this->servicePath2 = __DIR__ . DIRECTORY_SEPARATOR .
                              '..' . DIRECTORY_SEPARATOR .
                              'TestModule2' . DIRECTORY_SEPARATOR .
                              'services.yaml';
    }

    public function testCleaningSections()
    {
        $projectYaml = new DIConfigWrapper(['imports' => [], 'services' => []]);
        // These empty sections should be cleaned away
        $this->assertCount(0, $projectYaml->getConfigAsArray());
    }

    public function testGetAllImportFileNames()
    {
        $configArray = ['imports' => [['resource' => realpath($this->servicePath1)],
                                      ['resource' => realpath($this->servicePath2)]]];

        $wrapper = new DIConfigWrapper($configArray);
        $names = $wrapper->getImportFileNames();

        $this->assertEquals(realpath($this->servicePath1), $names[0]);
        $this->assertEquals(realpath($this->servicePath2), $names[1]);
    }

    public function testAddImport()
    {
        $configArray = ['imports' => [['resource' => realpath($this->servicePath1)]]];

        $wrapper = new DIConfigWrapper($configArray);
        $wrapper->addImport($this->servicePath2);

        $this->assertEquals(2, count($wrapper->getConfigAsArray()['imports']));
    }

    public function testAddFirstImport()
    {
        $configArray = [];

        $wrapper = new DIConfigWrapper($configArray);
        $wrapper->addImport($this->servicePath1);

        $expected = ['imports' => [['resource' => realpath($this->servicePath1)]]];
        $this->assertEquals($expected, $wrapper->getConfigAsArray());
    }

    public function testRemoveImport()
    {
        $configArray = ['imports' => [['resource' => realpath($this->servicePath1)],
                                      ['resource' => realpath($this->servicePath2)]]];

        $wrapper = new DIConfigWrapper($configArray);
        $wrapper->removeImport($this->servicePath1);

        $this->assertEquals(1, count($wrapper->getConfigAsArray()['imports']));
    }

    public function testRemoveLastImport()
    {
        $configArray = ['imports' => [['resource' => realpath($this->servicePath1)]]];

        $wrapper = new DIConfigWrapper($configArray);
        $wrapper->removeImport($this->servicePath1);

        $this->assertEquals([], $wrapper->getConfigAsArray());
    }

    public function testActivateServicesForShop()
    {
        $projectYaml = new DIConfigWrapper(['services' =>
                                                ['testmodulesubscriber' =>
                                                     ['class' => 'OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule\TestEventSubscriber']]]);
        $service = $projectYaml->getService('testmodulesubscriber');
        $activeShops = $service->addActiveShops([1, 5]);
        $projectYaml->addOrUpdateService($service);

        $yamlArray = $projectYaml->getConfigAsArray();

        $this->assertEquals([1, 5], $yamlArray['services']['testmodulesubscriber']['calls'][0]['arguments'][0]);
        $this->assertEquals([1, 5], $activeShops);
    }

    public function testRemovingActiveShops()
    {
        $projectYaml = new DIConfigWrapper(['services' =>
                                                ['testmodulesubscriber' =>
                                                     ['class' => 'OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule\TestEventSubscriber',
                                                      'calls' => [['method' => 'setActiveShops', 'arguments' => [[1, 5, 7]]]]]]]);

        $service = $projectYaml->getService('testmodulesubscriber');
        $activeShops = $service->removeActiveShops([1, 5]);
        $projectYaml->addOrUpdateService($service);

        $yamlArray = $projectYaml->getConfigAsArray();
        $this->assertEquals([7], $yamlArray['services']['testmodulesubscriber']['calls'][0]['arguments'][0]);
        $this->assertEquals([7], $activeShops);
    }

    public function testGetServices()
    {
        $projectYaml = new DIConfigWrapper(['services' =>
                                                ['testmodulesubscriber' =>
                                                     ['class' => 'OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule\TestEventSubscriber',
                                                      'calls' => [['method' => 'setActiveShops', 'arguments' => [[1, 5, 7]]]]]]]);

        $services = $projectYaml->getServices();
        $this->assertCount(1, $services);
        $this->assertEquals('testmodulesubscriber', $services[0]->getKey());
    }


    public function testCleaningUncalledServices()
    {
        $projectYaml = new DIConfigWrapper(['services' =>
                                                ['testmodulesubscriber' =>
                                                     ['class' => 'OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule\TestEventSubscriber',
                                                      'calls' => [['method' => 'setActiveShops', 'arguments' => [[1]]]]]]]);
        $service = $projectYaml->getService('testmodulesubscriber');
        $service->removeActiveShops([1]);
        $projectYaml->addOrUpdateService($service);
        // services section should be cleaned away after removeal of service
        $this->assertCount(0, $projectYaml->getConfigAsArray());
    }

    public function testSystemServiceCheckSucceeding()
    {
        $config = new DIConfigWrapper(['services' => ['nonexisting.service' => []]]);
        try {
            $config->checkServices(new ContainerStub());
        } catch (SystemServiceOverwriteException $e) {
            $this->fail('There should no exception been raised!');
        }
        // This is for php unit that is too stupid to recognize the above construct as test
        $this->assertTrue(true);
    }

    public function testSystemServiceCheckFailing()
    {
        $this->expectException(SystemServiceOverwriteException::class);
        $config = new DIConfigWrapper(['services' => ['existing.service' => []]]);
        $config->checkServices(new ContainerStub());
    }

    public function testServiceClassCheckWorking()
    {
        $servicesYaml = new DIConfigWrapper(['services' =>
                                                ['testmodulesubscriber' =>
                                                     ['class' => 'OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule\TestEventSubscriber']]]);


        $this->assertTrue($servicesYaml->checkServiceClassesCanBeLoaded());
    }

    public function testServiceClassCheckFailing()
    {
        $servicesYaml = new DIConfigWrapper(['services' =>
                                                 ['testmodulesubscriber' =>
                                                      ['class' => 'OxidEsales\EshopCommunity\Tests\SomeNotExistingClass']]]);


        $this->assertFalse($servicesYaml->checkServiceClassesCanBeLoaded());
    }
}
