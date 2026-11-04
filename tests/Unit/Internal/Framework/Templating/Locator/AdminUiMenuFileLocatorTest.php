<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating\Locator;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\AdminUiPathProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\AdminUiMenuFileLocator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class AdminUiMenuFileLocatorTest extends TestCase
{
    public function testLocateReturnsMenuFileOfAdminUi(): void
    {
        $adminUiDirectory = vfsStream::setup('adminUi', null, ['menu.xml' => 'menu'])->url();

        $this->assertSame(
            ["$adminUiDirectory/menu.xml"],
            $this->createLocator($adminUiDirectory)->locate()
        );
    }

    public function testLocateReturnsNothingWithoutMenuFile(): void
    {
        $adminUiDirectory = vfsStream::setup('adminUi')->url();

        $this->assertSame([], $this->createLocator($adminUiDirectory)->locate());
    }

    private function createLocator(string $adminUiDirectory): AdminUiMenuFileLocator
    {
        $adminUiPathProvider = $this->createStub(AdminUiPathProviderInterface::class);
        $adminUiPathProvider->method('getAbsolutePath')->willReturn($adminUiDirectory);

        return new AdminUiMenuFileLocator($adminUiPathProvider, new Filesystem());
    }
}
