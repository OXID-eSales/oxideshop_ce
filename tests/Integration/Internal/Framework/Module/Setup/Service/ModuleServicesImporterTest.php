<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\NoServiceYamlException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleServicesImporterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Yaml\Yaml;
use Webmozart\PathUtil\Path;

final class ModuleServicesImporterTest extends IntegrationTestCase
{
    use ContainerTrait;

    private int $shopId = 1;

    public function testImport(): void
    {
        $moduleDirectory = Path::join(__DIR__, 'Fixtures', 'Module');

        $importer = $this->get(ModuleServicesImporterInterface::class);
        $importer->addImport($moduleDirectory, 1);

        $expectedImport = Path::makeRelative(
            Path::join($moduleDirectory, 'services.yaml'),
            Path::getDirectory($this->getActiveModuleServicesFilePath())
        );

        $this->assertEquals(
            [$expectedImport],
            $this->getDIConfigWrapper()->getImportFileNames()
        );

        $importer->removeImport($moduleDirectory, 1);

        $this->assertEquals(
            [],
            $this->getDIConfigWrapper()->getImportFileNames()
        );
    }

    public function testImportWithNoServicesFileInDirectory(): void
    {
        $moduleDirectory = Path::join(__DIR__, 'Fixtures');

        $importer = $this->get(ModuleServicesImporterInterface::class);

        $this->expectException(NoServiceYamlException::class);
        $importer->addImport($moduleDirectory, 1);
    }

    private function getDIConfigWrapper(): DIConfigWrapper
    {
        return new DIConfigWrapper(
            Yaml::parse(file_get_contents($this->getActiveModuleServicesFilePath()), Yaml::PARSE_CUSTOM_TAGS)
        );
    }

    private function getActiveModuleServicesFilePath(): string
    {
        return $this->get(ContextInterface::class)->getActiveModuleServicesFilePath($this->shopId);
    }
}
