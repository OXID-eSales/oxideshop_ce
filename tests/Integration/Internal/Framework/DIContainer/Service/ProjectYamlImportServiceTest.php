<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\DIContainer\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\NoServiceYamlException;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ProjectYamlImportService;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ProjectYamlImportServiceInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class ProjectYamlImportServiceTest extends TestCase
{
    private string $generatedServicesFile = __DIR__ . '/Fixtures/generated_services.yaml';

    public function testAddImport(): void
    {
        $this->getImportService()->addImport($this->getFixturePath('module-1'));

        $imports = $this->getDao()->loadProjectConfigFile()->getImportFileNames();
        $this->assertStringEndsWith(
            'module-1/services.yaml',
            $imports[0],
        );
    }

    public function testAddImportSeveralTimes(): void
    {
        $service = $this->getImportService();
        $service->addImport($this->getFixturePath('module-1'));
        $service->addImport($this->getFixturePath('module-2'));
        $service->addImport($this->getFixturePath('module-1'));

        $imports = $this->getDao()->loadProjectConfigFile()->getImportFileNames();
        $this->assertCount(2, $imports);
    }

    public function testRemoveImport(): void
    {
        $service = $this->getImportService();
        $service->addImport($this->getFixturePath('module-1'));
        $service->addImport($this->getFixturePath('module-2'));
        $service->removeImport($this->getFixturePath('module-1'));

        $imports = $this->getDao()->loadProjectConfigFile()->getImportFileNames();
        $this->assertCount(1, $imports);
        $this->assertStringEndsWith(
            'module-2/services.yaml',
            $imports[0]
        );
    }

    public function testRemoveAllImports(): void
    {
        $service = $this->getImportService();
        $service->addImport($this->getFixturePath('module-1'));
        $service->addImport($this->getFixturePath('module-2'));
        $service->removeImport($this->getFixturePath('module-1'));
        $service->removeImport($this->getFixturePath('module-2'));

        $imports = $this->getDao()->loadProjectConfigFile()->getImportFileNames();
        $this->assertEmpty($imports);
    }

    public function testAddNonExistingDirectory(): void
    {
        $this->expectException(NoServiceYamlException::class);

        $this->getImportService()->addImport($this->getFixturePath('some-missing-directory'));
    }

    public function testAddNonExistingServiceYaml(): void
    {
        $this->expectException(NoServiceYamlException::class);

        $this->getImportService()->addImport($this->getFixturePath('module-1/missing-services.yaml'));
    }

    public function testRemovingNonExistingImports(): void
    {
        $path = 'module-1/services.yaml';
        $existingImport = $this->getFixturePath($path);
        $configuration = [
            'imports' => [
                ['resource' => 'some/non/existing/directory/services.yaml'],
                ['resource' => $existingImport]]
        ];
        $this->getDao()->saveProjectConfigFile(
            new DIConfigWrapper($configuration)
        );

        $this->getImportService()->removeNonExistingImports();

        $imports = $this->getDao()->loadProjectConfigFile()->getImportFileNames();
        $this->assertCount(1, $imports);
        $this->assertEquals($imports[0], $path);
    }

    private function getFixturePath(string $dir): string
    {
        return Path::join(__DIR__, 'Fixtures', $dir);
    }

    private function getImportService(): ProjectYamlImportServiceInterface
    {
        return new ProjectYamlImportService($this->getDao(), $this->getContextStub());
    }

    private function getContextStub(): BasicContextStub
    {
        $context = new BasicContextStub();
        $context->setGeneratedServicesFilePath($this->generatedServicesFile);
        return $context;
    }

    private function getDao(): ProjectYamlDao
    {
        return new ProjectYamlDao($this->getContextStub(), (new Filesystem()));
    }
}
