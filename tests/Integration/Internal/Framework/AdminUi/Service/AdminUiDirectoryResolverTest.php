<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\AdminUi\Service;

use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Exception\AdminUiDirectoryNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Provider\AdminUiDirectoryProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service\AdminUiDirectoryResolver;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service\AdminUiDirectoryResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class AdminUiDirectoryResolverTest extends TestCase
{
    use ContainerTrait;

    private string $adminUiDirectory = __DIR__ . '/../Fixtures/adminUi';
    private string $overlayDirectory = __DIR__ . '/../Fixtures/adminUiOverlay';

    public function testGetDirectoriesIsOrderedByDescendingPriority(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/../Fixtures/Priority');
        $this->compileContainer();
        $this->container->set(
            'oxid_esales.tests.admin_ui.directory_provider.high',
            $this->createDirectoryProvider($this->overlayDirectory)
        );
        $this->container->set(
            'oxid_esales.tests.admin_ui.directory_provider.low',
            $this->createDirectoryProvider($this->adminUiDirectory)
        );

        $directories = $this->get(AdminUiDirectoryResolverInterface::class)->getDirectories();

        $this->assertSame($this->overlayDirectory, $directories[0]);
        $this->assertSame($this->getAdminUiPackageDirectory(), $directories[count($directories) - 2]);
        $this->assertSame($this->adminUiDirectory, $directories[count($directories) - 1]);
    }

    public function testGetDirectoriesThrowsForMissingDirectory(): void
    {
        $resolver = $this->createResolver($this->adminUiDirectory, __DIR__ . '/../Fixtures/missing');

        $this->expectException(AdminUiDirectoryNotFoundException::class);

        $resolver->getDirectories();
    }

    private function createResolver(string ...$directories): AdminUiDirectoryResolver
    {
        return new AdminUiDirectoryResolver(
            array_map(fn (string $directory) => $this->createDirectoryProvider($directory), $directories),
            new Filesystem(),
        );
    }

    private function createDirectoryProvider(string $directory): AdminUiDirectoryProviderInterface
    {
        $directoryProvider = $this->createStub(AdminUiDirectoryProviderInterface::class);
        $directoryProvider->method('getDirectory')->willReturn($directory);

        return $directoryProvider;
    }

    private function getAdminUiPackageDirectory(): string
    {
        return Path::join(
            $this->get(BasicContextInterface::class)->getVendorPath(),
            $this->getParameter('oxid_esales.admin_ui.package_name')
        );
    }
}
