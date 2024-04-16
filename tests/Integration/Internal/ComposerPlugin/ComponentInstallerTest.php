<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ComposerPlugin;

use Composer\IO\NullIO;
use Composer\Package\Package;
use OxidEsales\ComposerPlugin\Installer\Package\ComponentInstaller;
use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ProjectYamlImportServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\TestCase;

final class ComponentInstallerTest extends TestCase
{
    use ContainerTrait;

    private string $servicesFilePath = 'Fixtures/services.yaml';

    public function tearDown(): void
    {
        parent::tearDown();

        $this->removeGeneratedLineFromProjectFile();
    }

    public function testInstall(): void
    {
        $installer = $this->createInstaller();
        $installer->install(__DIR__ . '/Fixtures');

        $this->assertTrue($this->doesServiceLineExists());
    }

    public function testUpdate(): void
    {
        $installer = $this->createInstaller();
        $installer->update(__DIR__ . '/Fixtures');

        $this->assertTrue($this->doesServiceLineExists());
    }

    private function createInstaller(): ComponentInstaller
    {
        $packageStub = $this->getMockBuilder(Package::class)->disableOriginalConstructor()->getMock();

        return new ComponentInstaller(
            new NullIO(),
            (new Facts())->getShopRootPath(),
            $packageStub
        );
    }

    private function doesServiceLineExists(): bool
    {
        $context = BootstrapContainerFactory::getBootstrapContainer()->get(BasicContextInterface::class);
        $contentsOfProjectFile = file_get_contents(
            $context->getGeneratedServicesFilePath()
        );

        return (bool)strpos($contentsOfProjectFile, $this->servicesFilePath);
    }

    private function removeGeneratedLineFromProjectFile(): void
    {
        /** @var ProjectYamlImportServiceInterface $projectYamlImportService */
        $projectYamlImportService = $this->get(ProjectYamlImportServiceInterface::class);
        $projectYamlImportService->removeImport(__DIR__ . '/Fixtures');
    }
}
