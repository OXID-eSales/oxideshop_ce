<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\AdminUi\Service;

use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service\AdminUiDirectoryResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service\AdminUiFileLocator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class AdminUiFileLocatorTest extends TestCase
{
    private string $adminUiDirectory = __DIR__ . '/../Fixtures/adminUi';
    private string $overlayDirectory = __DIR__ . '/../Fixtures/adminUiOverlay';

    public function testLocatePrefersFileOfFirstDirectory(): void
    {
        $this->assertSame(
            [Path::join($this->overlayDirectory, 'menu.xml')],
            $this->createLocator()->locate('menu.xml')
        );
    }

    public function testLocateFallsBackToNextDirectory(): void
    {
        $this->assertSame(
            [Path::join($this->adminUiDirectory, 'tpl/base-only.html.twig')],
            $this->createLocator()->locate('tpl/base-only.html.twig')
        );
    }

    public function testLocateReturnsNothingForUnknownFile(): void
    {
        $this->assertSame([], $this->createLocator()->locate('unknown.xml'));
    }

    private function createLocator(): AdminUiFileLocator
    {
        $directoryResolver = $this->createStub(AdminUiDirectoryResolverInterface::class);
        $directoryResolver->method('getDirectories')
            ->willReturn([$this->overlayDirectory, $this->adminUiDirectory]);

        return new AdminUiFileLocator($directoryResolver, new Filesystem());
    }
}
