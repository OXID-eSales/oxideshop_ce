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
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ProjectYamlImportServiceTest extends TestCase
{
    /**
     * @var ProjectYamlDaoInterface
     */
    private $dao;

    /**
     * @var ProjectYamlImportService
     */
    private $service;

    private $savedArray;

    public function setup(): void
    {
        $this->dao = $this->createStub(ProjectYamlDaoInterface::class);
        $this->savedArray = [];
        $this->dao->method('saveProjectConfigFile')->willReturnCallback([$this, 'getConfigWrapper']);

        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getGeneratedServicesFilePath')->willReturn(__DIR__);
        $this->service = new ProjectYamlImportService($this->dao, $context);
    }

    public function getConfigWrapper(DIConfigWrapper $config)
    {
        $this->savedArray = $config->getConfigAsArray();
    }

    public function testAddImportFromFilePath(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->service->addImportFromFilePath($this->getServiceFilePath('TestModule1'));
        $resource = $this->savedArray['imports'][0]['resource'];
        $this->assertStringEndsWith(
            'TestModule1/services.yaml',
            $resource
        );
    }

    public function testAddImportFromFilePathSeveralTimes(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->service->addImportFromFilePath($this->getServiceFilePath('TestModule1'));
        $this->service->addImportFromFilePath($this->getServiceFilePath('TestModule2'));
        $this->service->addImportFromFilePath($this->getServiceFilePath('TestModule1'));
        $this->assertCount(2, $this->savedArray['imports']);
    }

    public function testRemoveImportFromFilePath(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->service->addImportFromFilePath($this->getServiceFilePath('TestModule1'));
        $this->service->addImportFromFilePath($this->getServiceFilePath('TestModule2'));
        $this->service->removeImportFromFilePath($this->getServiceFilePath('TestModule1'));
        $resource = $this->savedArray['imports'][0]['resource'];
        $this->assertCount(1, $this->savedArray['imports']);
        $this->assertStringEndsWith(
            'TestModule2/services.yaml',
            $resource
        );
    }

    public function testRemoveAllImportsFromFilePath(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->service->addImportFromFilePath($this->getServiceFilePath('TestModule1'));
        $this->service->addImportFromFilePath($this->getServiceFilePath('TestModule2'));
        $this->service->removeImportFromFilePath($this->getServiceFilePath('TestModule1'));
        $this->service->removeImportFromFilePath($this->getServiceFilePath('TestModule2'));
        $this->assertSame([], $this->savedArray);
    }

    public function testAddImportFromNonExistingFilePath(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->expectException(NoServiceYamlException::class);
        $this->service->addImportFromFilePath(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule1' .
            DIRECTORY_SEPARATOR . 'nonexisting.yaml'
        );
    }

    public function testAddImportFromDirectoryPath(): void
    {
        $this->dao->method('loadProjectConfigFile')->willReturn(new DIConfigWrapper([]));
        $this->expectException(NoServiceYamlException::class);
        $this->service->addImportFromFilePath(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestModule1'
        );
    }

    private function getServiceFilePath(string $module): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $module .
            DIRECTORY_SEPARATOR . 'services.yaml';
    }

    public function testRemovingNonExistingImports()
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
