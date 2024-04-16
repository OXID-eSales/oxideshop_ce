<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\NoServiceYamlException;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ProjectYamlImportService;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ProjectYamlImportServiceTest extends TestCase
{
    /**
     * @var ProjectYamlDaoInterface|MockObject
     */
    private MockObject $dao;

    private ProjectYamlImportService $service;

    private $savedArray;

    public function setup(): void
    {
        $this->dao = $this->getMockBuilder(ProjectYamlDaoInterface::class)
            ->onlyMethods(['loadProjectConfigFile', 'saveProjectConfigFile', 'loadDIConfigFile'])->getMock();
        $this->savedArray = [];
        $this->dao->method('saveProjectConfigFile')->willReturnCallback(function (DIConfigWrapper $config): void {
            $this->getConfigWrapper($config);
        });

        $context = $this->getMockBuilder(BasicContextInterface::class)->getMock();
        $context->method('getGeneratedServicesFilePath')->willReturn(__DIR__);
        $this->service = new ProjectYamlImportService($this->dao, $context);
    }

    public function getConfigWrapper(DIConfigWrapper $config): void
    {
        $this->savedArray = $config->getConfigAsArray();
    }

    public function testAddImport(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->service->addImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule1');
        $resource = $this->savedArray['imports'][0]['resource'];
        $this->assertStringEndsWith(
            'TestModule1/services.yaml',
            $resource
        );
    }

    public function testAddImportSeveralTimes(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->service->addImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule1');
        $this->service->addImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule2');
        $this->service->addImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule1');
        $this->assertEquals(2, count($this->savedArray['imports']));
    }

    public function testRemoveImport(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->service->addImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule1');
        $this->service->addImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule2');
        $this->service->removeImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule1');
        $resource = $this->savedArray['imports'][0]['resource'];
        $this->assertEquals(1, count($this->savedArray['imports']));
        $this->assertStringEndsWith(
            'TestModule2/services.yaml',
            $resource
        );
    }

    public function testRemoveAllImports(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->service->addImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule1');
        $this->service->addImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule2');
        $this->service->removeImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule1');
        $this->service->removeImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule2');
        $this->assertEquals([], $this->savedArray);
    }

    public function testAddNonExistingDirectory(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->expectException(NoServiceYamlException::class);
        $this->service->addImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule3');
    }

    public function testAddNonExistingServiceYaml(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->expectException(NoServiceYamlException::class);
        $this->service->addImport(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Dao');
    }

    public function testRemovingNonExistingImports(): void
    {
        $existingImport = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule1' .
                          DIRECTORY_SEPARATOR . 'services.yaml');
        $nonexistingImport = 'some' . DIRECTORY_SEPARATOR . 'not' . DIRECTORY_SEPARATOR . 'existing' .
                             DIRECTORY_SEPARATOR . 'directory' . DIRECTORY_SEPARATOR . 'services.yaml';
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper(
            ['imports' => [
                ['resource' => $nonexistingImport],
                ['resource' => $existingImport]]]
        ));
        $this->service->removeNonExistingImports();

        $this->assertArrayHasKey('imports', $this->savedArray);
        $this->assertEquals(1, count($this->savedArray['imports']));
        $this->assertEquals($existingImport, $this->savedArray['imports'][0]['resource']);
    }
}
